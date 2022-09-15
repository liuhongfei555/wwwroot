<?php

namespace addons\miniform\model;

use think\Model;
use think\Db;
use addons\miniform\library\IntCode;
use traits\model\SoftDelete;

class Logs extends Model
{
    use SoftDelete;

    // 表名
    protected $name = 'miniform_logs';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'createtime_text',
        'verification_text',
        'status_text',
    ];

    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getVerificationTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['id']) ? $data['id'] : '');
        return IntCode::encode($value);
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getStatusList()
    {
        return ['normal' => '正常', 'nonpayment' => '未支付', 'refunding' => '退款中', 'canceled' => '已取消', 'expired' => '已过期'];
    }

    /**
     * 判断是否签到
     * @param $res
     */
    public static function getIsSignin($res)
    {
        if (isset($res['project']['table']) && !empty($res['project']['table']) && isset($res['diyform_id'])) {
            $model = new Diyform();
            $model->setName($res['project']['table']);
            $diyform = $model->where('id', $res['diyform_id'])->find();
            $res->signintime = $diyform->signintime;
            $res->is_signin = $diyform->signintime ? 1 : 0;
            $res->verificationtime = $diyform->verificationtime;
            $res->is_verification = $diyform->verificationtime ? 1 : 0;
        } else {
            $res->is_signin = 0;
        }
        $res->project->hidden(['table']);
    }

    //删除数据
    public static function deleteLogs($project_id, $status = false)
    {
        $list = new self();
        if ($status) {
            $list = $list->onlyTrashed();
        }
        $list = $list->where('project_id', $project_id)->select();
        if ($list) {
            foreach ($list as $row) {
                $row->delete($status);
            }
        }
        return true;
    }

    //还原数据
    public static function restoreLogs($project_id)
    {
        $list = (new self)->onlyTrashed()->where('project_id', $project_id)->select();
        if ($list) {
            foreach ($list as $row) {
                $row->restore();
            }
        }
        return true;
    }

    public function project()
    {
        return $this->hasOne('Project', 'id', 'project_id', [], 'LEFT');
    }

    public function User()
    {
        return $this->hasOne('\app\common\model\User', 'id', 'user_id', [], 'LEFT');
    }

    public function orderinfo()
    {
        return $this->belongsTo('Order', 'id', 'logs_id', [], 'LEFT');
    }

    public function Subscribe()
    {
        return $this->hasOne('SubscribeLog', 'logs_id', 'id');
    }
}
