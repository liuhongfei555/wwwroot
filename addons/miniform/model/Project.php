<?php

namespace addons\miniform\model;

use think\Model;
use traits\model\SoftDelete;

class Project extends Model
{

    use SoftDelete;
    // 表名
    protected $name = 'miniform_project';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'begintime_text',
        'endtime_text',
        'createtime_text',
        'status_text',
        'status_ing',
        'button_text',
    ];


    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden'), 'expired' => __('Expired')];
    }


    public function getBegintimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['begintime']) ? $data['begintime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['endtime']) ? $data['endtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getSigninNameAttr($value, $data)
    {
        return is_array($value) ? $value : (array)json_decode($value, true);
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getStatusIngAttr($value, $data)
    {
        if (!isset($data['begintime']) || !isset($data['endtime'])) {
            return '已结束';
        }
        $time = time();
        if ($time < $data['begintime']) {
            return '未开始';
        }
        if ($data['endtime'] < $time) {
            return '已结束';
        }

        return '进行中';

    }

    public function getButtonTextAttr($value, $data)
    {
        if (!isset($data['begintime']) || !isset($data['endtime'])) {
            return '已结束';
        }
        $time = time();
        if ($time < $data['begintime']) {
            return '未开始';
        }
        if ($data['endtime'] < $time) {
            return '已结束';
        }

        return '立即' . ($data['label'] ?? '报名');

    }

    public function getSurplusTimeAttr($value, $data)
    {
        $time = time();
        if (!isset($data['begintime']) || !isset($data['endtime'])) {
            return 0;
        }
        if ($time < $data['begintime']) {
            return $data['begintime'] - $time;
        } else {
            return max(0, $data['endtime'] - $time);
        }

    }

    public function getOpenSigninAttr($value, $data)
    {
        if (!isset($data['id'])) {
            return 0;
        }
        if (!$data['is_signin']) {
            return -1;
        }
        $timerange = explode(' - ', $data['signin_time']);
        if (count($timerange) != 2) {
            return -1;
        }
        $begintime = strtotime($timerange[0]);
        $endtime = strtotime($timerange[1]);
        $time = time();

        if ($time < $begintime) {
            return -2;
        }
        if ($time >= $endtime) {
            return -3;
        }
        return 1;
    }

    protected function setBegintimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function getImageAttr($value, $data)
    {
        if (isset($data['images']) && !empty($data['images'])) {
            $img = explode(',', $data['images']);
            return cdnurl($img[0], true);
        }
        return '';
    }

    public function getImagesTextAttr($value, $data)
    {
        if (isset($data['images']) && !empty($data['images'])) {
            $img = explode(',', $data['images']);
            foreach ($img as &$res) {
                $res = cdnurl($res, true);
            }
            return $img;
        }
        return [];
    }

    public function category()
    {
        return $this->hasOne('Category', 'id', 'category_id', [], 'LEFT');
    }
}
