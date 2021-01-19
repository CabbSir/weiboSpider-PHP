<?php


namespace Console\Service;


use Think\Service;

class ConfigService extends Service
{
    protected $_model;
    protected $_modelName = '\Console\Model\ConfigModel';

    public function queryConfig()
    {
        return $this->_model->queryAll();
    }
}