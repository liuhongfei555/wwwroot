<?php

namespace app\admin\controller\miniform;

use app\common\controller\Backend;
use app\admin\model\User;

/**
 *
 */
class Report extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        try {
            \think\Db::execute("SET @@sql_mode='';");
        } catch (\Exception $e) {
        }

        if ($this->request->isPost()) {

            $date = $this->request->post('date', '');
            $type = (int)$this->request->post('type', '0');

            switch ($type) {
                case 0:
                    list($orderSaleCategory, $orderSaleAmount, $orderSaleNums) = $this->getSaleOrderData($date);
                    $statistics = ['orderSaleCategory' => $orderSaleCategory, 'orderSaleAmount' => $orderSaleAmount, 'orderSaleNums' => $orderSaleNums];
                    $this->success('', '', $statistics);
                    break;
            }
        }

        //今日订单和会员
        $totalOrderAmount = round(\app\admin\model\miniform\Order::where('status', 'paid')->where('paytime', '>', 0)->sum('payamount'), 2);
        $yesterdayOrderAmount = round(\app\admin\model\miniform\Order::where('status', 'paid')->whereTime('paytime', 'yesterday')->sum('payamount'), 2);
        $todayOrderAmount = round(\app\admin\model\miniform\Order::where('status', 'paid')->whereTime('paytime', 'today')->sum('payamount'), 2);
        $todayOrderRatio = $yesterdayOrderAmount > 0 ? ceil((($todayOrderAmount - $yesterdayOrderAmount) / $yesterdayOrderAmount) * 100) : ($todayOrderAmount > 0 ? 100 : 0);

        $totalUser = User::count();
        $yesterdayUser = User::whereTime('jointime', 'yesterday')->count();
        $todayUser = User::whereTime('jointime', 'today')->count();
        $todayUserRatio = $yesterdayUser > 0 ? ceil((($todayUser - $yesterdayUser) / $yesterdayUser) * 100) : ($todayUser > 0 ? 100 : 0);


        $this->view->assign("totalOrderAmount", $totalOrderAmount);
        $this->view->assign("yesterdayOrderAmount", $yesterdayOrderAmount);
        $this->view->assign("todayOrderAmount", $todayOrderAmount);
        $this->view->assign("todayOrderRatio", $todayOrderRatio);

        $this->view->assign("totalUser", $totalUser);
        $this->view->assign("yesterdayUser", $yesterdayUser);
        $this->view->assign("todayUser", $todayUser);
        $this->view->assign("todayUserRatio", $todayUserRatio);

        //订单数和订单额统计
        list($orderSaleCategory, $orderSaleAmount, $orderSaleNums) = $this->getSaleOrderData();
        $this->assignconfig('orderSaleCategory', $orderSaleCategory);
        $this->assignconfig('orderSaleAmount', $orderSaleAmount);
        $this->assignconfig('orderSaleNums', $orderSaleNums);


        return $this->view->fetch();
    }


    /**
     * 获取订单销量销售额统计数据
     */
    protected function getSaleOrderData($date = '')
    {

        if ($date) {
            list($start, $end) = explode(' - ', $date);
            $starttime = strtotime($start);
            $endtime = strtotime($end);
        } else {
            $starttime = \fast\Date::unixtime('day', 0, 'begin');
            $endtime = \fast\Date::unixtime('day', 0, 'end');
        }
        $totalseconds = $endtime - $starttime;
        $format = '%Y-%m-%d';
        if ($totalseconds > 86400 * 30 * 2) {
            $format = '%Y-%m';
        } else {
            if ($totalseconds > 86400) {
                $format = '%Y-%m-%d';
            } else {
                $format = '%H:00';
            }
        }

        $orderList = \app\admin\model\miniform\Order::where('paytime', 'between time', [$starttime, $endtime])
            ->where('status', 'paid')
            ->field('paytime, status, COUNT(*) AS nums, SUM(payamount) AS amount, MIN(paytime) AS min_paytime, MAX(paytime) AS max_paytime, 
            DATE_FORMAT(FROM_UNIXTIME(paytime), "' . $format . '") AS paydate')
            ->group('paydate')
            ->select();

        if ($totalseconds > 84600 * 30 * 2) {
            $starttime = strtotime('last month', $starttime);
            while (($starttime = strtotime('next month', $starttime)) <= $endtime) {
                $column[] = date('Y-m', $starttime);
            }
        } else {
            if ($totalseconds > 86400) {
                for ($time = $starttime; $time <= $endtime;) {
                    $column[] = date("Y-m-d", $time);
                    $time += 86400;
                }
            } else {
                for ($time = $starttime; $time <= $endtime;) {
                    $column[] = date("H:00", $time);
                    $time += 3600;
                }
            }
        }

        $orderSaleNums = $orderSaleAmount = array_fill_keys($column, 0);
        foreach ($orderList as $k => $v) {
            $orderSaleNums[$v['paydate']] = $v['nums'];
            $orderSaleAmount[$v['paydate']] = round($v['amount'], 2);
        }
        $orderSaleCategory = array_keys($orderSaleAmount);
        $orderSaleAmount = array_values($orderSaleAmount);
        $orderSaleNums = array_values($orderSaleNums);
        return [$orderSaleCategory, $orderSaleAmount, $orderSaleNums];
    }


    //按项目统计
    public function project_sale()
    {


        $this->request->filter(['strip_tags', 'trim']);

        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = \app\admin\model\miniform\Project::field('p.*,COUNT(o.project_id) AS nums,SUM(o.payamount) AS amount')
                ->alias('p')
                ->join('miniform_order o', "o.project_id=p.id and o.status='paid'", 'LEFT')
                ->where($where)
                ->order($sort, $order)
                ->group('p.id')
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
    }
}
