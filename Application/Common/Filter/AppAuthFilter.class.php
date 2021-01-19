<?php
/**
 * Created by PhpStorm.
 * User: wuxianli@gm825.com
 * Date: 2018/7/6/006
 * Time: 10:24
 */

namespace Common\Filter;

use Api\Service\AppService;

class AppAuthFilter extends Filter
{
    protected $excludes = ['/app/init','/user/login','/user/info','/app/verify','/user/sms', '/user/add', '/user/forget', '/user/logout','/user/check','/app/list' ];

    public function __construct(){
        parent::__construct();
        $this->AppService = new AppService();
    }

    public function handle()
    {
        $uid = $this->AppService->getUidByAppId($this->getAppId());
        if ($uid != UID) E('没有数据');
    }

    private function getAppId()
    {
        return I('get.app_id', '');
    }
}