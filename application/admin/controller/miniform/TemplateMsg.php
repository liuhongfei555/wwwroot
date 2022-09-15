<?php

namespace app\admin\controller\miniform;

use app\common\controller\Backend;

/**
 * 模板消息
 *
 * @icon fa fa-circle-o
 */
class TemplateMsg extends Backend
{

    /**
     * TemplateMsg模型对象
     * @var \app\admin\model\miniform\TemplateMsg
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\miniform\TemplateMsg;
        $this->assign('identList', $this->model->getIdentList());
        $this->assign('template_data', json_encode([
            'orderid'          => '订单编号',
            'nickname'         => '用户昵称',
            'title'            => '项目名称',
            'registered'       => '参与人数',
            'begintime'        => '项目开始时间',
            'endtime'          => '项目结束时间',
            'ordercreatetime'  => '下单时间',
            'amount'           => '订单金额',
            'signin_name'      => '签到地点',
            'signin_begintime' => '签到开始时间',
            'signin_endtime'   => '签到结束时间',
            'diy_text'         => '自定义文本',
        ]));
    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
}
