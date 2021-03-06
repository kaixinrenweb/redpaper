<?php
header("content-type:text/html;charset=utf-8");
/**
 * 数据库的配置信息
 */
//主机名
define('HOST','');
//用户名
define('USER','');
//密码
define('PWD','');
//数据库
define('DB','');
//字符集
define('CHARSET',"utf8");
//实例化pdo
$pdo = MyPDO::getInstance(HOST, USER, PWD, DB, CHARSET);


/**
 * Redis的配置信息
 */
//用户名
define("REDIS_HOST", "127.0.0.1");
//端口号
define("REDIS_PORT",6379);
//密码
define("REDIS_PWD", "123");

/**
 * 设置信息
 */
//时区设置
date_default_timezone_set('PRC');
//开启session
session_start();
//设置错误级别
error_reporting("E_ALL & ~E_NOTICE");

//每一页显示的条数
$pageSize = 8;


/**
 *  错误的编号 
 */
$errorNo = [
	'101' => "验证错误",
	'102' => "广告位错误",
	
	'501' => "K的参数不可为空",  //crontab触发的任务
	'502' => "没有符合的计划",
	
	'600' => "添加广告信息到redis成功",
	'601' => "添加广告信息到redis失败",
];


















































