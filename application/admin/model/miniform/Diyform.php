<?php

namespace app\admin\model\miniform;

use addons\miniform\model\Logs;
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

    public static function init()
    {
        self::afterWrite(function ($row) {
            $changed = $row->getChangedData();
            if (isset($changed['status'])) {
                $order = Order::where('diyform_id', $row['id'])->where('project_id', $row['project_id'])->find();
                if ($order) {
                    if ($changed['status'] == 'paid') {
                        $order->status = 'paid';
                        $order->save();
                    }
                    if ($order->status == 'paid' && ($changed['status'] == 'canceled' || $changed['status'] == 'nonpayment')) {
                        $order->status = 'refunded';
                        $order->save();
                    }
                }
            }
            Project::refresRegistered($row['project_id']);
        });
        self::afterDelete(function ($row) {
            Project::refresRegistered($row['project_id']);
            Logs::where('project_id', $row['project_id'])->where('diyform_id', $row['id'])->delete();
        });
    }

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

    public function getSignintimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['signintime']) ? $data['signintime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setSignintimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setVerificationtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

}
