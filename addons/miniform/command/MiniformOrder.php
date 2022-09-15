<?php

namespace addons\miniform\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use addons\miniform\model\Order;
use think\Db;

class MiniformOrder extends Command
{

    protected function configure()
    {
        $this->setName('miniform:order')->setDescription("miniform 订单失效");
    }

    /*
     * 订单失效---
     */
    protected function execute(Input $input, Output $output)
    {
        $config = get_addon_config('miniform');
        $time = time() - $config['order_timeout'];

        $data = Order::with(['project'])->where('status', 'created')->where('createtime', '<', $time)->select();

        foreach ($data as $res) {
            // 启动事务
            Db::startTrans();
            try {
                $res->save(['status' => 'expired']);
                $res->logs->save(['status' => 'canceled']);
                $res->project && $res->project->registered > 0 && $res->project->setDec('registered');
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                continue;
            }
        } 
        $output->writeln("end");
    }
}
