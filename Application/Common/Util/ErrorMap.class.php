<?php

namespace Common\Util;

class ErrorMap
{
    const SUCCESS_CODE          = [0, '成功'];
    const SUCCESS               = [200, '处理成功'];
    const INTERFACE_NOT_FOUND   = [404, '接口不存在'];
    const HTTP_METHOD_NOT_ALLOW = [405, '不允许的HTTP请求方法'];
    const PARAM_MISSING         = [440, '缺少参数'];
    const PARAM_INVALID         = [441, '请求参数错误'];
    const PARAM_TOKEN_INVALID   = [442, '网络异常，请重新提交'];
    const PARAM_TOKEN_ERROR     = [443, '网络异常，请重新提交'];
    const SERVER_INTERNAL_ERROR = [500, '服务器错误'];
    const OPERATION_FAIL_ERROR  = [501, '请求失败,请重试'];

    const NOT_LOGGED                             = [1001, '未登录'];
    const CODE_VERIFY_FAILED                     = [1002, '验证码错误'];
    const USER_NOT_AUTH                          = [1003, '您不是管理员'];
    const UID_NOT_EXIST                          = [1004, '用户不存在'];
    const PASSWORD_ERROR                         = [1005, '密码错误'];
    const REQUEST_ERROR                          = [1006, '为止错误'];
    const LOGIN_FAILED_WITH_FREEZE               = [1007, '对不起，您的账号已冻结'];
    const MOBILE_VERIFICATION_CODE_VERIFY_FAILED = [1101, '短信验证码发送失败'];

    const BUILD_NEW_TASK_ERROR = [10000, '新建任务失败'];
    const NO_SEARCH_RESULT     = [10001, '没有搜索结果'];
    const NO_LOCAL_RESULT      = [10002, '没有本地搜索结果'];
}
