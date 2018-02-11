<?php 
header( 'Content-Type:text/html;charset=utf-8');
require_once("apis/config/pdo.class.php");
require_once("apis/config/config.init.php");
require_once("apis/config/function.php");

$openid   = $_GET['openid'];
$sharepwd = $_GET['sharepwd'];
if(!$openid){
	echo "页面错误";exit;
}
	
//query
$sql = "select * from ak_red_configs where status=1";
$resConfig = $pdo->query($sql);

$configArr = [];
foreach ($resConfig as $key=>$val){
	$configArr[$val['config_name']] = $val['config_val'];
}

//获取倒计时
$rest_time = $configArr['rest_time'];
//获取多长时间生成一个红包
$create_paper = $configArr['create_paper'];
//获取红包运行多久消失
$paper_time = $configArr['paper_time'];
//金额的区间
$money_range = $configArr['money_range'];
$start_money = explode("-", $money_range)[0];
$end_money   = explode("-", $money_range)[1];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no,width=device-width,initial-scale=1.0"/>
    <title>抢红包啦</title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <link rel="stylesheet" href="statics/css/reset.css"/>
    <link rel="stylesheet" href="statics/css/get.css"/>
    <meta name="x5-fullscreen" content="true">
	<meta name="full-screen" content="yes">
	<script>
		//第一次进来
		if(typeof localStorage.isFirst == 'undefined'){
			alert("当前来源页面错误，请重新进入");
			location.href = "index.php";
		}
		localStorage.removeItem("isFirst");
	</script>
</head>
<body>

	<!-- ready -->
	<div class="ready">
		<span><img src="statics/images/ready-5.png"/></span>
	</div>
	
	<!-- mask -->
	<div class="mask"></div>
	
	<!-- many paper -->
	<div class="papers">
		<!-- <a class="position-1 all-position" href="javascript:;" onclick="getit(0.6, this);">
			<img class="small-img" src="statics/images/paper/small-1.png">
			<span>0.6</span>
		</a>  -->
	</div>
	
	<!-- rest time -->
	<div class="rest-time">倒计时<span></span>秒</div>
	
	<!-- show-result -->
	<div class="show-result">
		<p><span class="paper-sum"></span>个包,最大包<span class="paper-max">￥</span></p>
		<ul class="show-detail">
			<!-- <li>
				<span class="fl">1</span>
				<span class="fl">￥2.8</span>
				<span class="fl">13:17:20 345</span>
			</li> -->
		</ul>
		<a href="javascript:;"></a>
	</div>
	
	<!-- error-tips -->
	<div class="error-tips"></div>
	
	<div class="mask-load">
		<img src="statics/images/loads.gif" />
	</div>
	
<script src="statics/js/zepto.min.js"></script>
<script src="statics/js/common.js"></script>

<script>
	var overscroll = function(el) {
	  el.addEventListener('touchstart', function() {
	    var top = el.scrollTop
	      , totalScroll = el.scrollHeight
	      , currentScroll = top + el.offsetHeight;
	    if(top === 0) {
	      el.scrollTop = 1;
	    } else if(currentScroll === totalScroll) {
	      el.scrollTop = top - 1;
	    }
	  });
	  el.addEventListener('touchmove', function(evt) {
	    if(el.offsetHeight < el.scrollHeight)
	      evt._isScroller = true;
	  });
	}
	overscroll(document.querySelector('.show-detail'));
	document.body.addEventListener('touchmove', function(evt) {
	  if(!evt._isScroller) {
	    evt.preventDefault();
	  }
	});
/*****************************************************************************************************/
	
	//get dom nodes 
	var $mask       = $(".mask");
	var $ready      = $(".ready");
	var $img        = $(".ready img");
	var $papers     = $(".papers");
	var $restTime   = $(".rest-time");
	var $timeSpan   = $(".rest-time span");
	var $showResult = $(".show-result");
	var paperMoneyTime = [];
	//能不能领取红包的标识
	var isGetPaper = true;
	
	//拆红包
	function getit(money,_this){
		//拆的红包的时间和金额保存起来
		var date = new Date();
		var obj = {};
		obj.money = money;
		obj.timeStr = parseInt(date.getTime()/1000);
		var hours = date.getHours()<10 ? "0"+date.getHours() : date.getHours();
		var minutes = date.getMinutes()<10 ? "0"+date.getMinutes() : date.getMinutes();
		var seconds = date.getSeconds()<10 ? "0"+date.getSeconds() : date.getSeconds();
		var times = hours+":"+minutes+":"+seconds+" "+date.getMilliseconds();
		obj.time = times;
		paperMoneyTime.push(obj);
		
		//显示红包的金额
		$(_this).children("span").show();
		setTimeout(function(){
			$(_this).remove();
		}, 400);
	}
	
	//JS生成随机的整数 [min,max]
	function GetRandomNum(Min,Max){   
		var Range = Max - Min;   
		var Rand = Math.random();   
		return(Min + Math.round(Rand * Range));   
	};   
	
	//红包最终消失的位置(0-13)
	var btmPx = "700px";
	var finallyPosition = [{left:"-80px",bottom:btmPx},{left:"-60px",bottom:btmPx},
	                       {left:"-40px",bottom:btmPx},{left:"-20px",bottom:btmPx},
	                       {left:"0",bottom:btmPx},{left:"50px",bottom:btmPx},
	                       {left:"100px",bottom:btmPx},{left:"150px",bottom:btmPx},
	                       {left:"250px",bottom:btmPx},{left:"300px",bottom:btmPx},
	                       {left:"350px",bottom:btmPx},{left:"400px",bottom:btmPx},
	                       {left:"450px",bottom:btmPx},{left:"500px",bottom:btmPx}];
	//红包出现的初始的位置(0-5)
	var positionArr     = ['position-1','position-2','position-3','position-4','position-5','position-6'];
	//红包运行的方式(0-6)
	var paperMove       = ['ease','linear','ease-in', 'linear', 'ease-out', 'linear', 'ease-in-out'];
	//红包的大小和无(0-3)
	var paperImg        = ['none', 'middle', 'big', 'small'];
	
	/* setTimeout(function(){
		$(".position-1").animate(finallyPosition[8], 8000, 'ease-in-out', function(){
			$(".position-1").remove();
		});
	}, 1000); */
	
/**************************************************************************************************************/	
	//构造函数
	var Ready = function(){
		
	}

	//封装的显示当前的error-tips
	Ready.prototype.errorTips = function(msg){
		$(".error-tips").html(msg);
		$(".error-tips").show();
		setTimeout(function(){
			$(".error-tips").hide();
		}, 4000);
	};
	
	//页面倒计时
	Ready.prototype.countDown = function(){
		var initCount = 5;
		var _self = this;
		var timer = setInterval(function(){
			initCount--;
			if(initCount==0){
				clearInterval(timer);
				//遮罩层关闭
				$mask.hide();
				$ready.hide();
				//发红包
				_self.sendPaper();
				return;
			}
			$img.attr("src", "statics/images/ready-"+initCount+".png");
		}, 1000);
	};
	
	//发红包的定时器
	var sendTimer = null;
	//发红包
	Ready.prototype.sendPaper = function(){
		var _self = this;
		//倒计时
		var restTimes = <?php echo $rest_time;?>;
		$timeSpan.html(restTimes);
		$restTime.show();
		//倒计时减少
		var restTimer = setInterval(function(){
			restTimes--;
			if(restTimes==-1){
				clearInterval(restTimer);
				clearInterval(sendTimer);
				$papers.empty();
				//获取到当前所抢的红包数据信息，ajax发送到数据库,参数是当前用户的openid
				_self.operateDatas();
				return;
			}
			restTimes = (restTimes<10) ? "0"+restTimes : restTimes;
			if(parseInt(restTimes)<=5){
				$timeSpan.css({color:"#ef4557"});
			}
			$timeSpan.html(restTimes);
		}, 1000);
		
		//运行多长时间结束
		var moveTime = <?php echo $paper_time;?>;
		var index = 1;
		//生成金额范围
		var moneyMin = <?php echo $start_money;?>;
		var moneyMax = <?php echo $end_money;?>;
		var fen = GetRandomNum(6, 8);
		if(moneyMax-moneyMin > 1){
			var moneyGap = (moneyMax-moneyMin)/fen;
			moneyGap = moneyGap.toString();
			var moneyPer = "";
			if(moneyGap.indexOf(".")==-1){
				moneyPer = moneyGap;
			}else{
				perArr = moneyGap.split(".");
				perArr[1] = perArr[1].slice(0, 1);
				moneyPer = perArr.join(".");
			}
			moneyPer = Number(moneyPer);
			var moneyRandTotal = [];
			for(var i=1; i<=fen; i++){
				moneyRandTotal.push((moneyMin+i*moneyPer).toFixed(1));
			}
			var mins = moneyMin;
			var maxs = moneyRandTotal[GetRandomNum(0, fen-1)];
		}else{
			var mins = moneyMin;
			var maxs = moneyMax;
		}
		//console.log(mins, maxs);
		
		//生成红包
		sendTimer = setInterval(function(){
			var randStartPosition   = GetRandomNum(0, 5);        //生成初始位置随机数
			var randFinallyPosition = GetRandomNum(0, 13);       //生成最终位置的随机数
			var randPaperMove       = GetRandomNum(0, 6);        //生成红包运动方式的随机数
			var randPaperImg        = GetRandomNum(0, 3);        //生成红包大小和无的随机数
			var randMoney           = GetRandomNum(mins*10, maxs*10)/10; //获取红包金额大小 
			var randPaperRotate     = GetRandomNum(1, 6);        //生成红包的旋转方式
			var sp = positionArr[randStartPosition];             //开始位置
			var fp = finallyPosition[randFinallyPosition];       //结束位置
			var pm = paperImg[randPaperImg];                     //红包大小
			var ids = sp+"-"+index;                              //id
			randMoney = (pm=='none')?0:randMoney;
			var strs = '<a class="'+sp+' all-position" id="'+ids+'" href="javascript:;" onclick="getit('+randMoney+', this);"><img class="'+pm+'-img" src="statics/images/paper/'+pm+'-'+randPaperRotate+'.png"><span>'+randMoney+'</span></a>';
			$papers.append(strs);
			$("#"+ids).animate(fp, moveTime, paperMove[randPaperMove], function(){
				$("#"+ids).remove();
			});
			index++;
		}, <?php echo $create_paper;?>);
	};
	
	//红包抢完后的显示和数据操作
	Ready.prototype.operateDatas = function(){
		//console.log(paperMoneyTime);
		//遮罩层开启，倒计时关闭
		$restTime.hide();
		$mask.show();
		setTimeout(function(){
			$showResult.show();
		}, 700);
		//fill datas
		$(".paper-sum").html(paperMoneyTime.length);    //一共抢了多少个红包
		var maxMoney = 0;
		var manyLis = "";
		for(var i=0; i<paperMoneyTime.length; i++){
			var cur = paperMoneyTime[i];
			maxMoney = (cur.money>maxMoney) ? cur.money : maxMoney;  //最大的红包
			manyLis += '<li>'+
						 '<span class="fl">'+(i+1)+'</span>'+
						 '<span class="fl">￥'+cur.money+'</span>'+
						 '<span class="fl">'+cur.time+'</span>'+
					   '</li>';
		}
		$(".paper-max").html("￥"+maxMoney);
		$(".show-detail").html(manyLis);
		//将数据信息写到数据库(ajax)
		this.fillDatas(paperMoneyTime, maxMoney);
	};

	var resultMaxMoney = 0;
	//红包抢完后，发送数据信息到服务器
	Ready.prototype.fillDatas = function(paperMoneyTime, maxMoney){
		resultMaxMoney = maxMoney;
		if(parseInt(maxMoney)==0) return;
		var openid = '<?php echo $openid;?>';
		var details = JSON.stringify(paperMoneyTime);
		var _self = this;
		$.ajax({
			url : "apis/get_addDatas.php",
			type: "post",
			data: {openid: openid, details: details, maxMoney: maxMoney, sharepwd:'<?php echo $sharepwd;?>'},
			dataType: "json",
			success: function(re){
				if(parseInt(re.status)==300){
					isGetPaper = false;
					_self.errorTips(re.message);
				}
			}
		});
	};

	//领取红包的按钮触发事件
	Ready.prototype.clickPaper = function(){
		var _self = this;
		var openid = '<?php echo $openid;?>';
		$(".show-result>a").on("click", function(){
			if(!isGetPaper){
				_self.errorTips('当前红包数据信息出问题了');
				return;
			}
			//ajax发起请求，通过微信支付给当前用户打钱
			//打钱成功后直接跳转到分享的页面
			location.href = "share.php?openid="+openid+"&maxMoney="+resultMaxMoney;
		});
	};
	
	//初始化执行
	Ready.prototype.init = function(){
		this.countDown();
		this.clickPaper();
	};
	
	var ready = new Ready();
	window.onload = function(){
		ready.init();
	}
	
		

</script>

</body>
</html>







































































