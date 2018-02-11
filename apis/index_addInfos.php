<?php
/**
 * addInfos Interface
 * @ctime 2018-01-18 10:10
 * @author tales
 *
 */
header("content-type:text/html;charset=utf-8");

//预加载所需的配置文件信息
require_once("config/pdo.class.php");
require_once("config/config.init.php");
require_once("config/function.php");
require_once("config/commonFuns.php");

//获取参数的信息
$users    = $_POST['users'];

$users = json_decode($users, true);
//可能是用户刷新页面导致的获取不到openid
if(!$users['openid']){
	returnDatas(300, "请退出重新进入", '');
}

//验证当前的活动时间有没有结束
verifyActivityTime($pdo, 500);

//验证当前的红包领取份额是不是已经领取完了
verifyMaxNums($pdo, 500);

$openid = $users['openid'];
//检查此用户是否已经存在
$sqls = "select * from ak_red_users where openid='{$openid}' and status=1";
$resultUsers = $pdo->query($sqls, "row");
if($resultUsers){//用户已经存在
	//查看此用户是否还有抢红包的机会
	if(!$resultUsers['rest_chance']){//已经没有抢红包的机会了
		returnDatas(400, "没有抢红包的机会了", '');
	}else{
		returnDatas(200, "success", $resultUsers['rest_chance']);
	}
}else{//用户不存在
	//检查用户已经被删除了
	$sqls = "select * from ak_red_users where openid='{$openid}'";
	$resultCurrentUsers = $pdo->query($sqls, "row");
	if($resultCurrentUsers){
		returnDatas(500, "当前用户已删除", '');
	}
	
	//将用户的信息添加到数据库Users表中
	$keys = "wechat_name,openid,sex,headimgurl,country,province,city";
	$headimgurlArr = explode("/", $users['headimgurl']);
	$headimgurlArr[count($headimgurlArr)-1] = 0;
	$headimgurl = join("/", $headimgurlArr);
	$vals = "'{$users['nickname']}','{$users['openid']}','{$users['sex']}','{$headimgurl}','{$users['country']}','{$users['province']}','{$users['city']}'";
	$sql = "insert into ak_red_users({$keys}) values({$vals})";
	$res = $pdo->insert($sql);
	if($res){//用户添加成功
		//生成该用户的分享口令
		$share_pwd = outPwd($res);
		$sqlp = "update ak_red_users set share_pwd='{$share_pwd}' where id={$res}";
		$resultUpdate = $pdo->update($sqlp);
		if($resultUpdate){
			returnDatas(200, "success", 1);
		}else{
			returnDatas(300, "生成红包口令失败", '');
		}
	}else{//用户添加失败
		returnDatas(300, "用户添加失败", '');
	}
}

//生成用户的分享口令信息
function outPwd($uid){
	//$strs = "ABCDEFGHJKLMNPQRSTUVWXY";
	$strs = "123456789";
	$rands1 = mt_rand(0, 8);
	$rands2 = mt_rand(0, 8);
	$rands3 = mt_rand(0, 8);
	$ends = $strs[$rands1].$strs[$rands2].$strs[$rands3];
	return $uid.$ends;
}



































































