<?php
header("content-type:text/html;charset=utf-8");

//验证当前是否在活动时间范围内
function verifyActivityTime($pdo,$status){
	$sql = "select * from ak_red_configs where config_name='activity_time' and status=1";
	$resTime = $pdo->query($sql, "row");
	$activityTime = $resTime['config_val'];
	$activityTimeArr = explode(">", $activityTime);
	$start_time = strtotime($activityTimeArr[0]);
	$end_time   = strtotime($activityTimeArr[1]);
	$time       = time();
	if(($time<$start_time) ||($time>$end_time)){
		returnDatas($status, "当前不在红包活动时间范围内", '');
	}
}

//验证当前的活动是否有限制，限制的份额是多少，有没有超过
function verifyMaxNums($pdo,$status){
	$sql = "select * from ak_red_configs where config_name='max_nums' and status=1";
	$resMaxNums = $pdo->query($sql, "row");
	if($resMaxNums['config_val']){
		//查询当前剩余的次数
		$sql = "select * from ak_red_configs where config_name='current_nums' and status=1";
		$resCurrentNums = $pdo->query($sql, "row");
		$restNums = $resMaxNums['config_val']-$resCurrentNums['config_val'];
		if(!$restNums){
			returnDatas($status, "本次红包已经抢完啦", '');
		}
	}
}


















































