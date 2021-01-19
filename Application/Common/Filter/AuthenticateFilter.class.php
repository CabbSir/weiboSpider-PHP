<?php
/**
 * Created by PhpStorm.
 * User: kongqiang
 * Date: 2018/2/1
 * Time: 上午10:08
 */

namespace Common\Filter;


use Api\Service\UserService;

class AuthenticateFilter extends Filter
{
    private   $userService;
    private   $oneLogin = false;
    protected $excludes = [ '/app/init', '/user/login', '/app/verify', '/user/sms', '/user/add', '/user/forget', '/user/logout', '/user/check'];

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    public function handle()
    {
        $uid         = is_login();
        $accessToken = $this->userService->getAccessToken($uid);
        if (!$uid || $accessToken != $this->getAccessToken()) {
            $this->error('身份认证失败', 401);
        }
        // @todo 限制唯一登录
        /*if ($this->oneLogin && $accessToken != $this->getAccessToken()) {
            $this->error('您的账号在其他地方登录', 401);
        }*/
        $user = $this->userService->getUserById($uid);
        if ($user == -1 || $user['status'] != 1) {
            $this->error("账号不存在或被禁用", 401);
        }
        define('UID', $user['id']);
    }

    private function getAccessToken()
    {
        new AppAuthFilter();
        if (in_array($this->route, $this->specials)) {
            return I('get.token', '');
        }
        return $_SERVER['HTTP_X_TOKEN'];
    }

}