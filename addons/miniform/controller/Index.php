<?php

namespace addons\miniform\controller;

use \addons\miniform\model\Project;
use \addons\miniform\model\Fields;
use \addons\miniform\model\Diyform;
use \addons\miniform\model\Order;
use \addons\miniform\model\Logs;
use think\Db;

class Index extends Base
{

    protected $noNeedLogin = ['project', 'projectInfo', 'diyform', 'postForm'];

    /**
     * 项目列表
     */
    public function project()
    {
        $param = $this->request->param();
        $limit = 10;
        if (isset($param['limit'])) {
            $limit = $param['limit'];
        }
        $data = Project::field('table,applyfields', true)->where(function ($query) use ($param) {
            $query->where('status', 'normal');
            if (isset($param['category_id']) && !empty($param['category_id'])) {
                $query->where('category_id', $param['category_id']);
            }
            if (isset($param['title']) && !empty($param['title'])) {
                $query->where('title', 'like', '%' . $param['title'] . '%');
            }
            if (isset($param['keyword']) && !empty($param['keyword'])) {
                $query->where('title', 'like', '%' . $param['keyword'] . '%');
            }
        })->order('id desc')->paginate($limit);
        foreach ($data as $res) {
            $res->append(['image', 'images_text']);
            $res->hidden(['images', 'createtime', 'updatetime', 'deletetime', 'table', 'begintime', 'endtime']);
        }
        $this->success('', $data);
    }

    /**
     * 项目详情
     */
    public function projectInfo()
    {
        $id = $this->request->param('id');
        $project = Project::get($id);
        if (!$project) {
            $this->error('记录未找到');
        }
        if ($project['status'] == 'hidden') {
            $this->error('记录暂不可用');
        }
        $fields = Fields::getProjectField($id);
        if ($project['is_signin']) {
            $fields = array_merge([
                ['title' => '签到时间', 'value' => $project['signin_time'], 'type' => 'datetimerange'],
                ['title' => '签到地点', 'value' => $project['signin_name'], 'type' => 'location']
            ], $fields);
        }
        $project->setInc('views');
        $project->append(['images_text', 'surplus_time']);
        $project->hidden(['images', 'createtime', 'updatetime', 'deletetime', 'table', 'begintime', 'endtime']);
        $this->success('', ['info' => $project, 'fields' => $fields]);
    }

    /**
     * 表单字段获取
     */
    public function diyform()
    {
        $id = $this->request->param('id');
        $project = Project::get($id);
        if (!$project) {
            $this->error('记录未找到');
        }
        if (($project->is_need_login || $project->price > 0) && !$this->auth->isLogin()) {
            $this->error(__('请登录后再操作'), null, 401);
        }
        $fields = Fields::where('source', $project['table'])
            ->where('source_id', $id)
            ->where('isshowfront', 1)
            ->field('createtime,isshowback,isshowfront,updatetime', true)
            ->order('weigh DESC,id DESC')->select();
        foreach ($fields as $item) {
            $setting = $item['setting'];
            unset($setting['table']);
            $item['setting'] = $setting;
        }
        $this->success('', [
            'info'   => $project,
            'fields' => $fields
        ]);
    }

    /**
     * 表单数据提交
     */
    public function postForm()
    {
        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }
        $project_id = $this->request->post('project_id/d');
        $project = Project::lock(true)->where('id', $project_id)->find();
        if (!$project) {
            $this->error('记录未找到');
        }
        if ($project->status != 'normal') {
            $this->error('项目已禁用');
        }
        if (($project->is_need_login || $project->price > 0) && !$this->auth->isLogin()) {
            $this->error('请先登录');
        }
        $captcha = $this->request->param('captcha');
        if ($project->iscaptcha && !captcha_check($captcha, $project_id)) {
            $this->error('验证码不正确');
        }
        $msg = '添加成功';
        $param = $this->request->post('', '', 'trim,xss_clean');
        //校验数据
        $result = Fields::validateByForm($project['table'], $project_id, $param);
        if (!$result['status']) {
            $this->error($result['msg']);
        }
        $tiem = time();
        if ($project['begintime'] > $tiem) {
            $this->error('项目未开始');
        }
        if ($project['endtime'] <= $tiem) {
            $this->error('项目已结束');
        }
        if ($project['people_num'] > 0 && $project['registered'] >= $project['people_num']) {
            $this->error('报名人数已满！');
        }
        $param['user_id'] = $this->auth->id;
        $diyform = new Diyform();
        $diyform->setName($project['table']);
        //是否已提交
        $data = $diyform->where('user_id', $this->auth->id)->find();
        if (!empty($data) && !$project->is_multi) {
            $this->error('不能重复提交数据！');
        }
        //付费的
        if ($project->price > 0) {
            $param['status'] = 'nonpayment';
        } else {
            $param['status'] = 'free';
            $msg = ($project['label'] ? $project['label'] : '提交') . '成功';
        }

        $logs = null;
        $orderid = '';

        Db::startTrans();
        try {
            //新增
            $diyform->allowField(true)->save($param);
            //统一做一个主表日志
            $logs = Logs::create([
                'user_id'    => $this->auth->id,
                'diyform_id' => $diyform->id,
                'project_id' => $project_id,
                'status'     => $project->price > 0 ? 'nonpayment' : 'normal'
            ]);
            //如果有价格，插入订单，去支付订单
            if ($project->price > 0) {
                $orderid = Order::submitOrder($project_id, $project->price, $this->auth->id, $diyform->id, $logs->id);
            }
            $logs->project->setInc('registered');
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success($msg, ['id' => $logs->id, 'orderid' => $orderid]);
    }

    /**
     * 我的项目
     */
    public function myProject()
    {
        $param = $this->request->param();
        $param['user_id'] = $this->auth->id;

        $limit = 15;
        $config = get_addon_config('miniform');
        if (isset($param['limit'])) {
            $limit = $param['limit'];
        }

        $data = Logs::with([
            'project'   => function ($query) {
                $query->field('id,title,table,images,registered,is_signin,signin_time,signin_name,begintime,endtime');
            },
            'orderinfo' => function ($query) {
                $query->field('id,orderid,logs_id,createtime,amount,status');
            },
            'Subscribe' => function ($query) {
                $query->field('id,logs_id')->where('status', 0);
            }
        ])->where(function ($query) use ($param) {
            if (isset($param['user_id']) && !empty($param['user_id'])) {
                $query->where('user_id', $param['user_id']);
            }
        })->order('id desc')->paginate($limit);
        // 启动事务
        Db::startTrans();
        try {
            foreach ($data as $res) {
                $res->order = $res->orderinfo;

                //超过设置订单状态为过期
                if ($res->order && $res->order->status == 'created' && time() - $res->order->createtime > $config['order_timeout']) {
                    $res->order->save(['status' => 'expired']);
                    $res->allowField(true)->save(['status' => 'canceled']);
                    $res->project && $res->project->registered > 0 && $res->project->setDec('registered');
                }

                if ($res->project) {
                    $res->project->append(['image', 'open_signin']);
                    $res->project->hidden(['images', 'createtime', 'updatetime', 'deletetime', 'table', 'begintime', 'endtime']);
                }
                Logs::getIsSignin($res);
                unset($res->orderinfo);
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        $this->success('', $data);
    }

    /**
     * 报名用户
     */
    public function projectUser()
    {
        $id = $this->request->param('id');
        $project = Project::get($id);
        if (!$project) {
            $this->error('记录未找到');
        }
        $diyform = new Diyform();
        $diyform->setName($project['table']);

        $formList = $diyform->field('id,user_id,createtime')->with([
            'user' => function ($query) {
                $query->field('id,nickname,avatar');
            }
        ])->order('id desc')->where('status', 'in', ['free', 'paid'])->paginate(15);
        foreach ($formList as $item) {
            $item->createtime = date('Y-m-d H:i:s', $item->createtime);
            if ($item->user) {
                $item->user->avatar = cdnurl($item->user->avatar, true);
            }
        }
        $this->success('', $formList);
    }

    /**
     * 项目日志详情
     */
    public function logDetail()
    {
        $id = $this->request->param('id/d');
        $logs = Logs::get($id, ['project']);
        if (!$logs) {
            $this->error('记录未找到');
        }
        if ($logs['user_id'] != $this->auth->id) {
            $this->error('无法进行越权操作');
        }
        if (!$logs->project) {
            $this->error('项目已被删除！');
        }
        $diyform = new Diyform();
        $diyform->setName($logs->project->getData('table'));
        $formDetail = $diyform->where('id', $logs['diyform_id'])->find();
        if (!$formDetail) {
            $this->error('记录未找到');
        }
        //查找字段，匹配值输出
        $fields = Fields::where('source', $logs->project->getData('table'))
            ->where('source_id', $logs['project_id'])
            ->where('isshowfront', 1)
            ->field('type,setting,value,content,name,title')
            ->order('weigh DESC,id DESC')
            ->select();
        foreach ($fields as $item) {
            $setting = $item['setting'];
            if ($item['type'] == 'images' || $item['type'] == 'files') {
                $das = explode(',', $formDetail[$item['name']]);
                foreach ($das as &$res) {
                    $res = cdnurl($res, true);
                }
                unset($res);
                $item['value'] = $das;
            } else if ($item['type'] == 'image' || $item['type'] == 'file') {
                $item['value'] = cdnurl($formDetail[$item['name']], true);
            } elseif ($item['type'] == 'selectpage') {
                $item['value'] = Db::table($setting['table'])->where($setting['primarykey'], $formDetail[$item['name']])->value($setting['field']);
            } elseif ($item['type'] == 'selectpages') {
                $item['value'] = Db::table($setting['table'])->where($setting['primarykey'], 'IN', $formDetail[$item['name']])->column($setting['field']);
            } elseif ($item['type'] == 'array') {
                $item['value'] = json_decode($formDetail[$item['name']], true);
            } elseif ($item['type'] == 'location') {
                $address = '';
                $lat = 0;
                $lng = 0;
                if (preg_match("/(.*)\,([0-9\.]+),([0-9\.]+)\$/i", $formDetail[$item['name']], $matches)) {
                    $address = $matches[1];
                    $lat = $matches[2];
                    $lng = $matches[3];
                }
                $item['value'] = ['address' => $address, 'lat' => $lat, 'lng' => $lng];
            } else {
                $item['value'] = $formDetail[$item['name']];
            }

            unset($setting['table']);
            $item['setting'] = $setting;
        }

        $project = Project::field('title,is_verification')->where('id', $logs['project_id'])->find();
        $project['verification_text'] = $logs['status'] == 'normal' ? $logs['verification_text'] : '';
        $project['verification_status'] = isset($formDetail['verificationtime']) && !empty($formDetail['verificationtime']);
        $project['logs_status'] = $logs['status'];
        $project['logs_status_text'] = $logs['status_text'];

        $this->success('', [
            'forminfo' => $fields,
            'project'  => $project
        ]);
    }

    /**
     * 取消项目
     */
    public function cancel()
    {
        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }
        $id = $this->request->param('id/d');
        $logs = Logs::get($id, ['project', 'orderinfo']);
        if (empty($logs)) {
            $this->error('记录不存在！');
        }
        if ($logs['status'] == 'canceled') {
            $this->error('项目已取消！');
        }
        if ($logs['user_id'] != $this->auth->id) {
            $this->error('无法进行越权操作');
        }
        $diyform = new Diyform();
        $diyform->setName($logs->project->getData('table'));
        $formData = $diyform->where('id', $logs->diyform_id)->find();
        if (!$formData) {
            $this->error('未找到指定项目表单数据');
        }
        if ($formData && !empty($formData->signintime)) {
            $this->error('项目已签到，无法进行取消');
        }
        Db::startTrans();
        try {
            $order = null;
            if ($logs->orderinfo) {
                $order = $logs->orderinfo;
                //已支付的订单，状态为申请
            }
            if ($order && $order['status'] == 'paid') {
                $logs->status = 'refunding'; //申请取消
                $logs->save();
                $order->status = 'refunding'; //申请取消
                $order->save();
            } else {
                if ($order) {
                    $order->status = 'expired';
                    $order->save();
                }
                //日志
                $logs->status = 'canceled';
                $logs->save();
                //更新活动参加人数
                $logs->project->registered > 0 && $logs->project->setDec('registered');
                //表单
                $formData->status = 'canceled';
                $formData->save();
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error($e->getMessage());
        }

        $this->success('取消成功！');
    }

    /**
     * 项目签到
     */
    public function signin()
    {
        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }
        $lng1 = $this->request->param('longitude');
        $lat1 = $this->request->param('latitude');
        $id = (int)$this->request->param('id');
        $logs = Logs::get($id, ['project']);
        if (!$logs) {
            $this->error('记录未找到');
        }
        if (!$logs->project) {
            $this->error('项目已失效！');
        }
        $project = $logs->project;
        if (!$project['is_signin']) {
            $this->error('未开启签到功能');
        }
        $timerange = explode(' - ', $project['signin_time']);
        if (count($timerange) == 2) {
            $time1 = strtotime($timerange[0]);
            $time2 = strtotime($timerange[1]);
        }
        $time = time();
        if ($time < $time1) {
            $this->error('签到未开始！');
        }
        if ($time >= $time2) {
            $this->error('签到已结束！');
        }
        //判断签到距离
        $location = $project['signin_name'];
        $lng2 = $location['lng'] ? $location['lng'] : 0;
        $lat2 = $location['lat'] ? $location['lat'] : 0;
        $config = get_addon_config('miniform');
        //签到范围判断
        $radLat1 = $lat1 * M_PI / 180.0;
        $radLat2 = $lat2 * M_PI / 180.0;
        $c = 2 * asin(sqrt(pow(sin(($radLat1 - $radLat2) / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin((($lng1 * M_PI / 180.0) - ($lng2 * M_PI / 180.0)) / 2), 2)));
        $distance = round($c * 6378.137 * 1000, 2); //米
        if (false && $distance > $config['signin_distance']) {
            $this->error('签到失败,请到签到地点再签到！', $distance);
        }
        //记录信息
        try {
            (new Diyform)->setName($project->getData('table'))->save([
                'signintime' => time()
            ], ['id' => $logs->diyform_id]);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('签到成功！', $distance);
    }
}
