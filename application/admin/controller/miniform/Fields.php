<?php

namespace app\admin\controller\miniform;

use app\common\controller\Backend;
use app\common\model\Config;


/**
 * 表单字段管理
 *
 * @icon fa fa-circle-o
 */
class Fields extends Backend
{

    /**
     * Subject模型对象
     * @var \app\admin\model\miniform\Fields
     */
    protected $model = null;

    protected $noNeedRight = ['rulelist'];
    protected $multiFields = 'isfilter,isshowback';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\miniform\Fields;
        $typeList = Config::getTypeList();
        unset($typeList['custom']);
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign('typeList', $typeList);
        $this->view->assign('regexList', Config::getRegexList());
    }

    /**
     * 查看
     */
    public function index()
    {
        $source_id = $this->request->param('source_id', 0);
        $source = $this->request->param('source', 'project');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->where('source_id', $source_id)
                ->where('source', $source)
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            if ($source == 'project') {
                $subject = \app\admin\model\miniform\Project::get($source_id);
                $fieldList = [
                    ['name' => 'title', 'title' => $subject->title . '名称', 'type' => 'string'],
                    ['name' => 'images', 'title' => $subject->title . '图片组', 'type' => 'images'],
                    ['name' => 'content', 'title' => $subject->title . '内容', 'type' => 'text'],
                ];
                foreach ($fieldList as $index => $field) {
                    $item = [
                        'id'         => 0,
                        'state'      => false,
                        'source_id'  => $source_id,
                        'name'       => $field['name'],
                        'title'      => $field['title'],
                        'type'       => $field['type'],
                        'issystem'   => true,
                        'isfilter'   => 0,
                        'isorder'    => 0,
                        'status'     => 'normal',
                        'createtime' => 0,
                        'updatetime' => 0
                    ];
                    $list[] = $item;
                }
            }

            $result = array("total" => count($list->items()), "rows" => $list->items());

            return json($result);
        }

        $this->assignconfig('source_id', $source_id);
        $this->assignconfig('source', $source);

        $this->view->assign('source_id', $source_id);
        $this->view->assign('source', $source);

        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        $source_id = $this->request->param('source_id', 0);
        $source = $this->request->param('source', '');
        $this->view->assign('source_id', $source_id);
        $this->view->assign('source', $source);
        $this->renderTable();
        return parent::add();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $this->renderTable();
        return parent::edit($ids);
    }

    /**
     * 渲染表
     */
    protected function renderTable()
    {
        $tableList = [];
        $dbname = \think\Config::get('database.database');
        $list = \think\Db::query("SELECT `TABLE_NAME`,`TABLE_COMMENT` FROM `information_schema`.`TABLES` where `TABLE_SCHEMA` = '{$dbname}';");
        foreach ($list as $key => $row) {
            $tableList[$row['TABLE_NAME']] = $row['TABLE_COMMENT'];
        }
        $this->view->assign("tableList", $tableList);
    }


    /**
     * 批量操作
     * @param string $ids
     */
    public function multi($ids = "")
    {
        $params = $this->request->request('params');
        parse_str($params, $paramsArr);
        if (isset($paramsArr['isfilter'])) {
            $field = \app\admin\model\miniform\Fields::get($ids);
            if (!$field || !in_array($field['type'], ['radio', 'checkbox', 'select', 'selects', 'array'])) {
                $this->error('只有类型为单选、复选、下拉列表、数组才可以加入列表筛选');
            }
        }
        if (isset($paramsArr['isshowback']) && !is_numeric($ids)) {
            if (!$ids || !in_array($ids, ["nickname", "intro", "content", "image"])) {
                $this->error('参数错误');
            }
            $source_id = $this->request->param('source_id', 0);
            $model = \app\admin\model\miniform\Project::get($source_id);
            if (!$model) {
                $this->error("模型未找到");
            }
            $setting = $model['setting'];
            $contributefields = $setting['contributefields'] ?? [];
            if ($paramsArr['isshowback']) {
                $contributefields[] = $ids;
            } else {
                $contributefields = array_values(array_diff($contributefields, [$ids]));
            }
            $setting['contributefields'] = $contributefields;
            $model->setting = $setting;
            $model->save();
            $this->success("");
        }
        return parent::multi($ids);
    }

    /**
     * 规则列表
     * @internal
     */
    public function rulelist()
    {
        //主键
        $primarykey = $this->request->request("keyField");
        //主键值
        $keyValue = $this->request->request("keyValue", "");

        $keyValueArr = array_filter(explode(',', $keyValue));
        $regexList = Config::getRegexList();
        $list = [];
        foreach ($regexList as $k => $v) {
            if ($keyValueArr) {
                if (in_array($k, $keyValueArr)) {
                    $list[] = ['id' => $k, 'name' => $v];
                }
            } else {
                $list[] = ['id' => $k, 'name' => $v];
            }
        }
        return json(['list' => $list]);
    }

}
