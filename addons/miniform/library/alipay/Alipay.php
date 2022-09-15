<?php

namespace addons\miniform\library\alipay;

use addons\miniform\library\alipay\AopClient;
use addons\miniform\library\alipay\AlipaySystemOauthTokenRequest;
use addons\third\library\Service;
use think\Session;

/**
 * 支付宝
 */
class Alipay
{

    protected $aop = null;

    public function __construct()
    {
        if (!$this->aop) {
            $this->aop = new AopClient();
        }
        $info = get_addon_info('third');
        if (!$info || !$info['state']) {
            throw new \Exception("请在后台安装第三方登录插件");
        }
        $config = get_addon_config('miniform');
        $this->aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';

        $pay_config = \addons\epay\library\Service::getConfig('alipay');
        //应用id
        $this->aop->appId = $config['alipay_app_id'];
        // print_r($pay_config);exit;
        //私钥
        $this->aop->rsaPrivateKey = $pay_config['private_key'];        
        //支付宝公钥
        $this->aop->alipayPublicKey = $pay_config['ali_public_key'];        
        // $this->aop->alipayrsaPublicKey = '';
        $this->aop->signType = 'RSA2';
    }


    public function getAuthThird($code, $userinfo)
    {
        $request = new AlipaySystemOauthTokenRequest();
        $request->setGrantType("authorization_code");
        $request->setCode($code);
        $result = $this->aop->execute($request);
        // print_r($result);exit;
        if (empty($result->alipay_system_oauth_token_response)) {
            throw new \Exception('授权失败！');
        }
        $res = (array)$result->alipay_system_oauth_token_response;
        $auth = \app\common\library\Auth::instance();

        $params['platform']      = 'alipay';
        $params['apptype']       = 'mini';
        $params['openid']        = $res['user_id'];
        $params['nickname']      = $userinfo['nickName'];
        $params['avatar']        = $userinfo['avatarUrl'];
        $params['access_token']  = $res['access_token'];
        $params['refresh_token'] = $res['refresh_token'];
        $params['expires_in']    = $res['expires_in'];
        $params['expiretime']    = $res['expires_in'];

        $user = null;
        $third = [
            'nickname' => $userinfo['nickName'],
            'avatar'   => $userinfo['avatarUrl']
        ];
        if ($auth->isLogin() || Service::isBindThird($params['platform'], $params['openid'])) {
            Service::connect($params['platform'], $params);
            $user = $auth->getUserinfo();
        } else {
            Session::set('third-userinfo', $params);
        }
        return ['user' => $user, 'third' => $third];
    }
}
