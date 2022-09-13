<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:69:"/var/www/html/public/../application/admin/view/cms/builder/index.html";i:1662987480;s:56:"/var/www/html/application/admin/view/layout/default.html";i:1653893966;s:53:"/var/www/html/application/admin/view/common/meta.html";i:1653893966;s:55:"/var/www/html/application/admin/view/common/script.html";i:1653893966;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">
<meta name="referrer" content="never">
<meta name="robots" content="noindex, nofollow">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<?php if(\think\Config::get('fastadmin.adminskin')): ?>
<link href="/assets/css/skins/<?php echo \think\Config::get('fastadmin.adminskin'); ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">
<?php endif; ?>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>

    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav') && \think\Config::get('fastadmin.breadcrumb')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <?php if($auth->check('dashboard')): ?>
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                    <?php endif; ?>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <style>
    .builder-form .row > .col-sm-4 {
        margin-bottom: 15px;
    }

    form.builder-form legend {
        padding-bottom: 10px;
    }

    .builder-form label {
        font-weight: normal;
    }

    #output, #result {
        font-size: 13px;
        line-height: 18px;
    }

    .config-list > div a {
        margin-bottom: 5px;
    }

    .flash {
        animation: flash 1s;
    }
    @keyframes flash {
        50% {
            background: rgba(255, 246, 210, 0.65);
        }
        100% {
            background: transparent;
        }
    }
</style>
<div class="panel panel-default panel-intro">

    <div class="panel-heading">
        <?php echo build_heading(null,FALSE); ?>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#config" data-toggle="tab" data-value="basic">站点配置</a></li>
            <li class=""><a href="#arclist" data-toggle="tab" data-value="arclist">文章列表arclist</a></li>
            <li class=""><a href="#channellist" data-toggle="tab" data-value="channellist">栏目列表channellist</a></li>
            <li class=""><a href="#spagelist" data-toggle="tab" data-value="spagelist">单页列表spagelist</a></li>
            <li class=""><a href="#speciallist" data-toggle="tab" data-value="speciallist">专题列表speciallist</a></li>
            <li class=""><a href="#blocklist" data-toggle="tab" data-value="blocklist">区块列表blocklist</a></li>
            <li class=""><a href="#userlist" data-toggle="tab" data-value="userlist">会员列表userlist</a></li>
            <li class=""><a href="#diydatalist" data-toggle="tab" data-value="diydatalist">自定义表单数据列表diydatalist</a></li>
            <li class=""><a href="#query" data-toggle="tab" data-value="query">万能查询query</a></li>
        </ul>
    </div>

    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="row">
                <div class="col-xs-12 col-sm-6 tab-content">
                    <div class="tab-pane fade active in" id="config">
                        <form role="form" class="builder-form" id="config-form">
                            <div class="form-group">
                                <legend>基础配置</legend>
                                <div class="row config-list">
                                    <?php if(is_array($configList) || $configList instanceof \think\Collection || $configList instanceof \think\Paginator): if( count($configList)==0 ) : echo "" ;else: foreach($configList as $key=>$item): ?>
                                    <div class="col-xs-6 col-sm-3">
                                        <a href="javascript:" class="btn btn-default" data-name="<?php echo $item['name']; ?>" data-type="<?php echo $item['type']; ?>"><?php echo $item['title']; ?></a>
                                    </div>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <legend>其它</legend>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <label>默认值</label>
                                        <input type="text" class="form-control" id="defaultvalue">
                                    </div>
                                    <div class="col-xs-6">
                                        <label>处理函数</label>
                                        <input type="text" class="form-control" id="func">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command">生成模板标签</button>
                                <button type="reset" class="btn btn-danger btn-embossed">重置</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="arclist">
                        <form role="form" class="builder-form" id="arclist-form">
                            <div class="form-group">
                                <legend>全局参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>列表循环变量(id)</label>
                                        <input type="text" name="id" id="id" class="form-control" placeholder="默认为item">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>为空提示(empty)</label>
                                        <input type="text" name="empty" id="empty" class="form-control" placeholder="默认无提示">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>循环变量(key)</label>
                                        <input type="text" name="key" id="key" class="form-control" placeholder="默认为变量i">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>取模值(mod)</label>
                                        <input type="number" name="mod" id="mod" class="form-control" placeholder="默认为2">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>行数(row)</label>
                                        <input type="number" name="row" id="row" class="form-control" placeholder="默认为10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序字段(orderby)</label>
                                        <select name="orderby" id="orderby" class="form-control selectpicker" data-live-search="true">
                                            <option value="">默认</option>
                                            <?php if(is_array($fieldList) || $fieldList instanceof \think\Collection || $fieldList instanceof \think\Paginator): if( count($fieldList)==0 ) : echo "" ;else: foreach($fieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="rand" data-subtext="随机">rand</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序方式(orderway)</label>
                                        <select name="orderway" id="orderway" class="form-control selectpicker">
                                            <option value="">默认为desc(降序)</option>
                                            <option value="desc">desc(降序)</option>
                                            <option value="asc">asc(升序)</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>偏移值(limit)</label>
                                        <input type="text" name="limit" class="form-control" placeholder="默认为空，例如：20,10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>缓存时长(cache)(秒)</label>
                                        <input type="text" name="cache" class="form-control" placeholder="默认读取站点配置">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <legend>特有参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>请选择模型</label>
                                        <select name="model" id="model" class="form-control selectpicker">
                                            <option value="">不限</option>
                                            <?php if(is_array($modelList) || $modelList instanceof \think\Collection || $modelList instanceof \think\Paginator): if( count($modelList)==0 ) : echo "" ;else: foreach($modelList as $key=>$item): ?>
                                            <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>请选择栏目</label>
                                        <select name="channel" id="channel" class="form-control selectpicker" data-live-search="true" multiple>
                                            <option value="">不限</option>
                                            <?php if(is_array($channelList) || $channelList instanceof \think\Collection || $channelList instanceof \think\Paginator): if( count($channelList)==0 ) : echo "" ;else: foreach($channelList as $key=>$item): ?>
                                            <option value="<?php echo $item['id']; ?>" data-model="<?php echo $item['model_id']; ?>" data-type="<?php echo $item['type']; ?>" <?php if($item['type']=='link'): ?>disabled<?php endif; ?>><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>栏目类型</label>
                                        <select name="type" id="channeltype" class="form-control selectpicker">
                                            <option value="">默认</option>
                                            <option value="son">(子集)仅栏目下文章</option>
                                            <option value="sons">(子孙集)栏目下所有子级栏目文章</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>主表字段 <a href="javascript:" data-toggle="tooltip" data-title="如果指定字段则id/user_id/channel_id/title/diyname必须勾选"><i class="fa fa-info-circle"></i></a></label>
                                        <select name="field" id="field" class="form-control selectpicker" data-live-search="true" multiple>
                                            <option value="">默认全部</option>
                                            <?php if(is_array($fieldList) || $fieldList instanceof \think\Collection || $fieldList instanceof \think\Paginator): if( count($fieldList)==0 ) : echo "" ;else: foreach($fieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="url" data-subtext="文章链接">url</option>
                                            <option value="fullurl" data-subtext="文章链接(带http://)">fullurl</option>
                                            <option value="textlink" data-subtext="文本链接(HTML)">textlink</option>
                                            <option value="imglink" data-subtext="图片链接(HTML)">imglink</option>
                                            <option value="img" data-subtext="图片(HTML)">img</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>副表字段 <a href="javascript:" data-toggle="tooltip" data-title="必须指定模型或栏目才可以设定副表字段，如果所选择的栏目中包含了多个模型的栏目则不能设定"><i class="fa fa-info-circle"></i></a></label>
                                        <select name="addon" id="addon" class="form-control selectpicker" data-live-search="true" multiple>
                                            <option value="">无</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>关联预载入 <a href="javascript:" data-toggle="tooltip" data-title="建议在需要读取栏目或会员信息时进行选中"><i class="fa fa-info-circle"></i></a></label>
                                        <select name="with" id="with" class="form-control selectpicker" multiple>
                                            <option value="channel">栏目信息</option>
                                            <option value="user">会员信息</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>栏目字段 <a href="javascript:" data-toggle="tooltip" data-title="如果勾选栏目字段，建议开启预载入栏目信息"><i class="fa fa-info-circle"></i></a></label>
                                        <select id="channelfield" class="form-control selectpicker" data-live-search="true" multiple>
                                            <?php if(is_array($channelFieldList) || $channelFieldList instanceof \think\Collection || $channelFieldList instanceof \think\Paginator): if( count($channelFieldList)==0 ) : echo "" ;else: foreach($channelFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="url" data-subtext="链接链接">url</option>
                                            <option value="fullurl" data-subtext="栏目链接(带http://)">fullurl</option>
                                            <option value="textlink" data-subtext="文本链接(HTML)">textlink</option>
                                            <option value="imglink" data-subtext="图片链接(HTML)">imglink</option>
                                            <option value="img" data-subtext="图片(HTML)">img</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>会员字段 <a href="javascript:" data-toggle="tooltip" data-title="如果勾选栏目字段，建议开启预载入会员信息"><i class="fa fa-info-circle"></i></a></label>
                                        <select id="userfield" class="form-control selectpicker" data-live-search="true" multiple>
                                            <?php if(is_array($userFieldList) || $userFieldList instanceof \think\Collection || $userFieldList instanceof \think\Paginator): if( count($userFieldList)==0 ) : echo "" ;else: foreach($userFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="url" data-subtext="会员主页链接">url</option>
                                            <option value="textlink" data-subtext="文本链接(HTML)">textlink</option>
                                            <option value="imglink" data-subtext="图片链接(HTML)">imglink</option>
                                            <option value="img" data-subtext="图片(HTML)">img</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command">生成模板标签</button>
                                <button type="reset" class="btn btn-danger btn-embossed">重置</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="channellist">
                        <form role="form" class="builder-form" id="channellist-form">
                            <div class="form-group">
                                <legend>全局参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>列表循环变量(id)</label>
                                        <input type="text" name="id" id="id" class="form-control" placeholder="默认为item">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>为空提示(empty)</label>
                                        <input type="text" name="empty" id="empty" class="form-control" placeholder="默认无提示">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>循环变量(key)</label>
                                        <input type="text" name="key" id="key" class="form-control" placeholder="默认为变量i">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>取模值(mod)</label>
                                        <input type="number" name="mod" id="mod" class="form-control" placeholder="默认为2">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>行数(row)</label>
                                        <input type="number" name="row" id="row" class="form-control" placeholder="默认为10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序字段(orderby)</label>
                                        <select name="orderby" id="orderby" class="form-control selectpicker" data-live-search="true">
                                            <option value="">默认</option>
                                            <?php if(is_array($channelFieldList) || $channelFieldList instanceof \think\Collection || $channelFieldList instanceof \think\Paginator): if( count($channelFieldList)==0 ) : echo "" ;else: foreach($channelFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="rand" data-subtext="随机">rand</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序方式(orderway)</label>
                                        <select name="orderway" id="orderway" class="form-control selectpicker">
                                            <option value="">默认为desc(降序)</option>
                                            <option value="desc">desc(降序)</option>
                                            <option value="asc">asc(升序)</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>偏移值(limit)</label>
                                        <input type="text" name="limit" class="form-control" placeholder="默认为空，例如：20,10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>缓存时长(cache)(秒)</label>
                                        <input type="text" name="cache" class="form-control" placeholder="默认读取站点配置">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <legend>特有参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>请选择模型</label>
                                        <select name="model" id="model" class="form-control selectpicker">
                                            <option value="">不限</option>
                                            <?php if(is_array($modelList) || $modelList instanceof \think\Collection || $modelList instanceof \think\Paginator): if( count($modelList)==0 ) : echo "" ;else: foreach($modelList as $key=>$item): ?>
                                            <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>请选择栏目</label>
                                        <select name="typeid" id="typeid" class="form-control selectpicker" data-live-search="true" multiple>
                                            <option value="">不限</option>
                                            <?php if(is_array($channelList) || $channelList instanceof \think\Collection || $channelList instanceof \think\Paginator): if( count($channelList)==0 ) : echo "" ;else: foreach($channelList as $key=>$item): ?>
                                            <option value="<?php echo $item['id']; ?>" data-model="<?php echo $item['model_id']; ?>" data-type="<?php echo $item['type']; ?>" <?php if($item['type']=='link'): ?>disabled<?php endif; ?>><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>栏目类型</label>
                                        <select name="type" id="channeltype" class="form-control selectpicker">
                                            <option value="">默认</option>
                                            <option value="top">顶级栏目</option>
                                            <option value="brother">兄弟栏目</option>
                                            <option value="son">(子集)仅栏目下文章</option>
                                            <option value="sons">(子孙集)栏目下所有子级栏目文章</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>主表字段 <a href="javascript:" data-toggle="tooltip" data-title="如果指定字段则id/name/diyname必须勾选"><i class="fa fa-info-circle"></i></a></label>
                                        <select name="field" id="field" class="form-control selectpicker" data-live-search="true" multiple>
                                            <option value="">默认全部</option>
                                            <?php if(is_array($channelFieldList) || $channelFieldList instanceof \think\Collection || $channelFieldList instanceof \think\Paginator): if( count($channelFieldList)==0 ) : echo "" ;else: foreach($channelFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="url" data-subtext="栏目链接">url</option>
                                            <option value="fullurl" data-subtext="栏目链接(带http://)">fullurl</option>
                                            <option value="textlink" data-subtext="文本链接(HTML)">textlink</option>
                                            <option value="imglink" data-subtext="图片链接(HTML)">imglink</option>
                                            <option value="img" data-subtext="图片(HTML)">img</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command">生成模板标签</button>
                                <button type="reset" class="btn btn-danger btn-embossed">重置</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="spagelist">
                        <form role="form" class="builder-form" id="spagelist-form">
                            <div class="form-group">
                                <legend>全局参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>列表循环变量(id)</label>
                                        <input type="text" name="id" id="id" class="form-control" placeholder="默认为item">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>为空提示(empty)</label>
                                        <input type="text" name="empty" id="empty" class="form-control" placeholder="默认无提示">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>循环变量(key)</label>
                                        <input type="text" name="key" id="key" class="form-control" placeholder="默认为变量i">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>取模值(mod)</label>
                                        <input type="number" name="mod" id="mod" class="form-control" placeholder="默认为2">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>行数(row)</label>
                                        <input type="number" name="row" id="row" class="form-control" placeholder="默认为10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序字段(orderby)</label>
                                        <select name="orderby" id="orderby" class="form-control selectpicker" data-live-search="true">
                                            <option value="">默认</option>
                                            <?php if(is_array($pageFieldList) || $pageFieldList instanceof \think\Collection || $pageFieldList instanceof \think\Paginator): if( count($pageFieldList)==0 ) : echo "" ;else: foreach($pageFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="rand" data-subtext="随机">rand</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序方式(orderway)</label>
                                        <select name="orderway" id="orderway" class="form-control selectpicker">
                                            <option value="">默认为desc(降序)</option>
                                            <option value="desc">desc(降序)</option>
                                            <option value="asc">asc(升序)</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>偏移值(limit)</label>
                                        <input type="text" name="limit" class="form-control" placeholder="默认为空，例如：20,10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>缓存时长(cache)(秒)</label>
                                        <input type="text" name="cache" class="form-control" placeholder="默认读取站点配置">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <legend>特有参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>请选择类型</label>
                                        <select name="type" id="type" class="form-control selectpicker">
                                            <option value="">不限</option>
                                            <?php if(is_array($pageTypeList) || $pageTypeList instanceof \think\Collection || $pageTypeList instanceof \think\Paginator): if( count($pageTypeList)==0 ) : echo "" ;else: foreach($pageTypeList as $key=>$item): ?>
                                            <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>主表字段 <a href="javascript:" data-toggle="tooltip" data-title="如果指定字段则id/title/diyname必须勾选"><i class="fa fa-info-circle"></i></a></label>
                                        <select name="field" id="field" class="form-control selectpicker" data-live-search="true" multiple>
                                            <option value="">默认全部</option>
                                            <?php if(is_array($pageFieldList) || $pageFieldList instanceof \think\Collection || $pageFieldList instanceof \think\Paginator): if( count($pageFieldList)==0 ) : echo "" ;else: foreach($pageFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="url" data-subtext="单页链接">url</option>
                                            <option value="fullurl" data-subtext="单页链接(带http://)">fullurl</option>
                                            <option value="textlink" data-subtext="文本链接(HTML)">textlink</option>
                                            <option value="imglink" data-subtext="图片链接(HTML)">imglink</option>
                                            <option value="img" data-subtext="图片(HTML)">img</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command">生成模板标签</button>
                                <button type="reset" class="btn btn-danger btn-embossed">重置</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="speciallist">
                        <form role="form" class="builder-form" id="speciallist-form">
                            <div class="form-group">
                                <legend>全局参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>列表循环变量(id)</label>
                                        <input type="text" name="id" id="id" class="form-control" placeholder="默认为item">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>为空提示(empty)</label>
                                        <input type="text" name="empty" id="empty" class="form-control" placeholder="默认无提示">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>循环变量(key)</label>
                                        <input type="text" name="key" id="key" class="form-control" placeholder="默认为变量i">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>取模值(mod)</label>
                                        <input type="number" name="mod" id="mod" class="form-control" placeholder="默认为2">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>行数(row)</label>
                                        <input type="number" name="row" id="row" class="form-control" placeholder="默认为10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序字段(orderby)</label>
                                        <select name="orderby" id="orderby" class="form-control selectpicker" data-live-search="true">
                                            <option value="">默认</option>
                                            <?php if(is_array($specialFieldList) || $specialFieldList instanceof \think\Collection || $specialFieldList instanceof \think\Paginator): if( count($specialFieldList)==0 ) : echo "" ;else: foreach($specialFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="rand" data-subtext="随机">rand</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序方式(orderway)</label>
                                        <select name="orderway" id="orderway" class="form-control selectpicker">
                                            <option value="">默认为desc(降序)</option>
                                            <option value="desc">desc(降序)</option>
                                            <option value="asc">asc(升序)</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>偏移值(limit)</label>
                                        <input type="text" name="limit" class="form-control" placeholder="默认为空，例如：20,10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>缓存时长(cache)(秒)</label>
                                        <input type="text" name="cache" class="form-control" placeholder="默认读取站点配置">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <legend>特有参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>请选择类型</label>
                                        <select name="type" id="type" class="form-control selectpicker">
                                            <option value="">不限</option>
                                            <?php if(is_array($pageTypeList) || $pageTypeList instanceof \think\Collection || $pageTypeList instanceof \think\Paginator): if( count($pageTypeList)==0 ) : echo "" ;else: foreach($pageTypeList as $key=>$item): ?>
                                            <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>主表字段 <a href="javascript:" data-toggle="tooltip" data-title="如果指定字段则id/title/image/diyname必须勾选"><i class="fa fa-info-circle"></i></a></label>
                                        <select name="field" id="field" class="form-control selectpicker" data-live-search="true" multiple>
                                            <option value="">默认全部</option>
                                            <?php if(is_array($specialFieldList) || $specialFieldList instanceof \think\Collection || $specialFieldList instanceof \think\Paginator): if( count($specialFieldList)==0 ) : echo "" ;else: foreach($specialFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="url" data-subtext="专题链接">url</option>
                                            <option value="fullurl" data-subtext="专题链接(带http://)">fullurl</option>
                                            <option value="textlink" data-subtext="文本链接(HTML)">textlink</option>
                                            <option value="imglink" data-subtext="图片链接(HTML)">imglink</option>
                                            <option value="img" data-subtext="图片(HTML)">img</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command">生成模板标签</button>
                                <button type="reset" class="btn btn-danger btn-embossed">重置</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="blocklist">
                        <form role="form" class="builder-form" id="blocklist-form">
                            <div class="form-group">
                                <legend>全局参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>列表循环变量(id)</label>
                                        <input type="text" name="id" id="id" class="form-control" placeholder="默认为item">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>为空提示(empty)</label>
                                        <input type="text" name="empty" id="empty" class="form-control" placeholder="默认无提示">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>循环变量(key)</label>
                                        <input type="text" name="key" id="key" class="form-control" placeholder="默认为变量i">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>取模值(mod)</label>
                                        <input type="number" name="mod" id="mod" class="form-control" placeholder="默认为2">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>行数(row)</label>
                                        <input type="number" name="row" id="row" class="form-control" placeholder="默认为10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序字段(orderby)</label>
                                        <select name="orderby" id="orderby" class="form-control selectpicker" data-live-search="true">
                                            <option value="">默认</option>
                                            <?php if(is_array($blockFieldList) || $blockFieldList instanceof \think\Collection || $blockFieldList instanceof \think\Paginator): if( count($blockFieldList)==0 ) : echo "" ;else: foreach($blockFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="rand" data-subtext="随机">rand</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序方式(orderway)</label>
                                        <select name="orderway" id="orderway" class="form-control selectpicker">
                                            <option value="">默认为desc(降序)</option>
                                            <option value="desc">desc(降序)</option>
                                            <option value="asc">asc(升序)</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>偏移值(limit)</label>
                                        <input type="text" name="limit" class="form-control" placeholder="默认为空，例如：20,10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>缓存时长(cache)(秒)</label>
                                        <input type="text" name="cache" class="form-control" placeholder="默认读取站点配置">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <legend>特有参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>请选择类型</label>
                                        <select name="type" id="type" class="form-control selectpicker">
                                            <option value="">不限</option>
                                            <?php if(is_array($blockTypeList) || $blockTypeList instanceof \think\Collection || $blockTypeList instanceof \think\Paginator): if( count($blockTypeList)==0 ) : echo "" ;else: foreach($blockTypeList as $key=>$item): ?>
                                            <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>请选择名称</label>
                                        <select name="name" id="name" class="form-control selectpicker">
                                            <option value="">不限</option>
                                            <?php if(is_array($blockNameList) || $blockNameList instanceof \think\Collection || $blockNameList instanceof \think\Paginator): if( count($blockNameList)==0 ) : echo "" ;else: foreach($blockNameList as $key=>$item): ?>
                                            <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>主表字段 <a href="javascript:" data-toggle="tooltip" data-title="如果指定字段则id/title/image/url/begintime/endtime必须勾选"><i class="fa fa-info-circle"></i></a></label>
                                        <select name="field" id="field" class="form-control selectpicker" data-live-search="true" multiple>
                                            <option value="">默认全部</option>
                                            <?php if(is_array($blockFieldList) || $blockFieldList instanceof \think\Collection || $blockFieldList instanceof \think\Paginator): if( count($blockFieldList)==0 ) : echo "" ;else: foreach($blockFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="textlink" data-subtext="文本链接(HTML)">textlink</option>
                                            <option value="imglink" data-subtext="图片链接(HTML)">imglink</option>
                                            <option value="img" data-subtext="图片(HTML)">img</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command">生成模板标签</button>
                                <button type="reset" class="btn btn-danger btn-embossed">重置</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="userlist">
                        <form role="form" class="builder-form" id="userlist-form">
                            <div class="form-group">
                                <legend>全局参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>列表循环变量(id)</label>
                                        <input type="text" name="id" id="id" class="form-control" placeholder="默认为item">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>为空提示(empty)</label>
                                        <input type="text" name="empty" id="empty" class="form-control" placeholder="默认无提示">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>循环变量(key)</label>
                                        <input type="text" name="key" id="key" class="form-control" placeholder="默认为变量i">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>取模值(mod)</label>
                                        <input type="number" name="mod" id="mod" class="form-control" placeholder="默认为2">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>行数(row)</label>
                                        <input type="number" name="row" id="row" class="form-control" placeholder="默认为10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序字段(orderby)</label>
                                        <select name="orderby" id="orderby" class="form-control selectpicker" data-live-search="true">
                                            <option value="">默认</option>
                                            <?php if(is_array($userFieldList) || $userFieldList instanceof \think\Collection || $userFieldList instanceof \think\Paginator): if( count($userFieldList)==0 ) : echo "" ;else: foreach($userFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="rand" data-subtext="随机">rand</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序方式(orderway)</label>
                                        <select name="orderway" id="orderway" class="form-control selectpicker">
                                            <option value="">默认为desc(降序)</option>
                                            <option value="desc">desc(降序)</option>
                                            <option value="asc">asc(升序)</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>偏移值(limit)</label>
                                        <input type="text" name="limit" class="form-control" placeholder="默认为空，例如：20,10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>缓存时长(cache)(秒)</label>
                                        <input type="text" name="cache" class="form-control" placeholder="默认读取站点配置">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <legend>特有参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>主表字段 <a href="javascript:" data-toggle="tooltip" data-title="如果指定字段则id/title/image/url/begintime/endtime必须勾选"><i class="fa fa-info-circle"></i></a></label>
                                        <select name="field" id="field" class="form-control selectpicker" data-live-search="true" multiple>
                                            <option value="">默认全部</option>
                                            <?php if(is_array($userFieldList) || $userFieldList instanceof \think\Collection || $userFieldList instanceof \think\Paginator): if( count($userFieldList)==0 ) : echo "" ;else: foreach($userFieldList as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>" data-subtext="<?php echo $item['title']; ?>"><?php echo $item['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="url" data-subtext="会员主页链接">url</option>
                                            <option value="textlink" data-subtext="文本链接(HTML)">textlink</option>
                                            <option value="imglink" data-subtext="图片链接(HTML)">imglink</option>
                                            <option value="img" data-subtext="图片(HTML)">img</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command">生成模板标签</button>
                                <button type="reset" class="btn btn-danger btn-embossed">重置</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="diydatalist">
                        <form role="form" class="builder-form" id="diydatalist-form">
                            <div class="form-group">
                                <legend>全局参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>列表循环变量(id)</label>
                                        <input type="text" name="id" id="id" class="form-control" placeholder="默认为item">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>为空提示(empty)</label>
                                        <input type="text" name="empty" id="empty" class="form-control" placeholder="默认无提示">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>循环变量(key)</label>
                                        <input type="text" name="key" id="key" class="form-control" placeholder="默认为变量i">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>取模值(mod)</label>
                                        <input type="number" name="mod" id="mod" class="form-control" placeholder="默认为2">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>行数(row)</label>
                                        <input type="number" name="row" id="row" class="form-control" placeholder="默认为10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序字段(orderby)</label>
                                        <select name="orderby" id="orderby" class="form-control selectpicker" data-live-search="true">
                                            <option value="">默认</option>
                                            <?php if(is_array($diyformFieldList) || $diyformFieldList instanceof \think\Collection || $diyformFieldList instanceof \think\Paginator): if( count($diyformFieldList)==0 ) : echo "" ;else: foreach($diyformFieldList as $key=>$item): if(is_array($item) || $item instanceof \think\Collection || $item instanceof \think\Paginator): if( count($item)==0 ) : echo "" ;else: foreach($item as $subkey=>$field): ?>
                                            <option class="hidden" data-id="<?php echo $key; ?>" value="<?php echo $field['name']; ?>" data-subtext="<?php echo $field['title']; ?>"><?php echo $field['name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                                            <option value="rand" data-subtext="随机">rand</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>排序方式(orderway)</label>
                                        <select name="orderway" id="orderway" class="form-control selectpicker">
                                            <option value="">默认为desc(降序)</option>
                                            <option value="desc">desc(降序)</option>
                                            <option value="asc">asc(升序)</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>偏移值(limit)</label>
                                        <input type="text" name="limit" class="form-control" placeholder="默认为空，例如：20,10">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>缓存时长(cache)(秒)</label>
                                        <input type="text" name="cache" class="form-control" placeholder="默认读取站点配置">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <legend>特有参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>请选择自定义表单</label>
                                        <select name="diyform_id" id="diyform_id" class="form-control selectpicker" data-live-search="true">
                                            <option value="">请选择</option>
                                            <?php if(is_array($diyformList) || $diyformList instanceof \think\Collection || $diyformList instanceof \think\Paginator): if( count($diyformList)==0 ) : echo "" ;else: foreach($diyformList as $key=>$item): ?>
                                            <option value="<?php echo $item['id']; ?>"><?php echo $item['title']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>请选择字段</label>
                                        <select name="field" id="field" class="form-control selectpicker" data-live-search="true" multiple>
                                            <?php if(is_array($diyformFieldList) || $diyformFieldList instanceof \think\Collection || $diyformFieldList instanceof \think\Paginator): if( count($diyformFieldList)==0 ) : echo "" ;else: foreach($diyformFieldList as $key=>$item): if(is_array($item) || $item instanceof \think\Collection || $item instanceof \think\Paginator): if( count($item)==0 ) : echo "" ;else: foreach($item as $subkey=>$field): ?>
                                                <option class="hidden" data-id="<?php echo $key; ?>" value="<?php echo $field['name']; ?>" data-subtext="<?php echo $field['title']; ?>"><?php echo $field['name']; ?></option>
                                                <?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command">生成模板标签</button>
                                <button type="reset" class="btn btn-danger btn-embossed">重置</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="query">
                        <form role="form" class="builder-form" id="query-form">
                            <div class="form-group">
                                <legend>全局参数</legend>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <label>列表循环变量(id)</label>
                                        <input type="text" name="id" id="id" class="form-control" placeholder="默认为item">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>为空提示(empty)</label>
                                        <input type="text" name="empty" id="empty" class="form-control" placeholder="默认无提示">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>循环变量(key)</label>
                                        <input type="text" name="key" id="key" class="form-control" placeholder="默认为变量i">
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <label>取模值(mod)</label>
                                        <input type="number" name="mod" id="mod" class="form-control" placeholder="默认为2">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <legend>特有参数</legend>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">
                                        <label>SQL语句</label>
                                        <input name="sql" id="sql" class="form-control" placeholder="SQL查询语句,如果需要参数绑定,请使用?占位符" />
                                    </div>
                                    <div class="col-xs-12 col-sm-12" style="margin-top:15px;">
                                        <label>绑定参数</label>
                                        <input name="bind" id="bind" class="form-control" placeholder="多个参数以,进行分隔,可以为空,如果是字符串请前后添加上单引号" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command">生成模板标签</button>
                                <button type="reset" class="btn btn-danger btn-embossed">重置</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <form role="form" class="builder-form" id="preview-form">
                        <div class="form-group">
                            <legend>标签预览</legend>
                            <textarea name="" id="output" cols="30" rows="10" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-success btn-embossed btn-result">渲染标签结果</button>
                            <button type="button" class="btn btn-info btn-embossed btn-copy" id="copytag">复制标签</button>
                        </div>
                        <div class="form-group">
                            <legend>执行结果</legend>
                            <textarea name="" id="result" cols="30" rows="10" class="form-control"></textarea>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!--@formatter:off-->

<script type="text/html" id="configtpl">
{$Think.config.cms.<%=name%><%=#defaultvalue%><%=#func%>}
</script>

<script type="text/html" id="arclisttpl">
{cms:arclist <%=#attrs%>}
<%for(var i=0;i < columns.length;i++){%>
    <%=columns[i]%>
<%}%>
{/cms:arclist}
</script>

<script type="text/html" id="channellisttpl">
{cms:channellist <%=#attrs%>}
<%for(var i=0;i < columns.length;i++){%>
    <%=columns[i]%>
<%}%>
{/cms:channellist}
</script>

<script type="text/html" id="spagelisttpl">
{cms:spagelist <%=#attrs%>}
<%for(var i=0;i < columns.length;i++){%>
    <%=columns[i]%>
<%}%>
{/cms:spagelist}
</script>

<script type="text/html" id="speciallisttpl">
{cms:speciallist <%=#attrs%>}
<%for(var i=0;i < columns.length;i++){%>
    <%=columns[i]%>
<%}%>
{/cms:speciallist}
</script>

<script type="text/html" id="blocklisttpl">
{cms:blocklist <%=#attrs%>}
<%for(var i=0;i < columns.length;i++){%>
    <%=columns[i]%>
<%}%>
{/cms:blocklist}
</script>

<script type="text/html" id="userlisttpl">
{cms:userlist <%=#attrs%>}
<%for(var i=0;i < columns.length;i++){%>
    <%=columns[i]%>
<%}%>
{/cms:userlist}
</script>

<script type="text/html" id="diydatalisttpl">
{cms:diydatalist <%=#attrs%>}
<%for(var i=0;i < columns.length;i++){%>
    <%=columns[i]%>
<%}%>
{/cms:diydatalist}
</script>

<script type="text/html" id="querytpl">
{cms:query <%=#attrs%>}

{/cms:query}
</script>

<!--@formatter:on-->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
