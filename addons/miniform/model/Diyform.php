<?php

namespace addons\miniform\model;

use think\Model;

class Diyform extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];

    public function setName($name)
    {
        $this->getQuery()->name('miniform_' . $name);
        return $this;
    }

    public function getStatusList()
    {
        return ['free' => __('Free'), 'nonpayment' => __('Nonpayment'), 'paid' => __('Paid'), 'canceled' => __('Canceled')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function user()
    {
        return $this->hasOne('\app\common\model\User', 'id', 'user_id', [], 'LEFT');
    }
}
