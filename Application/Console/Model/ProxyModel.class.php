<?php


namespace Console\Model;


use Think\Model;

class ProxyModel extends Model
{
    public function queryAvailable()
    {
        $where['status'] = 1;
        return $this->where($where)->order('priority')->getField('address', true);
    }

    public function removeAll()
    {
        return $this->where("1")->delete();
    }

    public function addOne($data)
    {
        return $this->add($data);
    }

    public function queryAll()
    {
        return $this->getField('address', true);
    }

    public function updateProxy($proxy, $data)
    {
        $where['address'] = $proxy;
        return $this->where($where)->save($data);
    }
}