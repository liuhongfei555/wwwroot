<?php

namespace app\admin\controller\miniform;

use app\common\controller\Backend;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class Order extends Backend
{

    /**
     * Order模型对象
     * @var \app\admin\model\miniform\Order
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\miniform\Order;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model->with(['project', 'user'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $index => $item) {
                if ($item->user) {
                    $item->user->visible(['nickname']);
                }
                if ($item->project) {
                    $item->project->visible(['title']);
                }
            }
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    public function import()
    {
        parent::import();
    }

    public function refunded()
    {
        $id = $this->request->param('ids');
        if (!$id) {
            $this->error('参数错误');
        }
        $order = $this->model->get($id);
        if (!$order) {
            $this->error('订单不存在');
        }
        $config = \addons\epay\library\Service::getConfig($order['paytype']);
        $response = null;
        try {
            if ($order['paytype'] == 'wechat') {
                $response = \Yansongda\Pay\Pay::wechat($config)->find([
                    'type'         => in_array($order['method'], ['miniapp', 'app']) ? $order['method'] : '',
                    'out_trade_no' => $order['orderid']
                ], 'refund');
            } elseif ($order['paytype'] == 'alipay') {
                $response = \Yansongda\Pay\Pay::alipay($config)->find([
                    'out_trade_no'   => $order['orderid'],
                    'out_request_no' => $order['orderid']
                ], 'refund');
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        if ($order['paytype'] == 'wechat') {
            if ($response && $response['return_msg'] == 'OK') {
                $this->success('成功退款：' . $response->refund_fee / 100 . '元。');
            } else {
                $this->error('查询失败');
            }
        } elseif ($order['paytype'] == 'alipay') {
            if ($response && $response['msg'] == 'Success') {
                $this->success('成功退款：' . $response->refund_amount . '元。');
            } else {
                $this->error('查询失败');
            }
        } else {
            $this->error("未知支付类型");
        }
    }
}
