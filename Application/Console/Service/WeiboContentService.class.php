<?php


namespace Console\Service;


use Think\Service;

class WeiboContentService extends Service
{
    protected $_model;
    protected $_modelName = '\Console\Model\WeiboContentModel';

    public function addOne($data)
    {
        return $this->_model->addOne($data);
    }

    public function queryLastDatetime($wbId)
    {
        return $this->_model->queryLastDatetime($wbId);
    }

    public function queryAllUnemail()
    {
        return $this->_model->queryAllUnemail();
    }

    public function updateSendStatus($id)
    {
        return $this->_model->updateSendStatus($id);
    }
}