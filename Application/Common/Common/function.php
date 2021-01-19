<?php

use Console\Service\ProxyIpService;
use Think\Log;

/**
 * 数据签名认证
 * @param array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    //$code .= C('USER_AUTH_KEY');
    $sign = sha1($code); //生成签名

    return $sign;
}

function get_user_info()
{
    return session('uid');
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{
    $user = session('uid');
    if (empty($user)) {
        return 0;
    } else {
        return session('uid');
    }
}

/**
 * 检测当前用户是否为管理员
 * @param null $uid
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($uid = null)
{
    $uid = is_null($uid) ? is_login() : $uid;
    if (!$uid) return false;
    $table = C('ADMIN_TABLE');

    if (empty($table)) {
        return false;
    }

    $where['id'] = $uid;

    return M($table)->where($where)->getField('id');
}

/**
 * 格式化数字
 * @param float $value 数值
 * @param int $scale 小数位数
 * @param null $default 默认值
 * @return null|string
 */
function numFormat($value, $scale = 2, $default = null)
{
    return (is_numeric($value) && !is_nan($value) && $value != INF) ? bcadd($value, 0, $scale) : $default;
}

/*
 *
 * 判断用户名的格式是否正确
 *
 * */
function checkUsername($username)
{
    if (!preg_match('/^[0-9A-Za-z]{6,18}$/', $username)) {
        return [
            'result' => false,
            'error'  => '用户名错误:格式错误,允许6-18位字母、数字',
        ];
    } else {
        return [
            'result' => true,
        ];
    }
}

/**
 * 校验密码强度 (6-20字母数字特殊字符3种以上)
 * @param $password
 * @return array()
 */
function checkPasswordStrong($password)
{
    $num = 0;

    // 包含空白字符
    if (preg_match('/\s+/', $password)) {
        return [
            'result' => false,
            'error'  => '密码不能包含空的字符',
        ];
    }

    //if (!preg_match('/[\S]{6,20}/', $password)) {
    if (!preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[!@#$%^&*])[0-9a-zA-Z!@#$%^&*]{6,18}$/', $password)) {
        return [
            'result' => false,
            'error'  => '密码应为6-18位字母,数字,特殊字符(!@#$%^&*)3种及以上组合',
        ];
    }

    // 包含数字
    if (preg_match('/\d+/', $password)) {
        $num += 1;
    }

    // 包含小写字母
    if (preg_match('/[a-z]+/', $password)) {
        $num += 1;
    }

    // 包含大写字母
    if (preg_match('/[A-Z]+/', $password)) {
        $num += 1;
    }

    // 包含特殊字符
    if (preg_match('/[\W_]+/', $password)) {
        $num += 1;
    }

    if ($num < 3) {
        return [
            'result' => false,
            'error'  => '密码应为6-18位字母,数字,特殊字符(!@#$%^&*)3种以上组合',
        ];
    }

    return [
        'result' => true,
    ];
}

/**
 * 对数组进行分组
 * @param $arr
 * @param $key
 * @return array
 */
function array_group_by($arr, $key)
{
    $grouped = [];
    foreach ($arr as $value) {
        $grouped[$value[$key]][] = $value;
    }
    // Recursively build a nested grouping if more parameters are supplied
    // Each grouped array value is grouped according to the next sequential key
    if (func_num_args() > 2) {
        $args = func_get_args();
        foreach ($grouped as $key => $value) {
            $parms         = array_merge([$value], array_slice($args, 2, func_num_args()));
            $grouped[$key] = call_user_func_array('array_group_by', $parms);
        }
    }

    return $grouped;
}

/**
 * UUID
 * @return string
 */
function UUID()
{
    $str  = md5(uniqid(mt_rand(), true));
    $uuid = substr($str, 0, 8) . '-';
    $uuid .= substr($str, 8, 4) . '-';
    $uuid .= substr($str, 12, 4) . '-';
    $uuid .= substr($str, 16, 4) . '-';
    $uuid .= substr($str, 20, 12);

    return $uuid;
}

/**
 * 获取上传文件地址
 * @param $file
 * @return string
 */
function getUploadFileUrl($file)
{
    $domain = C("UPLOAD_FILE_DOMAIN");

    if (stripos($file, 'http') === 0) {
        return $file;
    }

    return $domain . $file;
}

/**
 * curl函数
 * @datetime 2017-04-05T11:09:12+0800
 * @param string $url 请求url
 * @param string $type POST/GET
 * @param boolean $data 请求参数
 * @param string &$err_msg 错误信息
 * @param integer $timeout 超时时间
 * @param array $cert_info 证书信息
 * @return   mixed $response 返回数据
 * @author    <chensisong@gm825.com>
 */
function curl_request($url, $type, $data = false, &$err_msg = null, $timeout = 20, $cert_info = [])
{
    $type = strtoupper($type);
    if (is_array($data)) {
        $data = http_build_query($data);
    }

    $option = [];

    if ($type == 'POST') {
        $option[CURLOPT_POST] = 1;
    }
    if ($data) {
        if ($type == 'POST') {
            $option[CURLOPT_POSTFIELDS] = $data;
        } elseif ($type == 'GET') {
            $url = strpos($url, '?') !== false ? $url . '&' . $data : $url . '?' . $data;
        }
    }

    $option[CURLOPT_URL]            = $url;
    $option[CURLOPT_FOLLOWLOCATION] = true;
    $option[CURLOPT_MAXREDIRS]      = 4;
    $option[CURLOPT_RETURNTRANSFER] = true;
    $option[CURLOPT_TIMEOUT]        = $timeout;

    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $option[CURLOPT_USERAGENT] = $_SERVER['HTTP_USER_AGENT'];
    }

    //设置证书信息
    if (!empty($cert_info)) {
        $option[CURLOPT_SSLCERT]       = $cert_info['cert_file'];
        $option[CURLOPT_SSLCERTPASSWD] = $cert_info['cert_pass'];
        $option[CURLOPT_SSLCERTTYPE]   = $cert_info['cert_type'];
    }

    //设置CA
    if (!empty($cert_info['ca_file'])) {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 1;
        $option[CURLOPT_CAINFO]         = $cert_info['ca_file'];
    } else {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 0;
    }

    $ch = curl_init();
    curl_setopt_array($ch, $option);
    $response = curl_exec($ch);
    $curl_no  = curl_errno($ch);
    $curl_err = curl_error($ch);
    curl_close($ch);

    // error_log
    if ($curl_no > 0) {
        if ($err_msg !== null) {
            $err_msg = '(' . $curl_no . ')' . $curl_err;
        }
    }

    return $response;
}

/**
 * 获取13位毫秒时间戳
 * @return string
 */
function getMilliTime()
{
    list($t1, $t2) = explode(' ', microtime());

    return sprintf('%.0f', (floatval($t1) + intval($t2)) * 1000);
}

/**
 * 13位毫秒转时间格式
 * @param $milliTime
 * @return string
 */
function milli2DateTime($milliTime)
{
    $times = str_split($milliTime, 10);

    return sprintf('%s.%sZ', date('Y-m-d\TH:i:s', $times[0]), str_pad($times[1], 3, 0));
}

/**
 * 获取客户端真实ip
 * @return bool|mixed
 */
function getIp()
{
    $ip = false;
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) {
            array_unshift($ips, $ip);
            $ip = false;
        }
        for ($i = 0; $i < count($ips); $i++) {
            if (!preg_match('/^(10│172.16│192.168)./', $ips[$i])) {
                $ip = $ips[$i];
                break;
            }
        }
    }

    return $ip ? $ip : $_SERVER['REMOTE_ADDR'];
}

/**
 * 构造post请求 返回请求的html内容
 * @param $url
 * @param $query
 * @return bool|false|string
 */
function send_post($url, $query)
{
    $query = http_build_query($query);

    $options['http'] = [
        'timeout' => 120,
        'method'  => 'POST',
        'header'  => 'Content-type:application/x-www-form-urlencoded',
        'content' => $query
    ];

    $context = stream_context_create($options);

    return file_get_contents($url, false, $context);
}