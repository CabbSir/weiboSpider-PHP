<?php


namespace Console\Model;


use Think\Model;

class ConfigModel extends Model
{
    public function queryAll()
    {
        return $this->find();
    }
}