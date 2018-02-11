DROP TABLE IF EXISTS `ak_red_users`;
CREATE TABLE `ak_red_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增长的主键id',
  `wechat_name` varchar(50) DEFAULT NULL COMMENT '微信的昵称',
  `openid` varchar(255) DEFAULT NULL COMMENT '微信的用户openid',
  `share_pwd` varchar(20) DEFAULT NULL COMMENT '用户的分享红包的口令',
  `use_share_pwd` varchar(50) DEFAULT NULL COMMENT '已经使用过的好友红包口令,php序列化的字符串存储',
  `parent_id` int(11) DEFAULT NULL COMMENT '分享者的ID',
  `rest_chance` int(11) DEFAULT '1',
  `sex` tinyint(1) DEFAULT '0' COMMENT '用户的性别（默认为0 1->男  2->女）',
  `phone` varchar(20) DEFAULT NULL COMMENT '用户的手机号码',
  `headimgurl` varchar(500) DEFAULT NULL COMMENT '用户的图像的链接地址',
  `province` varchar(20) DEFAULT NULL COMMENT '省份的信息',
  `city` varchar(20) DEFAULT NULL COMMENT '城市的信息',
  `country` varchar(20) DEFAULT NULL COMMENT '国家的信息',
  `status` tinyint(1) DEFAULT '1' COMMENT '当前记录的状态的信息（0=>删除   1=>启用）',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录的创建的时间的信息',
  PRIMARY KEY (`id`),
  KEY `iopenid` (`openid`),
  KEY `iparentid` (`parent_id`),
  KEY `istatus` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ak_red_records`;
CREATE TABLE `ak_red_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增长的主键id',
  `wechat_name` varchar(50) DEFAULT NULL COMMENT '微信的昵称',
  `uid` int(11) DEFAULT NULL COMMENT '用户的ID',
  `headimgurl` varchar(500) DEFAULT NULL,
  `money` float DEFAULT NULL COMMENT '抢到的红包金额',
  `share_pwd` varchar(50) DEFAULT NULL,
  `details` text,
  `state` tinyint(1) DEFAULT '1' COMMENT '红包金额发放状态（1->未发放，2->已发放)',
  `accept_time` varchar(20) DEFAULT NULL COMMENT '发放红包的时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '当前记录的状态的信息（0=>删除   1=>启用）',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录的创建的时间的信息',
  PRIMARY KEY (`id`),
  KEY `iuid` (`uid`),
  KEY `istate` (`state`),
  KEY `istatus` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ak_red_details`;
CREATE TABLE `ak_red_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增长的主键id',
  `uid` int(11) DEFAULT NULL COMMENT '用户的id',
  `wechat_name` varchar(100) DEFAULT NULL,
  `headimgurl` varchar(500) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL COMMENT '所抢红包的ID',
  `money` float DEFAULT NULL COMMENT '所抢的金额',
  `ctime` varchar(20) DEFAULT NULL COMMENT '抢红包的时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '当前记录的状态的信息（0=>删除   1=>启用）',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录的创建的时间的信息',
  PRIMARY KEY (`id`),
  KEY `iuid` (`uid`),
  KEY `irid` (`record_id`),
  KEY `istatus` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ak_red_configs`;
CREATE TABLE `ak_red_configs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增长的主键id',
  `config_name` varchar(50) DEFAULT NULL COMMENT '配置信息的名称',
  `config_val` varchar(100) DEFAULT NULL COMMENT '配置信息的值',
  `remark` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '当前记录的状态的信息（0=>删除   1=>启用）',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录的创建的时间的信息',
  PRIMARY KEY (`id`),
  KEY `iname` (`config_name`),
  KEY `istatus` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;




































