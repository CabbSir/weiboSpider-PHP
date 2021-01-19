<?php
/**
 * Created by PhpStorm.
 * User: kongqiang
 * Date: 2018/1/29
 * Time: 下午1:49
 */

namespace Common\Filter;


class FilterLoader
{
    private $configs;

    public function __construct()
    {
        define("__ROUTE__", strpos("/", __INFO__) === 0 ? __INFO__ : "/" . __INFO__);
        $method = "_getConfigsBy" . ucfirst(MODULE_NAME);
        if (method_exists($this, $method)) {
            $this->configs = $this->$method();
        }
        $this->_loadBaseFilter();
        $this->_loadRouteFilter();
    }

    private function _loadBaseFilter()
    {
        foreach ($this->configs['base'] as $v) {
            $this->_execFilter($v);
        }
    }

    private function _loadRouteFilter()
    {
        foreach ($this->configs['route'] as $k => $v) {
            if (is_array($v) && !in_array(__ROUTE__, $v)) {
                $this->_execFilter($k);
            }
        }
    }

    private function _execFilter($className)
    {
        $filter = new $className();
        if (method_exists($filter, 'load')) {
            $filter->load();
        }
    }

    private function _getConfigsByApi()
    {
        return [
            'base'  => [
                AuthenticateFilter::class,

            ],
            'route' => [
                /*Filter::class => [
                    '/'
                ]*/
            ]
        ];
    }

}