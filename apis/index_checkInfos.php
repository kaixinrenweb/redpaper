<?php
/**
 * CheckInfos Interface
 * @ctime 2018-01-18 10:10
 * @author tales
 *
 */

header( 'Content-Type:text/html;charset=utf-8');

//预加载所需的配置文件信息
require_once("config/pdo.class.php");
require_once("config/config.init.php");
require_once("config/function.php");
require_once("config/commonFuns.php");

//获取参数的信息
$inputVal = $_POST['inputVal'];
$openid   = $_POST['openid'];

//可能是用户刷新页面导致的获取不到openid
if(!$openid){
	returnDatas(300, "请退出重新进入", '');
}

//查询用户的信息
$sqls = "select * from ak_red_users where openid='{$openid}' and status=1";
$resultCurrentUsers = $pdo->query($sqls, "row");
if(!$resultCurrentUsers){
	returnDatas(300, "当前用户已删除" , "");
}

//验证
//1.验证当前是否在活动时间范围内
verifyActivityTime($pdo, 300);

//2.验证当前的活动是否有限制，限制的份额是多少，有没有超过
verifyMaxNums($pdo, 300);

//验证自己还剩余的抢红包的次数
$sql = "select * from ak_red_users where openid='{$openid}'";
$myUser = $pdo->query($sql,"row");
if(!$myUser['rest_chance']){
	returnDatas(300, "没有抢红包的机会了", '');
}

//3.判断输入的红包口令是否正确
//首先判断是不是本次活动的口令
$sql = "select * from ak_red_configs where config_name='share_pwd' and status=1";
$resPwd = $pdo->query($sql, "row");
$commonPwd = $resPwd['config_val'];
if($commonPwd==$inputVal){//判断口令和本次活动的口令是否一致
	//本次活动的口令输入成功，剩余机会-1
	$sql = "update ak_red_users set rest_chance=rest_chance-1 where openid='{$openid}'";
	$resUpdate = $pdo->update($sql);
	//当前领取红包的份数+1
	$sql = "update ak_red_configs set config_val=config_val+1 where config_name='current_nums' and status=1";
	$resConfig = $pdo->update($sql);
	returnDatas(200, "success", $openid);
}else{//判断口令是不是好友分享的口令
	//$inputVal = strtoupper($inputVal);
	$sql = "select * from ak_red_users where share_pwd='{$inputVal}' and openid<>'{$openid}' and status=1";
	$resultUsers = $pdo->query($sql, "row");
	if($resultUsers){
		//查询出此用户的use_share_pwd
		$use_share_pwd = $resultCurrentUsers['use_share_pwd'];
		if($use_share_pwd){//已经存在
			$use_share_pwd = unserialize($use_share_pwd);
			if(in_array($inputVal, $use_share_pwd)){
				returnDatas(300, "不可重复分享好友红包口令", $openid);
			}
			array_push($use_share_pwd, $inputVal);
			$use_share_pwd = serialize($use_share_pwd);
		}else{//不存在
			$arrs = [$inputVal];
			$use_share_pwd = serialize($arrs);
		}
		//将此用户的rest_chance-1,use_share_pwd
		$sql = "update ak_red_users set use_share_pwd='{$use_share_pwd}',rest_chance=rest_chance-1,parent_id='{$resultUsers['id']}' where openid='{$openid}'";
		$resUpdate = $pdo->update($sql);
		//将分享给他的用户的rest_chance+1
		$sql = "update ak_red_users set rest_chance=rest_chance+1 where id='{$resultUsers['id']}'";
		$resUpdate = $pdo->update($sql);
		//当前领取红包的份数+1
		$sql = "update ak_red_configs set config_val=config_val+1 where config_name='current_nums' and status=1";
		$resConfig = $pdo->update($sql);
		returnDatas(200, "success", $openid);
	}else{
		returnDatas(300, "客官，您的口令输入有误，抓紧时间重新输入哦", $openid);
	}
}































