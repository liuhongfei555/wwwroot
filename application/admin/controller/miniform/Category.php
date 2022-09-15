<?php

namespace app\admin\controller\miniform;

use app\common\controller\Backend;

/**
 * 分类
 *
 * @icon fa fa-circle-o
 */
class Category extends Backend
{

    /**
     * Category模型对象
     * @var \app\admin\model\miniform\Category
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\miniform\Category;
    }

    public function import()
    {
        parent::import();
    }
}
