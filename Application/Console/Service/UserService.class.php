<?php


namespace Console\Service;


use Think\Service;

class UserService extends Service
{
    protected $_model;
    protected $_modelName = '\Console\Model\UserModel';

    public function queryUserList()
    {
        return (array)$this->_model->queryWbIds();
    }

    public function addOne($userInfo)
    {
        return $this->_model->addOne($userInfo);
    }

    public function queryNewUserList()
    {
        return $this->_model->queryNewUserList();
    }

    public function updateHistoryStatus($user)
    {
        return $this->_model->updateHistoryStatus($user);
    }

    public function queryOldUserList()
    {
        return $this->_model->queryOldUserList();
    }

    public function queryAll()
    {
        return $this->_model->queryAllActive();
    }

    public function updateByWbId($user, $userInfo)
    {
        return $this->_model->updateByWbId($user, $userInfo);
    }
}