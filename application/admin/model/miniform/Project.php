<?php

namespace app\admin\model\miniform;

use think\Exception;
use think\Model;
use think\Config;
use think\Db;
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
        'status_text'
    ];

    protected static function init()
    {
        self::beforeInsert(function ($row) {
            $data = (new self)->where('table', $row['table'])->find();
            if ($data) {
                throw new Exception("表单名已经存在！");
            }
            $info = null;
            try {
                $info = Db::name('miniform_' . $row['table'])->getTableInfo();
            } catch (\Exception $e) {
            }
            if ($info) {
                throw new Exception("数据表已经存在");
            }
        });

        self::afterInsert(function ($row) {
            $prefix = Config::get('database.prefix');
            $sql = "CREATE TABLE `{$prefix}miniform_{$row['table']}` (
              `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
              `user_id` int(10) DEFAULT NULL COMMENT '会员ID',
              `project_id` int(10) DEFAULT NULL COMMENT '项目ID',
              `createtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
              `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
              `signintime` bigint(16) DEFAULT NULL COMMENT '签到时间',
              `verificationtime` bigint(16) DEFAULT NULL COMMENT '核销时间',
              `memo` varchar(1500) DEFAULT '' COMMENT '备注',             
              `status` enum('free','nonpayment','paid','canceled') DEFAULT 'free' COMMENT '状态:free=免费,nonpayment=未支付,paid=已支付,canceled=已取消',
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              KEY `createtime` (`createtime`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='{$row['title']}'";
            db()->execute($sql);
            //追加字段
            (new Fields())->saveAll([
                [
                    'source_id'    => $row['id'],
                    'source'       => $row['table'],
                    'name'         => 'name',
                    'title'        => '姓名',
                    'type'         => 'string',
                    'decimals'     => 0,
                    'defaultvalue' => '',
                    'setting'      => ['table' => '', 'conditions' => '', 'key' => '', 'value' => ''],
                    'content'      => 'value1|title1
                    value2|title2',
                    'isshowback'   => 1,
                    'length'       => '50',
                    'status'       => 'normal'
                ],
                [
                    'source_id'    => $row['id'],
                    'source'       => $row['table'],
                    'name'         => 'mobile',
                    'title'        => '电话号码',
                    'type'         => 'string',
                    'decimals'     => 0,
                    'defaultvalue' => '',
                    'setting'      => ['table' => '', 'conditions' => '', 'key' => '', 'value' => ''],
                    'content'      => 'value1|title1
                    value2|title2',
                    'isshowback'   => 1,
                    'length'       => '50',
                    'status'       => 'normal'
                ]
            ]);
        });

        self::afterUpdate(function ($row) {
            //更新自定义字段值
            if (isset($row['fields']) && !empty($row['fields'])) {
                $data = $row['fields'];
                $data['id'] = $row['id'];
                Fields::updateFieldValue($data);
            }
        });

        //项目删除
        self::afterDelete(function ($row) {
            Db::transaction(function () use ($row) {
                //删除项目日志
                \addons\miniform\model\Logs::deleteLogs($row['id']);
            });
        });

        //项目更新前
        self::beforeWrite(function ($row) {
            $row['is_need_login'] = (isset($row['price']) && floatval($row['price']) > 0) || ($row['is_signin'] ?? 0) || ($row['is_verification'] ?? 0) ? 1 : ($row['is_need_login'] ?? 1);
        });
    }

    //删除表单
    public static function deleteForm($table)
    {
        $prefix = Config::get('database.prefix');
        $sql = "DROP TABLE `{$prefix}miniform_{$table}`;";
        db()->execute($sql);
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden'), 'expired' => __('Expired')];
    }

    public function getFrontBackList()
    {
        return ['0' => __('Front'), '1' => __('Back')];
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

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setBegintimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function getSigninNameAttr($value, $data)
    {
        return is_array($value) ? $value : (array)json_decode($value, true);
    }

    protected function setSigninNameAttr($value)
    {
        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }

    //刷新字段
    public static function refresFields($source_id)
    {
        $fields = Fields::where('source', 'project')->where('source_id', $source_id)->field('name')->column('name');
        self::where('id', $source_id)->update(['applyfields' => implode(',', $fields)]);
    }

    //刷新参加人数
    public static function refresRegistered($source_id)
    {
        $project = self::get($source_id);
        if ($project) {
            try {
                $count = Db::name('miniform_' . $project['table'])->where('status', 'in', ['free', 'paid'])->count();
                $count = $count ? (int)$count : 0;
                $project->save(['registered' => $count]);
            } catch (\Exception $e) {
            }
        }
    }


    public function category()
    {
        return $this->belongsTo('Category', 'category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
