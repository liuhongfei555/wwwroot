<?php

namespace app\admin\controller\miniform;

use addons\miniform\library\Service;
use app\common\controller\Backend;

/**
 * Ajax
 *
 * @icon fa fa-circle-o
 * @internal
 */
class Ajax extends Backend
{

    /**
     * 模型对象
     */
    protected $model = null;
    protected $noNeedRight = ['*'];

    /**
     * 获取模板列表
     * @internal
     */
    public function get_template_list()
    {
        $files = [];
        $keyValue = $this->request->request("keyValue");
        if (!$keyValue) {
            $type = $this->request->request("type");
            $name = $this->request->request("name");
            if ($name) {
                //$files[] = ['name' => $name . '.html'];
            }
            //设置过滤方法
            $this->request->filter(['strip_tags']);
            $config = get_addon_config('miniform');
            $themeDir = ADDON_PATH . 'miniform' . DS . 'view' . DS . $config['theme'] . DS;
            $dh = opendir($themeDir);
            while (false !== ($filename = readdir($dh))) {
                if ($filename == '.' || $filename == '..') {
                    continue;
                }
                if ($type) {
                    $rule = $type == 'channel' ? '(channel|list)' : $type;
                    if (!preg_match("/^{$rule}(.*)/i", $filename)) {
                        continue;
                    }
                }
                $files[] = ['name' => $filename];
            }
        } else {
            $files[] = ['name' => $keyValue];
        }
        return $result = ['total' => count($files), 'list' => $files];
    }

    /**
     * 获取标题拼音
     */
    public function get_title_pinyin()
    {

        $title = $this->request->post("title");
        //分隔符
        $delimiter = $this->request->post("delimiter", "");
        $pinyin = new \Overtrue\Pinyin\Pinyin('Overtrue\Pinyin\MemoryFileDictLoader');
        if ($title) {
            $result = $pinyin->permalink($title, $delimiter);
            $this->success("", null, ['pinyin' => $result]);
        } else {
            $this->error(__('Parameter %s can not be empty', 'name'));
        }
    }

    /**
     * 获取表字段列表
     * @internal
     */
    public function get_fields_list()
    {
        $table = $this->request->request('table');
        $fieldList = Service::getTableFields($table);
        $this->success("", null, ['fieldList' => $fieldList]);
    }

    /**
     * 获取自定义字段列表HTML
     * @internal
     */
    public function get_fields_html()
    {
        $this->view->engine->layout(false);
        $source = $this->request->post('source');
        $id = $this->request->post('id/d');
        if (in_array($source, ['channel', 'page', 'special'])) {
            $values = \think\Db::name("miniform_{$source}")->where('id', $id)->find();
            $values = $values ? $values : [];

            $fields = \addons\miniform\library\Service::getCustomFields($source, 0, $values);

            $this->view->assign('fields', $fields);
            $this->view->assign('values', $values);
            $this->success('', null, ['html' => $this->view->fetch('miniform/common/fields')]);
        } else {
            $this->error(__('Please select type'));
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    public function get_page_list()
    {
        $pageList = [
            ['path' => 'https://www.baidu.com', 'name' => '外部链接'],
            ['path' => '/pages/index/index', 'name' => '主页'],
            ['path' => '/pages/my/my', 'name' => '个人中心'],
            ['path' => '/pages/detail/detail?id=1', 'name' => '项目详情'],
            ['path' => '/pages/detail/diyform?id=11', 'name' => '提交表单'],
            ['path' => '/pages/record/record?id=11', 'name' => '报名记录'],
            ['path' => '/pages/my/items', 'name' => '我的项目'],
            ['path' => '/pages/my/detail', 'name' => '我的项目详情'],
            ['path' => '/pages/my/agreement', 'name' => '用户协议'],
            ['path' => '/pages/signin/signin', 'name' => '签到'],
            ['path' => '/pages/signin/ranking', 'name' => '签到排行榜'],
            ['path' => '/pages/signin/logs', 'name' => '签到日志'],
            ['path' => '/pages/login/login', 'name' => '登录(账号密码)'],
            ['path' => '/pages/login/mobilelogin', 'name' => '登录(手机号)'],
            ['path' => '/pages/login/register', 'name' => '注册'],
            ['path' => '/pages/login/forgetpwd', 'name' => '忘记密码'],
        ];
        $this->view->assign('pageList', $pageList);
        return $this->view->fetch('miniform/common/pages');
    }
}
