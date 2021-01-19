<?php


namespace Console\Controller;


use Console\Service\ProxyService;
use Think\Log;

class ProxyController extends BaseController
{
    /**
     * 每24小时刷新一次数据库
     */
    public function freshProxyInfo()
    {
        $proxyJson = json_decode(file_get_contents("http://local.cabbsir.com:5010/get_all"), true);
        $proxyList = [];
        foreach ($proxyJson as $proxy) {
            array_push($proxyList, $proxy['proxy']);
        }
        if (ProxyService::getInstance()->freshProxy($proxyList) === false) {
            Log::record("刷新代理库失败");
        } else {
            Log::record("刷新代理库成功");
        }
        exit();
    }

    /**
     * 每1小时测试一次代理速度
     */
    public function testProxySpeed()
    {
        // 取出所有ip
        $proxyList = ProxyService::getInstance()->queryAll();
        foreach ($proxyList as $proxy) {
            $begin   = getMilliTime();
            $context = [
                'http' => [
                    'proxy'           => "tcp://$proxy",
                    'request_fulluri' => true,
                ]
            ];
            $context = stream_context_create($context);
            $result  = file_get_contents("https://m.weibo.cn/api/container/getIndex?type=uid&value=5787832229&containerid=1005055787832229", false, $context);
            $end     = getMilliTime();
            if ($result === false) {
                $data = [
                    'mdatetime' => date('Y-m-d H:i:s'),
                    'status'    => 2,
                ];
            } else {
                $data = [
                    'mdatetime' => date('Y-m-d H:i:s'),
                    'priority'  => $end-$begin
                ];
            }
            // 更新对应proxy
            if (ProxyService::getInstance()->updateProxy($proxy, $data) === false) {
                Log::record("更新{$proxy}的状态失败");
            }
        }
        exit();
    }
}