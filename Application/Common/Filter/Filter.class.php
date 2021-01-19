<?php
/**
 * Created by PhpStorm.
 * User: kongqiang
 * Date: 2018/1/29
 * Time: 下午12:20
 */

namespace Common\Filter;

abstract class Filter
{
    protected $excludes = []; //例外的路由
    protected $route;
    protected $specials = [
        '/report/daily_export',
    ];//sid和token从参数中获取的路由

    public function __construct()
    {
        $this->route = __ROUTE__;
    }

    public abstract function handle();

    protected function error($error = '发生错误', $error_code = 1)
    {
        E($error, $error_code);
    }


    public function load()
    {
        if (!in_array($this->route, $this->excludes) && method_exists($this, "handle")) {
            $this->handle();
        }
    }
}