<?php


namespace Console\Controller;


use Console\Service\ConfigService;
use Console\Service\ProxyService;
use Console\Service\UserConfigService;
use Console\Service\UserService;
use Think\Log;

class UserController extends BaseController
{
    /**
     * 新增需要采集的用户信息
     */
    public function getUserInfo()
    {
        // 获取所需采集的用户
        $requestUserList = UserConfigService::getInstance()->queryUserList();
        // 获取已经采集的用户
        $userList = UserService::getInstance()->queryUserList();
        // 选出还未采集的用户
        $users = array_diff($requestUserList, $userList);
        if ($users == []) {
            Log::record("没有需要采集的用户");
            exit();
        }
        // 获取前缀
        $prefix = ConfigService::getInstance()->queryConfig()['userinfo_prefix'];
        foreach ($users as $user) {
            $url      = "https://m.weibo.cn/api/container/getIndex?type=uid&value={$user}&containerid={$prefix}{$user}";
            $contents = file_get_contents($url);
            if ($contents === false) {
                $contents = $this->proxyRequest($url);
            }
            if ($contents === false) {
                Log::record("访问url失败");
                exit();
            }
            $userJson = json_decode($contents, true)['data']['userInfo'];
            $userInfo = [
                'wb_id'        => $user,
                'follow_count' => $userJson['follow_count'],
                'fans_count'   => $userJson['followers_count'],
                'weibo_count'  => $userJson['statuses_count'],
                'avatar_url'   => $userJson['avatar_hd'],
                'nickname'     => $userJson['screen_name'],
                'description'  => $userJson['description'],
                'cdatetime'    => date('Y-m-d H:i:s'),
                'mdatetime'    => date('Y-m-d H:i:s'),
            ];
            if (UserService::getInstance()->addOne($userInfo) === false) {
                Log::record("新增用户失败");
            }
        }
        exit();
    }

    /**
     * 每天更新一次用户信息
     */
    public function updateUserInfo()
    {
        // 所有活跃用户
        $users = UserService::getInstance()->queryAll();
        // 获取前缀
        $prefix = ConfigService::getInstance()->queryConfig()['userinfo_prefix'];
        foreach ($users as $user) {
            $url      = "https://m.weibo.cn/api/container/getIndex?type=uid&value={$user}&containerid={$prefix}{$user}";
            $contents = file_get_contents($url);
            if ($contents === false) {
                $contents = $this->proxyRequest($url);
            }
            if ($contents === false) {
                Log::record("访问url失败");
                exit();
            }
            $userJson = json_decode($contents, true)['data']['userInfo'];
            $userInfo = [
                'follow_count' => $userJson['follow_count'],
                'fans_count'   => $userJson['followers_count'],
                'weibo_count'  => $userJson['statuses_count'],
                'avatar_url'   => $userJson['avatar_hd'],
                'nickname'     => $userJson['screen_name'],
                'description'  => $userJson['description'],
                'mdatetime'    => date('Y-m-d H:i:s'),
            ];
            if (UserService::getInstance()->updateByWbId($user, $userInfo) === false) {
                Log::record("更新用户失败");
            }
        }
        exit();
    }

    private function proxyRequest($url)
    {
        // 获取全部可用proxy
        $proxyList = ProxyService::getInstance()->queryAvailable();
        $result    = false;
        foreach ($proxyList as $proxy) {
            $context = [
                'http' => [
                    'proxy'           => "tcp://$proxy",
                    'request_fulluri' => true,
                ]
            ];
            $context = stream_context_create($context);
            $result  = file_get_contents($url, false, $context);
            if ($result != false) {
                return $result;
            }
        }

        return $result;
    }
}