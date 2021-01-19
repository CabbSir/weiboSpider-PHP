<?php
/**
 * Created by PhpStorm.
 * User: kongqiang
 * Date: 2018/1/31
 * Time: 上午11:23
 */

namespace Common\Controller;


use Common\Filter\FilterLoader;
use Common\Util\ErrorMap;
use Common\Util\Http_Response;
use Think\Controller;

class BaseController extends Controller
{
    protected function _initialize()
    {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        
        if (in_array($origin, C('HTTP_ALLOW_ORIGINS'))) {
            header("Access-Control-Allow-Origin: {$origin}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Methods: OPTIONS, GET, POST');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-TOKEN, X-SID');
        }

        // 处理OPTIONS请求
        $_SERVER['REQUEST_METHOD'] === 'OPTIONS' && exit();

//        $this->_loadFilter();
    }

    /**
     * @param array $data
     */
    protected function success($data = [])
    {
        $this->returnJson(['message' => $data]);
    }

    /**
     * @param array $errInfo
     */
    protected function error(array $errInfo)
    {
        $code    = isset($errInfo[0]) ? $errInfo[0] : ErrorMap::SERVER_INTERNAL_ERROR[0];
        $message = isset($errInfo[1]) ? $errInfo[1] : ErrorMap::SERVER_INTERNAL_ERROR[1];

        $this->returnJson(null, $code, $message);
    }

    /**
     * @param array $data
     * @param int $code
     * @param null $info
     */
    protected function returnJson($data = [], $code = 0, $info = null)
    {
        Http_Response::set('code', $code);
        isset($info) && Http_Response::set('msg', $info);
        foreach ($data as $k => $v) {
            Http_Response::set($k, $v);
        }
        Http_Response::send();
    }

    private function _loadFilter()
    {
        return new FilterLoader();
    }

}