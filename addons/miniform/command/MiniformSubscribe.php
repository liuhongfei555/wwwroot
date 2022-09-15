<?php

namespace addons\miniform\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class MiniformSubscribe extends Command
{

    protected function configure()
    {
        $this->setName('miniform:subscribe')->setDescription("miniform 模板消息");
    }

    /*
     * 订单失效---
     */
    protected function execute(Input $input, Output $output)
    {                   
        try {
            \addons\miniform\model\TemplateMsg::getSendData();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
        $output->writeln("end");
    }
}
