<?php
header( 'Content-Type:text/html;charset=utf-8');
require_once("apis/config/pdo.class.php");
require_once("apis/config/config.init.php");
require_once("apis/config/function.php");

function https_request($url, $data = null){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	if (!empty($data)){
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}
	
$code = $_GET['code'];
$state = $_GET['state'];

if(!$code){
	echo "当前页面错误";exit;
}

$appid     = "";
$appsecret = "";
$accessTokenUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
$res = json_decode(https_request($accessTokenUrl),true);

$userUrl = "https://api.weixin.qq.com/sns/userinfo?access_token={$res['access_token']}&openid={$res['openid']}&lang=zh_CN";
$resUser = json_decode(https_request($userUrl),true);

//获取用户的相关的微信资料信息
$openid       = $resUser['openid'];         
$wechat_name  = $resUser['nickname'];
$sex          = $resUser['sex'];
$headimgurl   = $resUser['headimgurl'];
$country      = $resUser['country'];
$province     = $resUser['province'];
$city         = $resUser['city'];

$userJsons = json_encode($resUser);

//获取slogan
$sql = "select * from ak_red_configs where config_name='slogan' and status=1";
$resSlogan = $pdo->query($sql, "row");
$slogan = $resSlogan['config_val'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no,width=device-width,initial-scale=1.0"/>
    <title>输口令抢红包</title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <link rel="stylesheet" href="statics/css/reset.css"/>
    <link rel="stylesheet" href="statics/css/index.css"/>
    <script>
    window.addEventListener('pageshow', function(e) {
        // 通过persisted属性判断是否存在 BF Cache
        if (e.persisted) {
            location.reload();
        }
    });
    </script>
</head>
<body>

<div class="pages">

	<!-- slogan -->
	<div class="slogan"><?php echo $slogan;?></div>

	<!-- inputs -->
	<div class="inputs-wrap">
		<input type="text" id="share_pwd" placeholder="输口令，抢红包"/>
	</div>
	
	<!-- mask -->
	<div class="mask"></div>
	
	<!-- loading -->
	<div class="loading">
		<img src="statics/images/loads.gif"/>
	</div>
	
	<!-- error-tips -->
	<div class="error-tips"><!-- 客官，您的口令输入有误，抓紧时间重新输入哦 --></div>
	
	<!-- rest chance -->
	<div class="rest-chance">你可以抢<span></span>次红包</div>
	
	<!-- copyright -->
	<div class="copyright">© 上海安康生物市场企划部提供服务</div>
	
</div>

<script src="statics/js/zepto.min.js"></script>
<script src="statics/js/common.js"></script>
<script>
$('body').on('touchmove', function (event) {
    event.preventDefault();
});
	
	(function(){
		//get dom nodes
		var $mask      = $(".mask");
		var $loading   = $(".loading");
		var $tips      = $(".error-tips");
		var $chance    = $(".rest-chance");
		var $chanceTxt = $(".rest-chance span");
		var $sharePwd  = $("#share_pwd");
		
		var Welcome = function(){
			
		};
		//页面刚进来，获取用户的微信资料的信息，发送到服务器
		Welcome.prototype.firstStep = function(){
			var _self = this;
			$.ajax({
				url: "apis/index_addInfos.php",
				data: {users: JSON.stringify(<?php echo $userJsons;?>)},
				type: "post",
				dataType: "json",
				success: function(re){
					if(parseInt(re.status)==300){ //error
						_self.errorTips(re.message);
					}
					if(parseInt(re.status)==200){ //success
						$chance.show();
						$chanceTxt.html(re.result);
					}
					if(parseInt(re.status)==400){ //no chance
						$chance.show();
						$chanceTxt.html(0);
						$sharePwd.attr("disabled", true);
					}
					if(parseInt(re.status)==500){
						$chance.show();
						$chance.html(re.message);
						$sharePwd.attr("disabled", true);
					}
				}
			});
		};
		
		//口令框焦点失去事件
		Welcome.prototype.inputBlur = function(){
			var _self = this;
			$sharePwd.blur(function(){
				var val = $("#share_pwd").val();
				if(val==""){
					_self.errorTips("口令不可为空哦");
					return;
				}
				_self.ajaxSend(val);
			});
		};
		
		//封装的显示当前的error-tips
		Welcome.prototype.errorTips = function(msg){
			$(".error-tips").html(msg);
			$tips.show();
			setTimeout(function(){
				$tips.hide();
			}, 2000);
		};
		
		//ajax请求数据库
		Welcome.prototype.ajaxSend = function(val){
			var _self = this;
			$.ajax({
				url: "apis/index_checkInfos.php",
				data: {inputVal: val, openid: '<?php echo $openid;?>'},
				type: "post",
				dataType: "json",
				success: function(re){
					if(parseInt(re.status)==300){
						$chance.hide();
						_self.errorTips(re.message);
					}else{
						//返回成功，直接跳转到领取红包的界面
						//_self.errorTips(re.result);
						localStorage.isFirst = 1;
						location.href = "get.php?openid="+re.result+"&sharepwd="+val;
					}
				}
			});
		};
		
		//初始化
		Welcome.prototype.init = function(){
			this.firstStep();
			this.inputBlur();
		};

		var welcome = new Welcome();
		welcome.init();
		
	})();
	
</script>

</body>
</html>



















































