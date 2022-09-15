<?php

namespace addons\miniform\model;

use think\Db;

class Fields extends \think\Model
{

    // 表名
    protected $name = 'miniform_fields';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        'content_list',
    ];

    protected $type = [
        'setting' => 'json',
    ];
    protected static $listFields = ['select', 'selects', 'checkbox', 'radio', 'array'];

    protected static $config = [];

    protected static function init()
    {
    }

    public function getExtendHtmlAttr($value, $data)
    {
        $result = preg_replace_callback("/\{([a-zA-Z]+)\}/", function ($matches) use ($data) {
            if (isset($data[$matches[1]])) {
                return $data[$matches[1]];
            }
        }, $data['extend']);
        return $result;
    }

    /**
     * 获取字典列表字段
     * @return array
     */
    public static function getListFields()
    {
        return self::$listFields;
    }

    public function getContentListAttr($value, $data)
    {
        return in_array($data['type'], self::$listFields) ? \app\common\model\Config::decode($data['content']) : $data['content'];
    }


    /**
     * 整理自定义项目字段数据
     * @param int $project_id 项目ID
     * @return array
     */
    public static function getProjectField($project_id)
    {
        $data = self::field('title,value,type,content,setting,defaultvalue')->where('source', 'project')->where('source_id', $project_id)->order('weigh desc')->select();
        $newData = [];
        foreach ($data as $item) {
            switch ($item['type']) {
                case 'files':
                case 'images':
                    $files = explode(',', $item['value']);
                    foreach ($files as &$file) {
                        $file = cdnurl($file, true);
                    }
                    $item['value'] = $files;
                    break;
                case 'file':
                case 'image':
                    $item['value'] = cdnurl($item['value'], true);
                    break;
                case 'array':
                    $item['value'] = json_decode($item['value'], true);
                    break;
                case 'selectpages':
                    $item['value'] = Db::table($item['setting']['table'])->where($item['setting']['primarykey'], 'IN', $item['value'])->column($item['setting']['field']);
                    break;
                case 'selectpage':
                    $item['value'] = Db::table($item['setting']['table'])->where($item['setting']['primarykey'], $item['value'])->value($item['setting']['field']);
                    break;
                case 'location':
                    $item['value'] = !empty($item['value']) ? json_decode($item['value'], true) : $item['value'];
                    break;
            }
            $setting = $item['setting'];
            unset($setting['table']);
            $item['setting'] = $setting;
            $newData[] = $item;
        }

        return $newData;
    }


    public static function validateByForm($source, $source_id, $param)
    {
        $fields = self::where('source', $source)->where('source_id', $source_id)->select();
        $rules = [];
        $msg = [];
        foreach ($fields as $item) {
            $rule = explode(',', $item['rule']);
            foreach ($rule as $res) {
                switch ($res) {
                    case 'required':
                        $rules["{$item['name']}"][] = 'require';
                        $msg["{$item['name']}.require"] = $item['title'] . '不能为空！';
                        break;
                    case 'digits':
                        $rules["{$item['name']}"][] = 'number';
                        $msg["{$item['name']}.number"] = $item['title'] . '必须是数字！';
                        break;
                    case 'letters':
                        $rules["{$item['name']}"][] = 'alpha';
                        $msg["{$item['name']}.alpha"] = $item['title'] . '必须是字母！';
                        break;
                    case 'date':
                        $rules[$item['name']][] = 'date';
                        $msg["{$item['name']}.date"] = $item['title'] . '格式错误！';
                        break;
                    case 'time':
                        $rules["{$item['name']}"][] = 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/';
                        $msg["{$item['name']}"] = $item['title'] . '格式错误！';
                        break;
                    case 'email':
                        $rules[$item['name']][] = 'email';
                        $msg["{$item['name']}.email"] = $item['title'] . '格式错误！';
                        break;
                    case 'url':
                        $rules[$item['name']] = 'url';
                        $msg["{$item['name']}.url"] = $item['title'] . '格式错误！';
                        break;
                    case 'qq':
                        $rules["{$item['name']}"][] = 'regex:/^[1-9][0-9]{4,10}$/';
                        $msg["{$item['name']}"] = $item['title'] . '格式错误！';
                        break;
                    case 'IDcard':
                        $rules["{$item['name']}"][] = 'regex:/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/';
                        $msg["{$item['name']}"] = $item['title'] . '格式错误！';
                        break;
                    case 'tel':
                        $rules["{$item['name']}"][] = 'regex:/^\d{3}-\d{8}$|^\d{4}-\d{7,8}$/';
                        $msg["{$item['name']}"] = $item['title'] . '格式错误！';
                        break;
                    case 'mobile':
                        $rules["{$item['name']}"][] = 'regex:/^1\d{10}$/';
                        $msg["{$item['name']}"] = $item['title'] . '格式错误！';
                        break;
                    case 'zipcode':
                        $rules["{$item['name']}"][] = 'regex:/^(0[1-7]|1[0-356]|2[0-7]|3[0-6]|4[0-7]|5[1-7]|6[1-7]|7[0-5]|8[013-6])\d{4}$/';
                        $msg["{$item['name']}"] = $item['title'] . '格式错误！';
                        break;
                    case 'chinese':
                        $rules[$item['name']][] = 'chs';
                        $msg["{$item['name']}.chs"] = $item['title'] . '只能填写中文！';
                        break;
                    case 'username':
                        $rules["{$item['name']}"][] = 'regex:/^[a-zA-Z0-9_]{3,12}$/';
                        $msg["{$item['name']}"] = $item['title'] . '请填写3-12位数字、字母、下划线！';
                        break;
                    case 'password':
                        $rules["{$item['name']}"][] = 'regex:/^[0-9a-zA-Z!@#$%^&*?]{6,16}$/';
                        $msg["{$item['name']}"] = $item['title'] . '请填写6-16位字符，不能包含空格！';
                        break;

                }
            }
        }
        $validate = new \think\Validate($rules, $msg);
        $return['status'] = $validate->check($param);
        $return['msg'] = $return['status'] ? '' : $validate->getError();
        return $return;
    }
}
