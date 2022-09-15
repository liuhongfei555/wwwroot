<?php

namespace addons\miniform\library;


use addons\miniform\model\Fields;
use fast\Http;
use think\Cache;
use think\Config;
use think\Db;
use think\Hook;

class Service
{

    /**
     * 推送消息通知
     * @param string $content     内容
     * @param string $type        类型
     * @param string $template_id 模板ID
     */
    public static function notice($content, $type, $template_id)
    {
        try {
            if ($type == 'dinghorn') {
                Hook::listen('msg_notice', $template_id, [
                    'content' => $content
                ]);
            } elseif ($type == 'vbot') {
                Hook::listen('vbot_send_msg', $template_id, [
                    'content' => $content
                ]);
            }
        } catch (\Exception $e) {

        }
    }


     /**
     * 获取表字段信息
     * @param string $table 表名
     * @return array
     */
    public static function getTableFields($table)
    {
        $tagName = "miniform-table-fields-{$table}";
        $fieldlist = Cache::get($tagName);
        if (!Config::get('app_debug') && $fieldlist) {
            return $fieldlist;
        }
        $dbname = Config::get('database.database');
        //从数据库中获取表字段信息
        $sql = "SELECT * FROM `information_schema`.`columns` WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION";
        //加载主表的列
        $columnList = Db::query($sql, [$dbname, $table]);
        $fieldlist = [];
        foreach ($columnList as $index => $item) {
            $fieldlist[] = ['name' => $item['COLUMN_NAME'], 'title' => $item['COLUMN_COMMENT'], 'type' => $item['DATA_TYPE']];
        }
        Cache::set($tagName, $fieldlist);
        return $fieldlist;
    }

    /**
     * 获取指定类型的自定义字段列表
     */
    public static function getCustomFields($subject_id, $values = [], $conditions = [])
    {
        $fields = Fields::where('subject_id', $subject_id)
            ->where($conditions)
            ->where('status', 'normal')
            ->order('weigh desc,id desc')
            ->select();
        foreach ($fields as $k => $v) {
            //优先取编辑的值,再次取默认值
            $v->value = isset($values[$v['name']]) ? $values[$v['name']] : (is_null($v['defaultvalue']) ? '' : $v['defaultvalue']);
            $v->rule = str_replace(',', '; ', $v->rule);
            if (in_array($v['type'], ['checkbox', 'lists', 'images'])) {
                $checked = '';
                if ($v['minimum'] && $v['maximum']) {
                    $checked = "{$v['minimum']}~{$v['maximum']}";
                } elseif ($v['minimum']) {
                    $checked = "{$v['minimum']}~";
                } elseif ($v['maximum']) {
                    $checked = "~{$v['maximum']}";
                }
                if ($checked) {
                    $v->rule .= (';checked(' . $checked . ')');
                }
            }
            if (in_array($v['type'], ['checkbox', 'radio']) && stripos($v->rule, 'required') !== false) {
                $v->rule = str_replace('required', 'checked', $v->rule);
            }
            if (in_array($v['type'], ['selects'])) {
                $v->extend .= (' ' . 'data-max-options="' . $v['maximum'] . '"');
            }
        }

        return $fields;
    }

}
