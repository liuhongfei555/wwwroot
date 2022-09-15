<?php

namespace addons\miniform\controller;

use \addons\miniform\model\Order;
use addons\third\model\Third;

class Pay extends Base
{

    protected $noNeedLogin = ['epay'];

    /**
     * 订单信息
     */
    public function order()
    {
        if (!$this->request->isPost()) {
            $this->error("请求错误");
        }
        $config = get_addon_config('miniform');
        $id = $this->request->param('id');
        if (!$id) {
            $this->error('参数错误！');
        }

        $order = Order::with([
            'project' => function ($query) {
                $query->field('id,title,images');
            }
        ])->field('id,orderid,logs_id,project_id,createtime,status,amount')
            ->where('logs_id', $id)
            ->where('user_id', $this->auth->id)
            ->find();

        if (!$order) {
            $this->error('订单不存在或已失效');
        }
        if ($order['status'] == 'paid') {
            $this->error('订单已支付', $order);
        }
        if ($order['status'] != 'created') {
            $this->error('订单已失效', $order);
        }
        //超过设置订单状态为过期
        if ($order['status'] == 'created' && time() - $order->createtime > $config['order_timeout']) {
            $order->save(['status' => 'expired']);
            $order->logs->save(['status' => 'canceled']);
            //设置活动参与人数
            $order->project && $order->project->registered>0 && $order->project->setDec('registered');
        }
        if ($order && $order->project) {
            $order->project->hidden(['images']);
            $order->project->append(['image']);
        }
        $this->success('', $order);
    }

    /**
     * 创建订单并支付
     */
    public function pay()
    {
        if (!$this->request->isPost()) {
            $this->error("请求错误");
        }
        $id = $this->request->post('id');
        $paytype = $this->request->post('paytype');
        $method = $this->request->post('method');
        $appid = $this->request->post('appid'); //为APP的应用id
        $returnurl = $this->request->post('returnurl', '', 'trim');
        $openid = '';

        $config = get_addon_config('miniform');

        $order = Order::with([
            'project' => function ($query) {
                $query->field('id,title,images');
            }
        ])->field('id,orderid,logs_id,project_id,createtime,status,amount')
            ->where('logs_id', $id)
            ->where('user_id', $this->auth->id)
            ->find();

        if (!$order) {
            $this->error('订单不存在或已失效');
        }
        if ($order['status'] == 'paid') {
            $this->error('订单已支付', $order);
        }
        if ($order['status'] != 'created') {
            $this->error('订单已失效', $order);
        }

        //超过设置订单状态为过期
        if ($order['status'] == 'created' && time() - $order->createtime > $config['order_timeout']) {
            $order->save(['status' => 'expired']);
            $order->logs->save(['status' => 'canceled']);
            $order->project && $order->project->registered > 0 && $order->project->setDec('registered');
        }

        //非H5 支付,非余额
        if (in_array($method, ['miniapp', 'mp', 'mini'])) {

            $info = get_addon_info('third');
            if (!$info || !$info['state']) {
                $this->error("请在后台安装第三方登录插件");
            }
            $third = Third::where('platform', $paytype)
                ->where('apptype', $method)
                ->where('user_id', $this->auth->id)
                ->find();

            if (!$third) {
                $this->error("未找到第三方用户信息", 'bind');
            }
            $openid = $third['openid'];
        }
        try {
            $response = Order::pay($order->orderid, $this->auth->id, $paytype, $method, $openid, '', $returnurl);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success("请求成功", $response);
    }

    /**
     * 支付回调
     */
    public function epay()
    {
        $type = $this->request->param('type');
        $paytype = $this->request->param('paytype');
        if ($type == 'notify') {
            $pay = \addons\epay\library\Service::checkNotify($paytype);
            if (!$pay) {
                echo '签名错误';
                return;
            }
            $data = $pay->verify();
            try {
                $payamount = $paytype == 'alipay' ? $data['total_amount'] : $data['total_fee'] / 100;
                \addons\miniform\model\Order::settle($data['out_trade_no'], $payamount);
            } catch (\Exception $e) {
                \think\Log::write($e->getMessage(), 'epay');
            }
            echo $pay->success();
        } else {
            //你可以在这里定义你的提示信息,但切记不可在此编写逻辑
            //$this->success("恭喜你！支付成功!", $order->archives->url);
        }
        return;
    }
}
