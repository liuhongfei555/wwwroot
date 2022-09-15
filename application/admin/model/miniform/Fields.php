<?php

namespace app\admin\model\miniform;

use app\common\model\Config;
use addons\miniform\library\Alter;
use think\exception\PDOException;
use think\Exception;
use think\Model;

class Fields extends Model
{

    // 表名
    protected $name = 'miniform_fields';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';
    // 追加属性
    protected $append = [
        'status_text',
        'content_list',
        'extend_html'
    ];
    protected $type = [
        'setting' => 'json',
    ];
    protected static $listField = ['select', 'selects', 'checkbox', 'radio', 'array'];

    public function setError($error)
    {
        $this->error = $error;
    }

    protected static $config = [];

    protected static function init()
    {
        $config = get_addon_config('miniform');
        self::$config = $config;
        $beforeCallback = function ($row) {
            if (!in_array($row['type'], ['editor', 'text', 'array', 'files'])) {
                $row->isshowback = 1;
            }
            $row->isshowfront = 1;
            if (!preg_match("/^([a-z0-9_]+)$/i", $row['name'])) {
                throw new Exception("字段只支持小写字母数字下划线");
            }
            if (is_numeric(substr($row['name'], 0, 1))) {
                throw new Exception("字段不能以数字开始");
            }

            $exists = Fields::where('source_id', $row['source_id'])->where('source', $row['source'])->where('name', $row['name'])->where('id', '<>', $row['id'] ?? 0)->find();
            if ($exists) {
                throw new Exception("字段已经存在");
            }

            if (in_array(strtolower($row['name']), ["title", "content", "image"])) {
                throw new Exception("保留字段暂不可用");
            }
        };

        $updateCallback = function ($row) {
            if (!preg_match("/^([a-z0-9_]+)$/i", $row['name'])) {
                throw new Exception("字段只支持小写字母数字下划线");
            }
            if (is_numeric(substr($row['name'], 0, 1))) {
                throw new Exception("字段不能以数字开始");
            }

            $exists = Fields::where('source_id', $row['source_id'])->where('source', $row['source'])->where('name', $row['name'])->where('id', '<>', $row['id'] ?? 0)->find();
            if ($exists) {
                throw new Exception("字段已经存在");
            }

            if (in_array(strtolower($row['name']), ["title", "content", "image"])) {
                throw new Exception("保留字段暂不可用");
            }
        };

        $afterInsertCallback = function ($row) {
            //为了避免引起更新的事件回调，这里采用直接执行SQL的写法
            $row->query($row->fetchSql(true)->update(['id' => $row['id'], 'weigh' => $row['id']]));
            if ($row['source'] == 'project') {
                Project::refresFields($row->source_id);
            } else {
                self::refreshTable($row);
            }
        };

        $afterUpdateCallback = function ($row) {
            if ($row['source'] == 'project') {
                Project::refresFields($row->source_id);
            } else {
                self::refreshTable($row, 'update');
            }
        };

        self::beforeInsert($beforeCallback);
        self::beforeUpdate($updateCallback);

        self::afterInsert($afterInsertCallback);
        self::afterUpdate($afterUpdateCallback);

        self::afterDelete(function ($row) {
            if ($row['source'] == 'project') {
                Project::refresFields($row->source_id);
            } else {
                self::refreshTable($row, 'delete');
            }
        });
    }

    public static function refreshTable($row, $action = 'insert')
    {
        $model = Project::get($row['source_id']);
        if (!$model) {
            return false;
        }
        $table = 'miniform_' . $model['table'];

        $alter = Alter::instance();

        if (isset($row['oldname']) && $row['oldname'] != $row['name']) {
            $alter->setOldname($row['oldname']);
        }
        $alter->setTable($table)
            ->setName($row['name'])
            ->setLength($row['length'])
            ->setContent($row['content'])
            ->setDecimals($row['decimals'])
            ->setDefaultvalue($row['defaultvalue'])
            ->setComment($row['title'])
            ->setType($row['type']);
        if ($action == 'insert') {
            $sql = $alter->getAddSql();
        } elseif ($action == 'update') {
            $sql = $alter->getModifySql();
        } elseif ($action == 'delete') {
            $sql = $alter->getDropSql();
        } else {
            throw new Exception("操作类型错误");
        }
        try {
            db()->execute($sql);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getContentListAttr($value, $data)
    {
        return in_array($data['type'], self::$listField) ? Config::decode($data['content']) : $data['content'];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getExtendHtmlAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['extend']) ? $data['extend'] : '');
        return $value;
    }

    //更新数据
    public static function updateFieldValue($data, $source = 'project')
    {
        $params = self::where('source', $source)->where('source_id', $data['id'])->select();
        $newData = [];
        foreach ($params as $item) {
            $row = $item->toArray();
            if (isset($data[$row['name']]) && !empty($data[$row['name']])) {
                $row['value'] = is_array($data[$row['name']]) ? $row['type'] == 'location' ? json_encode($data[$row['name']], JSON_UNESCAPED_UNICODE) : implode(',', $data[$row['name']]) : $data[$row['name']];
                $newData[] = $row;
            }
        }
        (new self)->allowField(true)->saveAll($newData);
    }

    //删除字段
    public static function deleteField($where)
    {
        $list = (new self())->where($where)->select();
        if ($list) {
            foreach ($list as $item) {
                $item->delete();
            }
        }
        return true;
    }
}
