<?php


namespace Console\Model;


use Think\Model;

class UserModel extends Model
{
    public function queryWbIds()
    {
        return $this->getField('wb_id', true);
    }

    public function addOne($data)
    {
        return $this->add($data);
    }

    public function queryNewUserList()
    {
        $where = [
            'status'         => 1,
            'history_status' => 2
        ];

        return $this->where($where)->getField('wb_id', true);
    }

    public function updateHistoryStatus($user)
    {
        $where['wb_id']         = $user;
        $data['history_status'] = 1;

        return $this->where($where)->save($data);
    }

    public function queryOldUserList()
    {
        $where  = [
            'u.status'         => 1,
            'u.history_status' => 1
        ];
        $fields = [
            'u.wb_id'  => 'wbId',
            'uc.email' => 'email'
        ];
        return $this->alias('u')->join('tb_user_config uc ON u.wb_id=uc.wb_id')->where($where)->field($fields)->select();
    }

    public function queryAllActive()
    {
        $where['status'] = 1;
        return $this->where($where)->getField('wb_id', true);
    }

    public function updateByWbId($user, $userInfo)
    {
        $where['wb_id'] = $user;
        return $this->where($where)->save($userInfo);
    }

}