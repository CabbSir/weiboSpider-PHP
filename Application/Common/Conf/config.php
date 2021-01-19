<?php

$arrErrCode = [
    'INTERNAL_ERROR'        => 1,
    'PARAM_ERROR'           => 2,
    'API_UNSUPPORTED'       => 3,
    'REQUEST_DUP'           => 4,
    'ILLEGAL_CHANNEL'       => 5,
    'USER_NOT_LOGIN'        => 6,
    'CAPTCHA_ERROR'         => 7,
    'USER_NOT_AUTH'         => 8,
    'REQUEST_ERROR'         => 9,
    'UID_NOT_EXIST'         => 10,
    'FILE_FORMAT_ERROR'     => 11,
    'FILE_UPLOAD_ERROR'     => 12,
    'PASSWORD_ERROR'        => 13,
    'RESOURCE_NOT_EXIST'    => 14,
    'UPLOAD_ERROR'          => 15,
    'GAME_NOT_EXIST'        => 16,
    'QUERY_STRING_TOO_LONG' => 17,
    'METHOD_NOT_ALLOWED'    => 18,
];

$arrError = [
    'ERROR_MESSAGE' => [
        $arrErrCode['INTERNAL_ERROR']        => 'internal server error',
        $arrErrCode['PARAM_ERROR']           => 'param error',
        $arrErrCode['API_UNSUPPORTED']       => 'Unsupported api',
        $arrErrCode['REQUEST_DUP']           => 'dup request',
        $arrErrCode['ILLEGAL_CHANNEL']       => 'illegal request',
        $arrErrCode['USER_NOT_LOGIN']        => 'user not login',
        $arrErrCode['CAPTCHA_ERROR']         => 'captcha error',
        $arrErrCode['USER_NOT_AUTH']         => 'user not authorized',
        $arrErrCode['UID_NOT_EXIST']         => 'user not exist',
        $arrErrCode['FILE_FORMAT_ERROR']     => 'file format error',
        $arrErrCode['FILE_UPLOAD_ERROR']     => 'file upload error',
        $arrErrCode['PASSWORD_ERROR']        => 'password error',
        $arrErrCode['RESOURCE_NOT_EXIST']    => 'resource not exist',
        $arrErrCode['GAME_NOT_EXIST']        => 'game not exist',
        $arrErrCode['QUERY_STRING_TOO_LONG'] => 'query string too long',
        $arrErrCode['METHOD_NOT_ALLOWED']    => 'Method Not Allowed',
    ],
    'HTTP_CODE'     => [
        $arrErrCode['INTERNAL_ERROR']        => 500,
        $arrErrCode['PARAM_ERROR']           => 400,
        $arrErrCode['API_UNSUPPORTED']       => 403,
        $arrErrCode['REQUEST_DUP']           => 409,
        $arrErrCode['ILLEGAL_CHANNEL']       => 403,
        $arrErrCode['USER_NOT_LOGIN']        => 403,
        $arrErrCode['CAPTCHA_ERROR']         => 403,
        $arrErrCode['USER_NOT_AUTH']         => 403,
        $arrErrCode['REQUEST_ERROR']         => 403,
        $arrErrCode['FILE_FORMAT_ERROR']     => 403,
        $arrErrCode['FILE_UPLOAD_ERROR']     => 403,
        $arrErrCode['PASSWORD_ERROR']        => 403,
        $arrErrCode['RESOURCE_NOT_EXIST']    => 403,
        $arrErrCode['UPLOAD_ERROR']          => 403,
        $arrErrCode['GAME_NOT_EXIST']        => 403,
        $arrErrCode['QUERY_STRING_TOO_LONG'] => 403,
        $arrErrCode['METHOD_NOT_ALLOWED']    => 405,
    ]
];

$appConf = [
    'LOAD_EXT_CONFIG' => [
        'basic',
        'module',
        'upload'
    ],

    'IS_WEB'               => 0,
    'IS_API'               => 0,    // 是否是专用接口（非操作请求）

    /* 模块相关配置 */
    'TMPL_CACHE_ON'        => false,
    'CONTROLLER_LEVEL'     => 1,

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => false,    // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 1,       // URL模式
    'VAR_URL_PARAMS'       => '',      // PATH_INFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/',     // PATH_INFO URL分割符

    'SESSION_AUTO_START' => true,    // 是否自动开启Session
];

$error_map = [
    'MAP_ERROR' => [
        '0'   => '未知错误',
        '-1'  => '用户名长度必须在16个字符以内！',
        '-2'  => '用户名被禁止注册！',
        '-3'  => '用户名被占用！',
        '-4'  => '密码长度必须在6-30个字符之间！',
        '-5'  => '邮箱格式不正确！',
        '-6'  => '邮箱长度必须在1-32个字符之间！',
        '-7'  => '邮箱被禁止注册！',
        '-8'  => '邮箱被占用！',
        '-9'  => '手机格式不正确！',
        '-10' => '手机被禁止注册！',
        '-11' => '手机号被占用！',
        '-12' => '图形验证码不正确！',
        '-13' => '手机验证码已过期！',
        '-14' => '参数错误！',
        '-15' => '密码和重复密码不相同！',
        '-16' => '用户不存在或被禁用!',
        '-17' => '密码错误！',
        '-18' => '手机未注册!',
    ],
];

return array_merge($appConf, $arrErrCode, $arrError, $error_map);
