<?php


namespace Console\Service;


use Think\Log;
use Think\Service;

class ProxyService extends Service
{
    protected $_model;
    protected $_modelName = '\Console\Model\ProxyModel';

    public function freshProxy($proxyList)
    {
        M()->startTrans();
        // 首先清空数据库
        if ($this->removeAll() === false) {
            Log::record("清空proxy表失败");
            M()->rollback();
            return false;
        }
        foreach ($proxyList as $proxy) {
            $data = [
                'address'   => $proxy,
                'cdatetime' => date('Y-m-d H:i:s'),
                'mdatetime' => date('Y-m-d H:i:s'),
                'status'    => 1,
                'priority'  => 0
            ];
            if ($this->addOne($data) === false) {
                Log::record("插入一条新数据失败");
                M()->rollback();
                return false;
            }
        }
        // 都成功了
        M()->commit();
        return true;
    }

    private function removeAll()
    {
        return $this->_model->removeAll();
    }

    private function addOne($data)
    {
        return $this->_model->addOne($data);
    }

    public function queryAll()
    {
        return $this->_model->queryAll();
    }

    public function updateProxy($proxy, $data)
    {
        return $this->_model->updateProxy($proxy, $data);
    }

    public function queryAvailable()
    {
        return (array)$this->_model->queryAvailable();
    }

}