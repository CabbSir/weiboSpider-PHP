<?php


namespace Console\Controller;

use Console\Service\ConfigService;
use Console\Service\ProxyService;
use Console\Service\UserService;
use Console\Service\WeiboContentService;
use Think\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once("Application/Common/Libs/simplehtmldom_1_9_1/simple_html_dom.php");
require 'Application/Common/Libs/phpMailer/Exception.php';
require 'Application/Common/Libs/phpMailer/PHPMailer.php';
require 'Application/Common/Libs/phpMailer/SMTP.php';

class ContentController extends BaseController
{
    /**
     * 首次新增用户时，调用一次获取历史全部微博，每10s翻一页，每个用户仅调用一次
     */
    public function getHistoryWb()
    {
        // 获取userid
        $newUsers = UserService::getInstance()->queryNewUserList();
        // 获取前缀
        $prefix = ConfigService::getInstance()->queryConfig()['context_prefix'];
        foreach ($newUsers as $user) {
            // 拼接第一个url
            $url = "https://m.weibo.cn/api/container/getIndex?type=uid&value={$user}&containerid={$prefix}{$user}";
            // 发送get请求
            $content = json_decode(file_get_contents($url), true);
            if ($content['ok'] != '1') {
                // 代理访问
                $content = $this->proxyRequest($url);
                if ($content === false) {
                    // 代理访问也失败
                    Log::record("访问此url失败，URL地址是{$url}");
                    exit();
                }
            }
            // 一切正常，拼接数据入库
            // 下一页的since_id
            $monthConfig = C('MONTH_CONFIG');
            $sinceId     = $content['data']['cardlistInfo']['since_id'];
            $wbData      = $content['data']['cards'];
            foreach ($wbData as $wb) {
                $arr      = explode(' ', $wb['mblog']['created_at']);
                $datetime = $arr['5'] . '-' . $monthConfig[$arr['1']] . '-' . $arr['2'] . ' ' . $arr['3'];
                // 长文
                if ($wb['mblog']['isLongText'] == 'true') {
                    // 拼接url
                    $html      = file_get_contents("https://m.weibo.cn/status/{$wb['mblog']['mid']}");
                    $wbContent = explode('",', explode('"text": "', $html)[1])[0];
                } else {
                    $wbContent = $wb['mblog']['text'];
                }
                $data = [
                    'mid'           => $wb['mblog']['mid'],
                    'user_id'       => $user,
                    'content'       => $wbContent,
                    'source'        => $wb['mblog']['source'],
                    'retweet_count' => $wb['mblog']['reposts_count'],
                    'comment_count' => $wb['mblog']['comments_count'],
                    'like_count'    => $wb['mblog']['attitudes_count'],
                    'pic_num'       => $wb['mblog']['pic_num'],
                    'pics'          => '',
                    'cdatetime'     => $datetime,
                ];
                // 微博图片
                if ($wb['mblog']['pic_num'] != 0) {
                    $arr = [];
                    foreach ($wb['mblog']['pics'] as $pic) {
                        array_push($arr, $pic['large']['url']);
                    }
                    $data['pics'] = implode(',', $arr);
                }
                // 微博故事 - 3，微博live - 1，视频 - 2
                if ($wb['mblog']['live_photo']) {
                    $arr = [];
                    foreach ($wb['mblog']['live_photo'] as $photo) {
                        array_push($arr, $photo);
                    }
                    $data['media'] = implode(',', $arr);
                    $data['media_category'] = 1;
                }
                if ($wb['mblog']['page_info']['type'] == 'video') {
                    $data['media_category'] = 2;
                    $data['media'] = $wb['mblog']['page_info']['page_url'];
                }
                if ($wb['mblog']['page_info']['type'] == 'story') {
                    $data['media_category'] = 3;
                    $data['media'] = $wb['mblog']['page_info']['page_url'];
                }
                if (WeiboContentService::getInstance()->addOne($data) === false) {
                    Log::record("插入新微博错误，数据如下". implode(',', $data));
                }
            }
            // 下一页
            $this->nextPage($user, $prefix, $sinceId);
        }
    }

    /**
     * 对每个用户账号间隔10分钟扫描一次是否更新微博
     * 如果更新那么调用此方法入库
     */
    public function getUpdate()
    {
        // 不考虑10分钟更新10多条微博这种特殊情况，所以默认访问第一页即可获得最新更新
        // 获取userid
        $oldUsers = UserService::getInstance()->queryOldUserList();
        // 获取前缀
        $prefix = ConfigService::getInstance()->queryConfig()['context_prefix'];
        foreach ($oldUsers as $user) {
            // 查出最后一条微博的新建时间
            $lastWbDatetime = WeiboContentService::getInstance()->queryLastDatetime($user['wbId']);
            // 拼接第一个url
            $url = "https://m.weibo.cn/api/container/getIndex?type=uid&value={$user['wbId']}&containerid={$prefix}{$user['wbId']}";
            // 发送get请求
            $content = json_decode(file_get_contents($url), true);
            if ($content['ok'] != '1') {
                // 代理访问
                $content = $this->proxyRequest($url);
                if ($content === false) {
                    // 代理访问也失败
                    Log::record("访问此url失败，URL地址是{$url}");
                    exit();
                }
            }
            // 一切正常，拼接数据入库
            $monthConfig = C('MONTH_CONFIG');
            $wbData      = $content['data']['cards'];
            foreach ($wbData as $wb) {
                // 判断是否是置顶微博
                if ($wb['mblog']['mblogtype'] == '2') {
                    continue;
                }
                $arr      = explode(' ', $wb['mblog']['created_at']);
                $datetime = $arr['5'] . '-' . $monthConfig[$arr['1']] . '-' . $arr['2'] . ' ' . $arr['3'];
                if (strtotime($datetime) <= strtotime($lastWbDatetime)) {
                    Log::record("用户{$user['wbId']}没有更新微博!");
                    break;
                }
                // 长文
                if ($wb['mblog']['isLongText'] == 'true') {
                    // 拼接url
                    $html      = file_get_contents("https://m.weibo.cn/status/{$wb['mblog']['mid']}");
                    $wbContent = explode('",', explode('"text": "', $html)[1])[0];
                } else {
                    $wbContent = $wb['mblog']['text'];
                }
                $data = [
                    'mid'           => $wb['mblog']['mid'],
                    'user_id'       => $user['wbId'],
                    'content'       => $wbContent,
                    'source'        => $wb['mblog']['source'],
                    'retweet_count' => $wb['mblog']['reposts_count'],
                    'comment_count' => $wb['mblog']['comments_count'],
                    'like_count'    => $wb['mblog']['attitudes_count'],
                    'pic_num'       => $wb['mblog']['pic_num'],
                    'pics'          => '',
                    'cdatetime'     => $datetime,
                ];
                // 微博图片
                if ($wb['mblog']['pic_num'] != 0) {
                    $arr = [];
                    foreach ($wb['mblog']['pics'] as $pic) {
                        array_push($arr, $pic['large']['url']);
                    }
                    $data['pics'] = implode(',', $arr);
                }
                // 微博故事 - 3，微博live - 1，视频 - 2
                if ($wb['mblog']['live_photo']) {
                    $arr = [];
                    foreach ($wb['mblog']['live_photo'] as $photo) {
                        array_push($arr, $photo);
                    }
                    $data['media'] = implode(',', $arr);
                    $data['media_category'] = 1;
                }
                if ($wb['mblog']['page_info']['type'] == 'video') {
                    $data['media_category'] = 2;
                    $data['media'] = $wb['mblog']['page_info']['page_url'];
                }
                if ($wb['mblog']['page_info']['type'] == 'story') {
                    $data['media_category'] = 3;
                    $data['media'] = $wb['mblog']['page_info']['page_url'];
                }
                if (WeiboContentService::getInstance()->addOne($data) === false) {
                    Log::record("插入新微博错误，数据如下". implode(',', $data));
                }
            }
        }
        exit();
    }

    /**
     * 每5分钟扫描一次数据库，如果有变动立即发送邮件
     */
    public function email()
    {
        // 扫描出所有未发送邮件的内容
        $content = WeiboContentService::getInstance()->queryAllUnemail();
        if (!$content) {
            Log::record("所有微博都已发送成功");
            exit();
        }
        foreach ($content as $wb) {
            if ($wb['media'] == '' && $wb['pics'] == '') {
                $media = '该微博没有任何媒体内容';
                $category = '无';
            } elseif ($wb['media']) {
                $media = implode('<br>', explode(',', $wb['media'])) .'<br>'. implode('<br>', explode(',', $wb['pics']));
                if ($wb['mediaCategory'] == 1) {
                    $category = '微博live';
                } elseif ($wb['mediaCategory'] == 2) {
                    $category = '微博视频';
                } else {
                    $category = '微博故事';
                }
            } else {
                $media = implode('<br>', explode(',', $wb['pics']));
                $category = '纯图片';
            }
            $emailContent = "<html>
    <center><h1>您格外关注的<b>{$wb['name']}</b>又发了新微博哦！！！</h1></center>
    <center>
        <table border='1'>
            <tr>
                <td>发出时间</td>
                <td>{$wb['cdatetime']}</td>
            </tr>
            <tr>
                <td>通过什么设备发送的</td>
                <td>{$wb['source']}</td>
            </tr>
            <tr>
                <td>微博文字内容</td>
                <td>{$wb['content']}</td>
            </tr>
            <tr>
                <td>微博媒体内容</td>
                <td>{$media}</td>
            </tr>
            <tr>
                <td>微博类型</td>
                <td>{$category}</td>
            </tr>
        </table>
    </center>
</html>";
            $address = $wb['email'];
            if ($this->sendEmail($emailContent, $address, $wb['name'])) {
                // 更新对应微博状态
                WeiboContentService::getInstance()->updateSendStatus($wb['id']);
            }
        }
    }

    private function sendEmail($content, $addresses, $name)
    {
        foreach (array_filter(explode(',', $addresses)) as $address) {
            $mail = new PHPMailer(true);
            try {
                //服务器配置
                $mail->CharSet ="UTF-8";                     //设定邮件编码
                $mail->SMTPDebug = 0;                        // 调试模式输出
                $mail->isSMTP();                             // 使用SMTP
                $mail->Host = 'smtp.88.com';                // SMTP服务器
                $mail->SMTPAuth = true;                      // 允许 SMTP 认证
                $mail->Username = 'dota2beta';                // SMTP 用户名  即邮箱的用户名
                $mail->Password = 'HCFR76GbUjT8nzJ9';             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
                $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
                $mail->Port = 465;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

                $mail->setFrom('dota2beta@88.com', '您特别关注的新微博提醒');  //发件人
                $mail->addAddress($address, $address);  // 收件人

                //Content
                $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
                $mail->Subject = date('Y-m-d H:i:s'). '，'.$name. '新微博提醒';
                $mail->Body    = $content;
                $mail->AltBody = $name. '发新微博了，赶紧去看';

                $mail->send();
                Log::record('邮件发送成功');
            } catch (Exception $e) {
                Log::record('邮件发送失败: ', $mail->ErrorInfo);
                return false;
            }
        }
        return true;
    }

    private function proxyRequest($url)
    {
        // 获取全部可用proxy
        $proxyList = ProxyService::getInstance()->queryAvailable();
        foreach ($proxyList as $proxy) {
            $context = [
                'http' => [
                    'proxy'           => "tcp://$proxy",
                    'request_fulluri' => true,
                ]
            ];
            $context = stream_context_create($context);
            $result  = json_decode(file_get_contents($url, false, $context), true);
            if ($result['ok'] == '1') {
                return $result;
            }
        }

        return false;
    }

    private function nextPage($user, $prefix, $sinceId)
    {
        // 一页插入完毕等待10s
        sleep(10);
        // 拼接第一个url
        $url = "https://m.weibo.cn/api/container/getIndex?type=uid&value={$user}&containerid={$prefix}{$user}&since_id={$sinceId}";
        // 发送get请求
        $content = json_decode(file_get_contents($url), true);
        if ($content['ok'] != '1') {
            // 代理访问
            $content = $this->proxyRequest($url);
            if ($content === false) {
                // 代理访问也失败
                Log::record("访问此url失败，URL地址是{$url}");
                exit();
            }
        }
        // 一切正常，拼接数据入库
        // 下一页的since_id
        $monthConfig = C('MONTH_CONFIG');
        $sinceId     = $content['data']['cardlistInfo']['since_id'];
        $wbData      = $content['data']['cards'];
        foreach ($wbData as $wb) {
            $arr      = explode(' ', $wb['mblog']['created_at']);
            $datetime = $arr['5'] . '-' . $monthConfig[$arr['1']] . '-' . $arr['2'] . ' ' . $arr['3'];
            // 长文
            if ($wb['mblog']['isLongText'] == 'true') {
                // 拼接url
                $html      = file_get_contents("https://m.weibo.cn/status/{$wb['mblog']['mid']}");
                $wbContent = explode('",', explode('"text": "', $html)[1])[0];
            } else {
                $wbContent = $wb['mblog']['text'];
            }
            $data = [
                'mid'           => $wb['mblog']['mid'],
                'user_id'       => $user,
                'content'       => $wbContent,
                'source'        => $wb['mblog']['source'],
                'retweet_count' => $wb['mblog']['reposts_count'],
                'comment_count' => $wb['mblog']['comments_count'],
                'like_count'    => $wb['mblog']['attitudes_count'],
                'pic_num'       => $wb['mblog']['pic_num'],
                'pics'          => '',
                'cdatetime'     => $datetime,
            ];
            // 微博图片
            if ($wb['mblog']['pic_num'] != 0) {
                $arr = [];
                foreach ($wb['mblog']['pics'] as $pic) {
                    array_push($arr, $pic['large']['url']);
                }
                $data['pics'] = implode(',', $arr);
            }
            // 微博故事 - 3，微博live - 1，视频 - 2
            if ($wb['mblog']['live_photo']) {
                $arr = [];
                foreach ($wb['mblog']['live_photo'] as $photo) {
                    array_push($arr, $photo);
                }
                $data['media'] = implode(',', $arr);
                $data['media_category'] = 1;
            }
            if ($wb['mblog']['page_info']['type'] == 'video') {
                $data['media_category'] = 2;
                $data['media'] = $wb['mblog']['page_info']['page_url'];
            }
            if ($wb['mblog']['page_info']['type'] == 'story') {
                $data['media_category'] = 3;
                $data['media'] = $wb['mblog']['page_info']['page_url'];
            }
            if (WeiboContentService::getInstance()->addOne($data) === false) {
                Log::record("插入新微博错误，数据如下". implode(',', $data));
                continue;
            }
        }
        if (!$sinceId) {
            Log::record("所有历史数据爬取结束");
            // 更新状态
            UserService::getInstance()->updateHistoryStatus($user);
            exit();
        }
        // 下一页
        $this->nextPage($user, $prefix, $sinceId);
    }
}