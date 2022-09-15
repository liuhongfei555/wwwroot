<?php

namespace addons\miniform\controller;

use addons\miniform\library\Theme;
use fast\Http;
use think\addons\Service;
use think\Config;
use think\Hook;
use think\Db;
use think\captcha\Captcha;

/**
 * 公共
 */
class Common extends Base
{
    protected $noNeedLogin = '*';

    /**
     * 初始化
     */
    public function init()
    {
        //配置信息
        $upload = Config::get('upload');

        //如果非服务端中转模式需要修改为中转
        if ($upload['storage'] != 'local' && isset($upload['uploadmode']) && $upload['uploadmode'] != 'server') {
            //临时修改上传模式为服务端中转
            set_addon_config($upload['storage'], ["uploadmode" => "server"], false);

            $upload = \app\common\model\Config::upload();
            // 上传信息配置后
            Hook::listen("upload_config_init", $upload);

            $upload = Config::set('upload', array_merge(Config::get('upload'), $upload));
        }

        $upload['cdnurl'] = $upload['cdnurl'] ? $upload['cdnurl'] : cdnurl('', true);
        $upload['uploadurl'] = preg_match("/^((?:[a-z]+:)?\/\/)(.*)/i", $upload['uploadurl']) ? $upload['uploadurl'] : url($upload['storage'] == 'local' ? '/api/common/upload' : $upload['uploadurl'], '', false, true);

        $data = [
            'upload'    => $upload,
            '__token__' => $this->request->token()
        ];

        $data['category'] = Db::name('miniform_category')->order('weigh desc,id desc')->select();

        //消息订阅模板id
        $data['tpl_ids'] = \addons\miniform\model\TemplateMsg::getTplIds();

        $swiperInfo = Config::get('miniform.swiperInfo');
        $swiperInfo = $swiperInfo ? (array)json_decode($swiperInfo, true) : [];
        foreach ($swiperInfo as $key => &$info) {
            $info['image'] = cdnurl($info['image'], true);
        }
        $data['swiper'] = $swiperInfo;
        $data['phone'] = Config::get('miniform.mobile');
        $data['index_list'] = Config::get('miniform.index_list');
        $data['isShowNavInMp'] = Config::get('miniform.isShowNavInMp');
        $data['sendnoticemode'] = Config::get('miniform.sendnoticemode');
        $data['agreement'] = Config::get('miniform.agreement');
        //合并主题样式，判断是否预览模式
        $isPreview = stripos($this->request->SERVER("HTTP_REFERER"), "mode=preview") !== false;
        $themeConfig = $isPreview && \think\Session::get("previewtheme-miniform") ? \think\Session::get("previewtheme-miniform") : Theme::get();

        $themeConfig = Theme::render($themeConfig);
        $data = array_merge($data, $themeConfig);

        $this->success('', $data);
    }

    //获取验证码
    public function captcha()
    {
        $ident = $this->request->param('ident');
        $captcha = new Captcha((array)Config::get('captcha'));
        $res = $captcha->entry($ident)->getContent();
        $this->success('获取成功', 'data:image/png;base64,' . base64_encode($res));
    }

    //获取小程序码
    public function getWxCode()
    {
        $project_id = $this->request->post('project_id');
        if (empty($project_id)) {
            $this->error('参数错误');
        }
        $user_id = '';
        if ($this->auth->isLogin()) {
            $user_id = $this->auth->id;
        }
        $base64_data = '';
        try {
            $fileStream = (new \addons\miniform\library\Wechat\Service)->getWxCodeUnlimited([
                'scene' => "invite_id={$user_id}&project_id={$project_id}",
                'page'  => 'pages/goods/detail'
            ]);
            if (is_null(json_decode($fileStream))) {
                $base64_data = base64_encode($fileStream);
            }
        } catch (\Exception $e) {

        }
        if (!$base64_data) {
            $config = get_addon_config('miniform');
            if (empty($config['wxapp'])) {
                $this->error('请在后台配置小程序二维码！');
            }
            $fileContent = '';
            if (preg_match('/^((?:[a-z]+:)?\/\/)(.*)/i', $config['wxapp'])) {
                try {
                    $fileContent = Http::get($config['wxapp']);
                } catch (\Exception $e) {
                    $this->error('获取小程序二维码失败');
                }
            } else {
                $filePath = ROOT_PATH . 'public' . $config['wxapp'];
                if (!is_file($filePath)) {
                    $this->error('未找到指定小程序二维码！');
                }
                $fileContent = file_get_contents($filePath);
            }
            $base64_data = base64_encode($fileContent);
        }
        $base64_file = 'data:image/jpg;base64,' . $base64_data;
        $this->success('获取成功', $base64_file);
    }
}
