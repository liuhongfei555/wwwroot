<?php

namespace addons\miniform\controller;

use addons\third\model\Third;
use think\Config;
use think\Validate;

/**
 * 会员
 */
class User extends Base
{
    protected $noNeedLogin = ['getSigned'];

    public function _initialize()
    {
        parent::_initialize();

        if (!Config::get('fastadmin.usercenter')) {
            $this->error(__('User center already closed'));
        }
    }

    /**
     * 个人中心
     */
    public function index()
    {
        $apptype = $this->request->param('apptype');
        $platform = $this->request->param('platform');

        $info = $this->auth->getUserInfo();
        $info['avatar'] = cdnurl($info['avatar'], true);
        $signin = get_addon_info('signin');
        
        $info['is_third'] = (Third::where('user_id',$this->auth->id)->where('platform',$platform)->where('apptype',$apptype)->find())?true:false;

        $info['is_install_signin'] = ($signin && $signin['state']);
        $this->success('', [
            'userInfo' => $info
        ]);
    }


    /**
     * 个人资料
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $username = $this->request->post('username');
        $nickname = $this->request->post('nickname');
        $bio = $this->request->post('bio');
        $avatar = $this->request->post('avatar');
        if (!$username || !$nickname) {
            $this->error("用户名和昵称不能为空");
        }
        if (strlen($bio) > 100) {
            $this->error("签名太长了！");
        }
        $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
        if ($exists) {
            $this->error(__('Username already exists'));
        }

        $avatar = str_replace(cdnurl('', true), '', $avatar);

        $user->username = $username;
        $user->nickname = $nickname;
        $user->bio = $bio;
        $user->avatar = $avatar;
        $user->save();

        $this->success('修改成功！');
    }

    /**
     * 保存头像
     */
    public function avatar()
    {
        $user = $this->auth->getUser();
        $avatar = $this->request->post('avatar');
        if (!$avatar) {
            $this->error("头像不能为空");
        }

        $avatar = str_replace(cdnurl('', true), '', $avatar);
        $user->avatar = $avatar;
        $user->save();
        $this->success('修改成功！');
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success(__('Logout successful'), ['__token__' => $this->request->token()]);
    }

    /**
     * 分享配置参数
     */
    public function getSigned()
    {
        $url = $this->request->param('url','', 'trim');
        $js_sdk = new \addons\miniform\library\Jssdk();
        $data = $js_sdk->getSignedPackage($url);
        $this->success('', $data);
    }
}
