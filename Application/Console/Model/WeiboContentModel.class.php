<?php


namespace Console\Model;


use Think\Model;

class WeiboContentModel extends Model
{
    public function addOne($data)
    {
        return $this->add($data);
    }

    public function queryLastDatetime($id)
    {
        $where['user_id'] = $id;

        return $this->where($where)->order('cdatetime DESC')->getField('cdatetime');
    }

    public function queryAllUnemail()
    {
        $where['w.send_status'] = 2;
        $fields                 = [
            'w.id'             => 'id',
            'w.content'        => 'content',
            'w.source'         => 'source',
            'w.pics'           => 'pics',
            'w.media'          => 'media',
            'w.media_category' => 'mediaCategory',
            'w.cdatetime'      => 'cdatetime',
            'uc.email'         => 'email',
            'u.nickname'       => 'name'
        ];

        return $this->alias('w')->join('tb_user_config uc ON w.user_id=uc.wb_id')
            ->join('tb_user u ON u.wb_id=w.user_id')->where($where)->field($fields)->select();
    }

    public function updateSendStatus($id)
    {
        $where['id'] = $id;
        $data['send_status'] = 1;
        return $this->where($where)->save($data);
    }
}