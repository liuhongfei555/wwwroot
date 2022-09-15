-- ----------------------------
-- Table structure for fa_miniform_category
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__miniform_category`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `createtime` bigint(16) NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) NULL DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT 0 COMMENT '权重',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '分类表';

-- ----------------------------
-- Table structure for fa_miniform_fields
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__miniform_fields`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `source` varchar(50) CHARACTER SET utf8mb4 NOT NULL COMMENT '资源',
  `source_id` int(10) NOT NULL DEFAULT 0 COMMENT '资源ID',
  `name` char(30) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '名称',
  `type` varchar(30) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '类型',
  `title` varchar(30) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '标题',
  `content` text CHARACTER SET utf8mb4 NULL COMMENT '内容',
  `value` text CHARACTER SET utf8mb4 NULL COMMENT '变量值',
  `defaultvalue` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '默认值',
  `rule` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT '' COMMENT '验证规则',
  `msg` varchar(30) CHARACTER SET utf8mb4 NULL DEFAULT '0' COMMENT '错误消息',
  `ok` varchar(30) CHARACTER SET utf8mb4 NULL DEFAULT '0' COMMENT '成功消息',
  `tip` varchar(30) CHARACTER SET utf8mb4 NULL DEFAULT '' COMMENT '提示消息',
  `decimals` tinyint(1) NULL DEFAULT NULL COMMENT '小数点',
  `length` mediumint(8) NULL DEFAULT NULL COMMENT '长度',
  `minimum` smallint(6) NULL DEFAULT NULL COMMENT '最小数量',
  `maximum` smallint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最大数量',
  `isshowfront` tinyint(1) NULL DEFAULT 0 COMMENT '是否前端显示',
  `isshowback` tinyint(1) NULL DEFAULT 0 COMMENT '是否后台显示',
  `setting` varchar(1500) CHARACTER SET utf8mb4 NULL DEFAULT '' COMMENT '配置信息',
  `weigh` int(10) NOT NULL DEFAULT 0 COMMENT '排序',
  `createtime` bigint(16) NULL DEFAULT NULL COMMENT '添加时间',
  `updatetime` bigint(16) NULL DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') CHARACTER SET utf8mb4 NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '项目字段';

-- ----------------------------
-- Table structure for fa_miniform_logs
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__miniform_logs`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NULL DEFAULT NULL COMMENT '用户ID',
  `diyform_id` int(10) NULL DEFAULT 0 COMMENT '表单ID',
  `project_id` int(10) NULL DEFAULT NULL COMMENT '项目ID',
  `status` enum('normal','nonpayment','refunding','canceled','expired') CHARACTER SET utf8mb4 NULL DEFAULT 'normal' COMMENT '状态:normal=正常,nonpayment=未支付,refunding=退款中,canceled=取消',
  `createtime` bigint(16) NULL DEFAULT NULL COMMENT '添加时间',
  `updatetime` bigint(16) NULL DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '项目记录';

-- ----------------------------
-- Table structure for fa_miniform_order
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__miniform_order`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `orderid` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '订单号',
  `diyform_id` int(10) NULL DEFAULT 0 COMMENT '表单ID',
  `project_id` int(10) NULL DEFAULT 0 COMMENT '项目ID',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID',
  `logs_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '数据ID',
  `amount` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '订单金额',
  `payamount` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '支付金额',
  `ip` varchar(50) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '下单IP',
  `useragent` varchar(255) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT 'UserAgent',
  `paytype` varchar(50) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '支付类型',
  `paytime` bigint(16) NULL DEFAULT NULL COMMENT '支付时间',
  `method` varchar(50) NULL DEFAULT NULL COMMENT '支付方式',
  `memo` varchar(255) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '备注',
  `status` enum('created','paid','expired','refunding','refunded') CHARACTER SET utf8mb4 NULL DEFAULT 'created' COMMENT '状态:created=未支付,paid=已支付,expired=已失效,refunding=退款中,refunded=已退款',
  `createtime` bigint(16) NULL DEFAULT NULL COMMENT '添加时间',
  `updatetime` bigint(16) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '订单表';

-- ----------------------------
-- Table structure for fa_miniform_project
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__miniform_project`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category_id` int(10) NULL DEFAULT NULL COMMENT '分类ID',
  `title` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '标题',
  `images` varchar(1000) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '图片',
  `people_num` int(10) NULL DEFAULT 0 COMMENT '人数',
  `registered` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '参与人数',
  `views` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '浏览数',
  `price` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '价格',
  `front_back` tinyint(4) NULL DEFAULT NULL COMMENT '详情靠前或靠后',
  `is_multi` tinyint(1) NULL DEFAULT 1 COMMENT '是否可多次提交',
  `is_signin` tinyint(1) NULL DEFAULT 1 COMMENT '是否签到',
  `signin_time` varchar(100) NULL DEFAULT '' COMMENT '签到时间',
  `signin_name` varchar(255) NULL DEFAULT '' COMMENT '签到地点',
  `is_verification` tinyint(1) NULL DEFAULT 0 COMMENT '是否开启活动核销',
  `is_need_login` tinyint(1) DEFAULT 1 COMMENT '是否需要登录:0=否,1=是',
  `iscaptcha` tinyint(1) DEFAULT 0 COMMENT '是否开启验证码:0=否,1=是',
  `content` text CHARACTER SET utf8mb4 NULL COMMENT '内容',
  `table` varchar(100) CHARACTER SET utf8mb4 NOT NULL COMMENT '表单名称',
  `label` varchar(150) CHARACTER SET utf8mb4 NOT NULL COMMENT '标签名称',
  `applyfields` varchar(1500) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '报名字段',
  `begintime` bigint(16) NULL DEFAULT NULL COMMENT '开始时间',
  `endtime` bigint(16) NULL DEFAULT NULL COMMENT '结束时间',
  `createtime` bigint(16) NULL DEFAULT NULL COMMENT '添加时间',
  `updatetime` bigint(16) NULL DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) NULL DEFAULT NULL COMMENT '删除时间',
  `status` enum('normal','hidden','expired') CHARACTER SET utf8mb4 NULL DEFAULT 'hidden' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '项目表';

--
-- 1.0.1
--
ALTER TABLE `__PREFIX__miniform_project`
ADD COLUMN `views` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '浏览数' AFTER `registered`,
ADD COLUMN `is_signin` tinyint(1) NULL COMMENT '是否活动签到' AFTER `is_multi`,
ADD COLUMN `signin_time` varchar(100) NULL DEFAULT '' COMMENT '签到时间' AFTER `is_signin`,
ADD COLUMN `signin_name` varchar(255) NULL DEFAULT '' COMMENT '签到信息' AFTER `signin_time`,
ADD COLUMN `is_verification` tinyint(1) NULL DEFAULT 0 COMMENT '是否开启活动核销' AFTER `signin_name`;


CREATE TABLE IF NOT EXISTS `__PREFIX__miniform_template_msg`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ident` tinyint(1) NOT NULL DEFAULT 0 COMMENT '类型:0=签到即将开始,1=签到即将结束,2=项目即将结束',
  `title` varchar(150) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '标题',
  `tpl_id` varchar(50) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '模板ID',
  `diy_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '自定义文本',
  `content` varchar(500) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '内容',
  `page` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '页面路径',
  `switch` tinyint(1) NOT NULL DEFAULT 0 COMMENT '开关',
  `createtime` bigint(16) NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `ident`(`ident`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '模板消息';


CREATE TABLE IF NOT EXISTS `__PREFIX__miniform_subscribe_log`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NULL DEFAULT NULL COMMENT '用户id',
  `logs_id` int(10) DEFAULT '0' COMMENT '日志id',
  `tpl_id` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '模板id',
  `status` tinyint(1) NULL DEFAULT 0 COMMENT '状态:0=未发送,1=已发送',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '订阅记录';


ALTER TABLE `__PREFIX__miniform_project` ADD COLUMN `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间' AFTER `updatetime`;
ALTER TABLE `__PREFIX__miniform_project` ADD COLUMN `is_need_login` tinyint(1) DEFAULT 1 COMMENT '是否需要登录:0=否,1=是' AFTER `is_verification`;
ALTER TABLE `__PREFIX__miniform_project` ADD COLUMN  `iscaptcha` tinyint(1) DEFAULT 1 COMMENT '是否开启验证码:0=否,1=是' AFTER `is_need_login`;

ALTER TABLE `__PREFIX__miniform_logs` ADD COLUMN `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间' AFTER `updatetime`;
