<?php 
header( 'Content-Type:text/html;charset=utf-8');
require_once("apis/config/pdo.class.php");
require_once("apis/config/config.init.php");
require_once("apis/config/function.php");

$openid   = $_GET['openid'];
$maxMoney = $_GET['maxMoney'];
if(!$openid){
	echo "页面错误";exit;
}

//根据openid获取用户的基本信息
$sql = "select * from ak_red_users where openid='{$openid}' and status=1";
$users = $pdo->query($sql, "row");

$headimgurl = $users['headimgurl'];    //图像
$share_pwd  = $users['share_pwd'];     //该用户的分享的红包口令

//远程的图片保存到本地
$res = getImage($headimgurl, 'statics/images/headimg', $openid.".jpg", 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no,width=device-width,initial-scale=1.0"/>
    <title>分享抢红包</title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <link rel="stylesheet" href="statics/css/reset.css"/>
    <link rel="stylesheet" href="statics/css/share.css"/>
    <meta name="x5-fullscreen" content="true">
	<meta name="full-screen" content="yes">
</head>
<body>
	
	<div class="big-imgs"><img src="statics/images/share-bg.jpg"/></div>
	
	<!-- headimg -->
	<div class="headimg">
		<img src="statics/images/headimg/<?php echo $openid?>.jpg"/>
	</div>
	
	<!-- maxMoney -->
	<div class="maxMoney"><?php echo $maxMoney;?></div>
	
	<!-- share-pwd -->
	<div class="share-pwd">分享口令：<?php echo $share_pwd;?></div>
	
	<div class="share-img" style="position:absolute;left:0;right:0;top:0;bottom:0;display:none;z-index:9;">
		<img style="max-width:100%;" src="" id="share-img-res"/>
	</div>
	
<script src="statics/js/common.js"></script>
<script src="statics/js/html2canvas.js"></script>
<script src="statics/js/canvas2image.js"></script>
<script>
	document.body.addEventListener('touchmove', function(evt) {
	  if(!evt._isScroller) {
	    evt.preventDefault();
	  }
	});

/*******************************************************************************************/
	window.onload = function(){
		document.querySelector(".share-img").style.display = "block";
	   var shareContent = document.documentElement;
	   console.log(shareContent);
	   var width = shareContent.clientWidth; //获取dom 宽度
	   var height = shareContent.clientHeight; //获取dom 高度
	   var canvas = document.createElement("canvas"); //创建一个canvas节点
	   var scale = 2; //定义任意放大倍数 支持小数
	   canvas.width = width * scale; //定义canvas 宽度 * 缩放
	   canvas.height = height * scale; //定义canvas高度 *缩放
	   canvas.getContext("2d").scale(scale,scale); //获取context,设置scale 
	   var opts = {
	       scale:scale, // 添加的scale 参数
	       canvas:canvas, //自定义 canvas
	       logging: false, //日志开关
	       width:width, //dom 原始宽度
	       height:height //dom 原始高度
	   };
	   html2canvas(shareContent, opts).then(function (canvas) {
		    var img = Canvas2Image.convertToImage(canvas, canvas.width, canvas.height);
		    //console.log(img.src);
		    document.querySelector("#share-img-res").src = img.src;
	   });
	};
		

</script>

</body>
</html>







































































