<?php

namespace addons\miniform;

use app\common\library\Menu;
use think\Addons;
use think\Loader;
/**
 * 插件
 */
class Miniform extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu =  [
            [
                'name'    => 'miniform',
                'title'   => '活动报名表单管理',
                'icon'    => 'fa fa-list',
                'sublist' => [
                    [
                        'name'    => 'miniform/category',
                        'title'   => '分类管理',
                        'icon'    => 'fa fa-opencart',
                        'ismenu'  => 1,
                        'weigh'   => 100,
                        'sublist' => [
                            ['name' => 'miniform/category/index', 'title' => '查看'],
                            ['name' => 'miniform/category/add', 'title' => '添加'],
                            ['name' => 'miniform/category/edit', 'title' => '编辑'],
                            ['name' => 'miniform/category/del', 'title' => '删除'],
                            ['name' => 'miniform/category/multi', 'title' => '批量更新'],
                        ]
                    ],
                    [
                        'name'    => 'miniform/project',
                        'title'   => '项目管理',
                        'icon'    => 'fa fa-navicon',
                        'ismenu'  => 1,
                        'weigh'   => 99,
                        'sublist' => [
                            ['name' => 'miniform/project/index', 'title' => '查看'],
                            ['name' => 'miniform/project/add', 'title' => '添加'],
                            ['name' => 'miniform/project/edit', 'title' => '编辑'],
                            ['name' => 'miniform/project/del', 'title' => '删除'],
                            ['name' => 'miniform/project/multi', 'title' => '批量更新'],
                        ]
                    ],
                    [
                        'name'    => 'miniform/diyform',
                        'title'   => '项目表单管理',
                        'icon'    => 'fa fa-navicon',
                        'ismenu'  => 0,
                        'weigh'   => 98,
                        'sublist' => [
                            ['name' => 'miniform/diyform/index', 'title' => '查看'],
                            ['name' => 'miniform/diyform/add', 'title' => '添加'],
                            ['name' => 'miniform/diyform/edit', 'title' => '编辑'],
                            ['name' => 'miniform/diyform/del', 'title' => '删除'],
                            ['name' => 'miniform/diyform/verification', 'title' => '核销'],
                        ]
                    ],
                    [
                        'name'    => 'miniform/order',
                        'title'   => '订单管理',
                        'ismenu'  => 1,
                        'weigh'   => 97,
                        'icon'    => 'fa fa-file-text-o',
                        'sublist' => [
                            ['name' => 'miniform/order/index', 'title' => '查看'],
                            ['name' => 'miniform/order/add', 'title' => '添加'],
                            ['name' => 'miniform/order/edit', 'title' => '编辑'],
                            ['name' => 'miniform/order/del', 'title' => '删除'],
                            ['name' => 'miniform/order/multi', 'title' => '批量更新'],
                        ]
                    ],
                    [
                        'name'    => 'miniform/fields',
                        'title'   => '字段管理',
                        'ismenu'  => 0,
                        'weigh'   => 96,
                        'icon'    => 'fa fa-file-text-o',
                        'sublist' => [
                            ['name' => 'miniform/fields/index', 'title' => '查看'],
                            ['name' => 'miniform/fields/add', 'title' => '添加'],
                            ['name' => 'miniform/fields/edit', 'title' => '编辑'],
                            ['name' => 'miniform/fields/del', 'title' => '删除'],
                            ['name' => 'miniform/fields/multi', 'title' => '批量更新'],
                        ]
                    ],
                    [
                        'name'    => 'miniform/template_msg',
                        'title'   => '模板消息',
                        'ismenu'  => 1,
                        'weigh'   => 96,
                        'icon'    => 'fa fa-envelope-o',
                        'sublist' => [
                            ['name' => 'miniform/template_msg/index', 'title' => '查看'],
                            ['name' => 'miniform/template_msg/add', 'title' => '添加'],
                            ['name' => 'miniform/template_msg/edit', 'title' => '编辑'],
                            ['name' => 'miniform/template_msg/del', 'title' => '删除'],
                            ['name' => 'miniform/template_msg/import', 'title' => '导入'],
                            ['name' => 'miniform/template_msg/multi', 'title' => '批量更新'],
                        ]
                    ],
                    [
                        'name'    => 'miniform/report',
                        'title'   => '统计控制台',
                        'ismenu'  => 1,
                        'weigh'   => 95,
                        'icon'    => 'fa fa-line-chart',
                        'sublist' => [
                            ['name' => 'miniform/report/index', 'title' => '查看'],
                            ['name' => 'miniform/report/project_sale', 'title' => '项目数据统计'],
                        ]
                    ],
                    [
                        'name'    => 'miniform/theme',
                        'title'   => '在线预览',
                        'ismenu'  => 1,
                        'weigh'   => 94,
                        'icon'    => 'fa fa-mobile-phone',
                        'sublist' => [
                            ['name' => 'miniform/theme/index', 'title' => '查看'],
                        ]
                    ],
                    [
                        'name'    => 'miniform/config',
                        'title'   => '配置管理',
                        'ismenu'  => 1,
                        'weigh'   => 93,
                        'icon'    => 'fa fa-gears',
                        'sublist' => [
                            ['name' => 'miniform/config/index', 'title' => '查看'],
                        ]
                    ]
                ]
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete("miniform");
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable("miniform");
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable("miniform");
        return true;
    }

     /**
     * 应用初始化
     */
    public function appInit()
    {
        //添加命名空间
        if (!class_exists('\Hashids\Hashids')) {
            Loader::addNamespace('Hashids', ADDON_PATH . 'miniform' . DS . 'library' . DS . 'Hashids' . DS);
        }

        if (request()->isCli()) {
            \think\Console::addDefaultCommands([
                'addons\miniform\command\MiniformOrder',
                'addons\miniform\command\MiniformSubscribe'
            ]);
        }
    }

}
