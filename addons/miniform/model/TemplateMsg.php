<?php

namespace addons\miniform\model;

use think\Model;
use think\Queue;

class TemplateMsg extends Model
{

    // 表名
    protected $name = 'miniform_template_msg';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];

    protected $type = [
        'content' => 'json'
    ];

    public static function getTplIds()
    {
        return self::where('switch', 1)->column('tpl_id');
    }

    //获取发送模板消息的数据
    public static function getSendData()
    {
        if (!SubscribeLog::where('status', 0)->order('id asc')->find()) {
            return;
        }
        $config = get_addon_config('miniform');
        $time = time() + (int)$config['critical_timeout'];
        $templateArr = self::where('switch', 1)->column('ident,tpl_id');
        $holdData = [];
        $subscribeIds = [];
        // //取项目记录
        //活动即将结束,有签到时间的
        $logs = Logs::field('l.id,p.title,p.registered,p.signin_time,p.signin_name,p.begintime,p.endtime,o.orderid,o.createtime ordercreatetime,o.amount,t.openid,t.openname,s.id as subscribe_id,s.tpl_id')
            ->alias('l')
            ->join('miniform_project p', 'l.project_id=p.id')
            ->join('miniform_order o', 'o.logs_id=l.id', 'LEFT')
            ->join("third t", "t.user_id=l.user_id and t.apptype='miniapp' and t.platform='wechat'")
            ->join("miniform_subscribe_log s", "s.user_id=l.user_id and s.logs_id=l.id AND s.status=0")
            ->where('p.endtime', '<', $time)
            ->where('p.endtime', '>', time())
            ->whereOr(function ($query) {
                $query->whereNotNull('p.signin_time')->where('p.signin_time', '<>', '');
            })
            ->where('l.status', 'normal')->fetchSql(false)->select();

        //循环判断可发送的消息
        foreach ($logs as $item) {

            if (empty($item['openid'])) {
                continue;
            }

            $signin_name = (array)json_decode($item['signin_name'], true);

            $data = [
                'orderid'         => !empty($item['orderid']) ? $item['orderid'] : '无',
                'nickname'        => $item['openname'],
                'title'           => $item['title'],
                'registered'      => $item['registered'],
                'begintime'       => date('Y-m-d', $item['begintime']),
                'endtime'         => date('Y-m-d', $item['endtime']),
                'ordercreatetime' => !empty($item['ordercreatetime']) ? $item['ordercreatetime'] : '无',
                'amount'          => !empty($item['amount']) ? $item['amount'] : '0',
                'signin_name'     => isset($signin_name['address']) && !empty($signin_name['address']) ? $signin_name['address'] : '未配置地点',
                'openid'          => $item['openid'],
                'subscribe_id'    => $item['subscribe_id']               
            ];

            if (empty($item['signin_time'])) {
                $data['signin_begintime'] = '未配置签到开始时间';
                $data['signin_endtime'] = '未配置签到结束时间';
            }
            $identArr = [];
            //签到时间判断
            if (!empty($item['signin_time'])) {

                $time_arr = explode(' - ', $item['signin_time']);
                $start = strtotime($time_arr[0]);
                $end = strtotime($time_arr[1]);
                $data['signin_begintime'] = date('Y-m-d', $start);
                $data['signin_endtime'] = date('Y-m-d', $end);

                //开始时间判断
                if ($start < $time && $start > time()) {
                    $identArr[] = 0;
                }
                //结束时间判断
                if ($end < $time && $end > time()) {
                    $identArr[] = 1;
                }
            }
            //结束时间判断
            if ($item['endtime'] < $time && $item['endtime'] > time()) {
                $identArr[] = 2;
            }

            foreach ($identArr as $key => $value) {
                if (isset($templateArr[$value]) && $templateArr[$value] == $item['tpl_id']) {
                    $subscribeIds[] = $data['subscribe_id'];
                    $holdData[] = array_merge($data, ['ident' => $value]);
                }
            }
        }
        // 封装数据
        self::assembleData($holdData);
        // 变更状态
        SubscribeLog::where('id', 'in', $subscribeIds)->update(['status' => 1]);
    }

    //组装模板数据
    public static function assembleData($holdData)
    {
        $pushList = [];
        $tplArr = self::where('switch', 1)->column('*', 'ident');
        foreach ($holdData as $index => $holdDatum) {
            $data = [];
            $tpl = isset($tplArr[$holdDatum['ident']]) ? $tplArr[$holdDatum['ident']] : [];
            if (!$tpl) {
                continue;
            }
            $tpl['content'] = is_array($tpl['content']) ? $tpl['content'] : (array)json_decode($tpl['content'], true);
            foreach ($tpl['content'] as $item) {
                $value = str_replace('.DATA}}', '', str_replace('{{', '', $item['value']));
                if ($value) {
                    $data[$value] = [
                        'value' => $item['key'] != 'diy_text' ? ($item['def_val'] ? $item['def_val'] : $holdDatum[$item['key']]) : $item['def_val']
                    ];
                }
            }
            $pushData = [
                'touser'      => $holdDatum['openid'],
                'template_id' => $tpl['tpl_id'],
                'page'        => $tpl['page'],
                'data'        => $data
            ];
            $pushList[] = $pushData;
        }

        $config = get_addon_config('miniform');
        try {
            if ($config['sendnoticemode'] == 'queue') {
                if (extension_loaded('redis') && class_exists('\think\Queue')) {
                    //使用队列发送
                    foreach ($pushList as $index => $item) {
                        Queue::push('addons\miniform\controller\queue\Subscribe', $item, 'miniformSubscribeQueue');
                    }
                }
            } elseif ($config['sendnoticemode'] == 'async') {
                //异步并发发送
                (new \addons\miniform\library\Wechat\Service)->subscribeMessageSendMultiple($pushList);
            }
        } catch (\Exception $e) {
        }
    }
}
