<?php


namespace Console\Model;


use Think\Model;

class UserConfigModel extends Model
{
    public function queryUsers()
    {
        return $this->getField('wb_id', true);
    }
}