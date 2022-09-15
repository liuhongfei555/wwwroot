<?php

namespace app\admin\controller\miniform;

use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\admin\model\miniform\Fields;
use addons\miniform\library\IntCode;
use addons\miniform\model\Logs;

/**
 *
 * 表单数据
 * @icon fa fa-circle-o
 */
class Diyform extends Backend
{

    /**
     * Diyform模型对象
     * @var \app\admin\model\miniform\Diyform
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\miniform\Diyform;
        $source = $this->request->param('source');
        $this->model->setName($source);
        $this->view->assign("statusList", $this->model->getStatusList());
        $config = get_addon_config('miniform');
        $this->view->assign('camera_qrcode', isset($config['camera_qrcode']) ? intval($config['camera_qrcode']) : false);
        $this->assignconfig('camera_qrcode', isset($config['camera_qrcode']) ? intval($config['camera_qrcode']) : false);
        $this->assignconfig('verification_voice', isset($config['verification_voice']) ? intval($config['verification_voice']) : false);
    }

    public function import()
    {
        parent::import();
    }

    public function index()
    {
        $source = $this->request->param('source');
        $source_id = $this->request->param('source_id');
        try {
            $fields = Fields::field('id,name,title,type,status,content,isshowback')->where('source', $source)->where('source_id', $source_id)->order('weigh desc')->select();
            $this->assignconfig('source', $source);
            $this->assignconfig('source_id', $source_id);
            $this->assignconfig('fields', $fields);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $project = \app\admin\model\miniform\Project::get($source_id);
        $is_verification = $this->request->param('is_verification');
        $this->view->assign('project', $project);
        $this->view->assign('is_verification', $is_verification);
        $this->assignconfig('is_verification', $is_verification);
        $this->assignconfig('is_signin', $project->is_signin);
        return parent::index();
    }

    public function add()
    {
        return;
    }

    public function edit($ids = null)
    {
        $source = $this->request->param('source');
        $source_id = $this->request->param('source_id');
        $row = $this->model->where('id', $ids)->find();
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if (isset($params['fields']) && is_array($params['fields'])) {
                $params = array_merge($params, $params['fields']);
            }
            if ($params) {
                $params = $this->preExcludeFields($params);
                if (isset($params['fields'])) {
                    foreach ($params['fields'] as $key => &$item) {
                        if (is_array($item)) {
                            $item = implode(',', $item);
                        }
                    }
                    $params = array_merge($params, $params['fields']);
                }
                $result = false;
                Db::startTrans();
                try {
                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $fields = Fields::where('source', $source)->where('source_id', $source_id)->order('weigh desc')->select();

        foreach ($fields as $item) {
            if ($item['type'] == 'array') {
                $item['value'] = html_entity_decode($row[$item['name']]);
            } elseif ($item['type'] == 'location') {
                $address = '';
                $lat = 0;
                $lng = 0;
                if (preg_match("/(.*)\,([0-9\.]+),([0-9\.]+)\$/i", $row[$item['name']], $matches)) {
                    $address = $matches[1];
                    $lat = $matches[2];
                    $lng = $matches[3];
                }
                $item['value'] = ['address' => $address, 'lat' => $lat, 'lng' => $lng];
            } else {
                $item['value'] = $row[$item['name']];
            }
        }

        $project = \app\admin\model\miniform\Project::get($source_id);
        $this->assign('fields', $fields);
        $this->view->assign("row", $row);
        $this->view->assign("project", $project);
        return $this->view->fetch();
    }

    public function del($ids = null)
    {
        return parent::del($ids);
    }

    //扫码核销
    public function verification()
    {
        if ($this->request->isPost()) {
            $verification = $this->request->post('verification');
            if (!$verification) {
                $this->error('请填写核销码！');
            }
            $verification = IntCode::decode($verification);
            if (!$verification) {
                $this->error('核销码不正确！');
            }
            $logs = (new Logs())->where('id', $verification)->find();
            if (!$logs) {
                $this->error('记录未找到');
            }
            if (in_array($logs['status'], ['nonpayment', 'canceled', 'expired'])) {
                $this->error('项目记录未支付/已取消/已过期');
            }
            //核销表单
            $diyform = $this->model->where(['id' => $logs->diyform_id])->find();
            if (!in_array($diyform['status'], ['free', 'paid'])) {
                $this->error('未支付或已经取消！');
            }
            if ($diyform['verificationtime']) {
                $this->error('已经核销，无需重复核销！');
            }
            $result = false;
            Db::startTrans();
            try {
                $result = $diyform->save(['verificationtime' => time()]);
                if ($logs['status'] == 'refunding') {
                    //恢复订单和项目记录状态正常
                    $logs->orderinfo->save(['status' => 'paid']);
                    $logs->save(['status' => 'normal']);
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
            }
            if ($result) {
                $this->success('核销成功!');
            } else {
                $this->error('核销失败！');
            }
        } else {
            $this->error('请求方式错误！');
        }
    }
}
