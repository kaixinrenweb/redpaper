<?php 
/**
 * addDatas Interface
 * @ctime 2018-01-19 17:10
 * @author tales
 *
 */
header("content-type:text/html;charset=utf-8");

//预加载所需的配置文件信息
require_once("config/pdo.class.php");
require_once("config/config.init.php");
require_once("config/function.php");
require_once("config/commonFuns.php");

//接受参数
$openid   = $_POST['openid'];
$details  = $_POST['details'];
$maxMoney = $_POST['maxMoney'];
$sharepwd = $_POST['sharepwd'];

//获取当前系统的最大的红包金额
$sqls = "select * from ak_red_configs where config_name='money_range' and status=1";
$resultConfig = $pdo->query($sqls, "row");
$configMaxMoney = explode("-", $resultConfig['config_val'])[1];
if($configMaxMoney<$maxMoney){ //用户的红包金额大于本次活动的最大金额
	returnDatas(300, "当前红包金额错误", $sqls);
}

//根据openid获取用户的信息
$sql = "select * from ak_red_users where openid='{$openid}' and status=1";
$resUser = $pdo->query($sql, "row");

//插入数据信息到records表中
$keys = "wechat_name,uid,headimgurl,money,share_pwd,details";
$vals = "'{$resUser['wechat_name']}','{$resUser['id']}','{$resUser['headimgurl']}','{$maxMoney}','{$sharepwd}','{$details}'";
$sqls = "insert into ak_red_records({$keys}) values({$vals})";
$rid = $pdo->insert($sqls);

if($rid){
	returnDatas(200, "success", $sqls);
}else{
	returnDatas(300, "数据入库失败", $sqls);
}


































































