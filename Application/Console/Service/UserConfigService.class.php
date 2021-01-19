<?php


namespace Console\Service;


use Think\Service;

class UserConfigService extends Service
{
    protected $_model;
    protected $_modelName = '\Console\Model\UserConfigModel';

    public function queryUserList()
    {
        return (array)$this->_model->queryUsers();
    }
}