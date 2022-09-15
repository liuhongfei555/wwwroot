<?php

namespace app\admin\model\miniform;

use think\Model;


class TemplateMsg extends Model
{


    // 表名
    protected $name = 'miniform_template_msg';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];


    public function getIdentList()
    {
        return ['0' => __('Ident 0'), '1' => __('Ident 1'), '2' => __('Ident 2')];
    }
}
