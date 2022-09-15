<?php

namespace addons\miniform\model;

use addons\epay\library\Service;
use think\Log;
use think\Model;
use Yansongda\Pay\Exceptions\GatewayException;
use think\Db;
use Yansongda\Pay\Pay;

class Order extends Model
{

    // 表名
    protected $name = 'miniform_order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'paytime_text',
        'status_text'
    ];

    public function getStatusList()
    {
        return ['created' => __('Status created'), 'paid' => __('Status paid'), 'expired' => __('Status expired'), 'refunded' => __('Status refunded')];
    }

    public function getPaytimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paytime']) ? $data['paytime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    /**
     * @param int   $project_id 项目ID
     * @param float $money      金额
     * @param int   $user_id    会员ID
     * @param int   $diyform_id 表单ID
     * @return mixed
     */
    public static function submitOrder($project_id, $money, $user_id, $diyform_id, $logs_id)
    {
        $request = \think\Request::instance();
        $orderid = date("Ymdhis") . sprintf("%08d", $user_id) . mt_rand(1000, 9999);
        $data = [
            'orderid'    => $orderid,
            'user_id'    => $user_id,
            'project_id' => $project_id,
            'diyform_id' => $diyform_id,
            'logs_id'    => $logs_id,
            'amount'     => $money,
            'payamount'  => 0,
            'ip'         => $request->ip(),
            'useragent'  => substr($request->server('HTTP_USER_AGENT'), 0, 255),
            'status'     => 'created'
        ];
        $order = self::create($data);
        return $order->orderid;
    }

    /**
     * 订单支付
     *
     * @param string $orderid   订单号
     * @param int    $user_id   会员ID
     * @param string $paytype   支付类型
     * @param string $method    支付方式
     * @param string $openid    Openid
     * @param string $notifyurl 通知地址
     * @param string $returnurl 返回地址
     * @return \addons\epay\library\Collection|\addons\epay\library\RedirectResponse|\addons\epay\library\Response|null
     */
    public static function pay($orderid, $user_id, $paytype = 'wechat', $method = 'web', $openid = '', $notifyurl = '', $returnurl = '')
    {
        $request = \think\Request::instance();
        $order = self::where('orderid', $orderid)->where('user_id', $user_id)->where('status', 'created')->find();
        if (!$order) {
            throw new \Exception('订单不存在，或已失效！');
        }

        $project = Project::where('id', $order->project_id)->find();
        if (!$project) {
            throw new \Exception('项目已失效！');
        }
        if ($project['people_num'] > 0 && $project['registered'] >= $project['people_num']) {
            //订单和报名数据应该取消
            throw new \Exception('报名人数已满！');
        }
        //支付方式变更
        if (($order['method'] && $order['paytype'] == $paytype && $order['method'] != $method)) {
            $orderid = date("Ymdhis") . sprintf("%08d", $user_id) . mt_rand(1000, 9999);
            $order->save(['orderid' => $orderid]);
        }

        //更新支付类型和方法
        $order->save(['paytype' => $paytype, 'method' => $method]);

        $response = null;

        $epay = get_addon_info('epay');

        if ($epay && $epay['state']) {

            $notifyurl = $notifyurl ? $notifyurl : $request->root(true) . '/addons/miniform/pay/epay/type/notify/paytype/' . $paytype;
            $returnurl = $returnurl ? $returnurl : $request->root(true) . '/addons/miniform/pay/epay/type/return/paytype/' . $paytype . '/order_id/' . $orderid;

            //保证取出的金额一致，不一致将导致订单重复错误
            $amount = sprintf("%.2f", $order->amount);

            $params = [
                'amount'    => $amount,
                'orderid'   => $orderid,
                'type'      => $paytype,
                'title'     => "支付{$amount}元",
                'notifyurl' => $notifyurl,
                'returnurl' => $returnurl,
                'method'    => $method,
                'openid'    => $openid,
                'buyer_id'  => $openid
            ];
            // print_r($params);exit;
            try {
                $response = Service::submitOrder($params);
            } catch (GatewayException $e) {
                throw new \Exception(config('app_debug') ? $e->getMessage() : "支付失败，请稍后重试");
            }
        } else {
            throw new \Exception("请在后台安装配置微信支付宝整合插件");
        }
        return $response;
    }

    /**
     * 订单结算
     *
     */
    public static function settle($orderid, $payamount)
    {
        $order = Order::with('project')->where('orderid', $orderid)->find();
        if (!$order) {
            return false;
        }
        if ($payamount != $order->amount) {
            \think\Log::write("[miniform][pay][{$orderid}][订单支付金额不一致]");
            return false;
        }

        Db::startTrans();
        try {
            //更新订单表
            $time = time();

            $order->payamount = $payamount;
            $order->paytime = $time;
            $order->status = 'paid';
            $order->save();

            //更新表单
            $model = new Diyform();
            $diyform = $model->setName($order->project->getData('table'))->where('id', $order->diyform_id)->find();
            //存在
            if ($diyform) {
                $diyform->status = 'paid';
                $diyform->save();
            }
            //更新日志
            Logs::where('id', $order->logs_id)->update(['status' => 'normal', 'updatetime' => time()]);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }

    /**
     * 后台订单结算
     *
     */
    public static function backSettle($orderid, $status)
    {
        $order = Order::with('project')->where('orderid', $orderid)->find();
        if (!$order) {
            return false;
        }
        $config = get_addon_config('miniform');
        Db::startTrans();
        try {
            //更新表单
            $model = new Diyform();
            $diyform = $model->setName($order->project->getData('table'))->where('id', $order->diyform_id)->find();
            //存在
            if ($diyform) {
                if ($status == 'paid') {
                    $diyform->status = 'paid';
                } elseif ($status == 'refunded') {
                    $diyform->status = 'canceled';
                } else {
                    $diyform->status = 'nonpayment';
                }
                $diyform->save();
            }
            //更新日志
            $log_data = [];
            if ($status == 'paid') {
                $log_data['status'] = 'normal';
            } elseif ($status == 'refunded') {
                $log_data['status'] = 'canceled';
                //设置活动参与人数
                $order->project->registered > 0 && $order->project->setDec('registered');
            } else {
                $log_data['status'] = 'nonpayment';
            }
            Logs::where('id', $order->logs_id)->update($log_data);
            if ($status == 'refunded' && $order->payamount > 0 && $config['order_refund_sync']) {
                //执行同步退款
                self::refund($order->orderid, $order->paytype, $order->payamount);
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }

    /**
     * 退款
     */
    public static function refund($orderid, $paytype, $payamount)
    {
        $config = Service::getConfig($paytype);
        try {
            $order = Order::getByOrderid($orderid);
            if ($paytype == 'wechat') {
                $response = Pay::wechat($config)->refund([
                    'type'          => in_array($order['method'], ['miniapp', 'app']) ? $order['method'] : '',
                    'out_trade_no'  => $orderid,
                    'out_refund_no' => time(),
                    'total_fee'     => $payamount * 100,
                    'refund_fee'    => $payamount * 100
                ]);
                if (!$response['result_code'] || $response['result_code'] != 'SUCCESS') {
                    throw new \Exception(($response['err_code'] ?? '') . ':' . ($response['err_code_des'] ?? '未知退款错误'));
                }
            } elseif ($paytype == 'alipay') {
                $response = Pay::alipay($config)->refund([
                    'out_trade_no'  => $orderid,
                    'refund_amount' => $payamount,
                ]);
                if (!$response['code'] || $response['code'] != '10000') {
                    throw new \Exception(($response['code'] ?? '') . ':' . ($response['msg'] ?? '未知退款错误'));
                }
            }
        } catch (\Exception $e) {
            Log::write("[miniform][refund][{$orderid}]同步退款失败，失败原因：" . $e->getMessage());
            throw new \Exception("同步退款失败，失败原因：" . $e->getMessage());
        }
        return true;
    }

    public function project()
    {
        return $this->hasOne('Project', 'id', 'project_id', [], 'LEFT');
    }

    public function logs()
    {
        return $this->hasOne('Logs', 'id', 'logs_id', [], 'LEFT');
    }

}
