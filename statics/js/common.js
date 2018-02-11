/**
*	commonJS 
*	author	tales
*	ctime	2018-01-17 15:50
*/

(function(){
	toSize();
	window.addEventListener("resize", toSize);
	function toSize(){
		var html  = document.documentElement;   //html
		var width = html.clientWidth;           //html-width
		if(width>675){
			html.style.fontSize = "90px";
		}else{
			var nums = 7.5;                   //rem的基值
			html.style.fontSize = width/nums + "px"; 
		}
	}
})();
































































