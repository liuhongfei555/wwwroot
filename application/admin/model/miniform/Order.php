<?php

namespace app\admin\model\miniform;

use think\Model;


class Order extends Model
{

    // 表名
    protected $name = 'miniform_order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'paytime_text',
        'status_text'
    ];

    protected static function init()
    {

        self::beforeUpdate(function ($row) {
            if ($row['status'] == 'paid' && $row['payamount'] == 0) {
                $row->payamount = $row->amount;
            }
            if ($row['status'] == 'paid' && empty($row['paytype'])) {
                $row->paytype = 'system';
            }
            if ($row['status'] == 'paid' && empty($row['paytime'])) {
                $row->paytime = time();
            }
        });

        self::afterUpdate(function ($row) {
            $data = $row->getChangedData();
            if (isset($data['status'])) {
                \addons\miniform\model\Order::backSettle($row['orderid'], $row['status']);
            }
        });
    }


    public function getStatusList()
    {
        return ['created' => __('Status created'), 'paid' => __('Status paid'), 'expired' => __('Status expired'), 'refunding' => __('Refunding'), 'refunded' => __('Status refunded')];
    }


    public function getPaytimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paytime']) ? $data['paytime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setPaytimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function project()
    {
        return $this->belongsTo('\app\admin\model\miniform\Project', 'project_id', 'id', [])->setEagerlyType(0);
    }

    public function user()
    {
        return $this->belongsTo('\app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
