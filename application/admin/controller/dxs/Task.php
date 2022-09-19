<?php

namespace app\admin\controller\dxs;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Task extends Backend
{

    /**
     * Task模型对象
     * @var \app\admin\model\dxs\Task
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\dxs\Task;

        //项目分组信息
        $project_id = $this->request->param('project_id');
        $project = \app\admin\model\dxs\Project::get($project_id);

        
        $this->assignconfig('groupswitch', $project->groupswitch);
        $this->view->assign("groupswitch", $project->groupswitch);

      
        

        // $groupList = json_decode($project->groupjson);
        // var_dump($groupList);

        // $groupList = 
        // $this->assignconfig('groupList', $groupList);
        // $this->view->assign("groupList", $groupList);


       



    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function index()
    {
        $project_id = $this->request->param('project_id');
        
        
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->where('project_id', $project_id)
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);


            $result = array("total" => count($list->items()), "rows" => $list->items());
            return json($result);
        }

        $this->assignconfig('project_id', $project_id);
        $this->view->assign('project_id', $project_id);

        return $this->view->fetch();

    }

    /**
     * 添加
     */
    public function add()
    {
        $project_id = $this->request->param('project_id', 0);
        $this->view->assign('project_id', $project_id);
        return parent::add();
    }
   
}
