$(function (){
var html = '';
	html+='<div id="tcc_phone_id" style="width:70%;height:auto;padding:15px;position:fixed;left:50%;margin-left:-35%;top:50%;margin-top:-175px;background:#fff;display:none;z-index:10000;text-align:center;line-height:80px;border-radius:8px;">';
	html+='<span id="spansd_id" style="display:block;border-bottom: solid 1px #ddd;line-height: 1.5;padding: 5% 0;height: auto;word-wrap:break-word;text-align:center; ">请先关注公众号</span>';
	html+='<a href="javascript:;" style="display:block;width:100px;height:35px;font-size:14px;color:#fff;line-height:35px;text-align:center;background:#F00;border-radius:4px;margin:0 auto;margin-top: 6%;" onclick="asureFun();">确定</a>';
	html+='</div>';
	html+='<div id="phone_bg_id" style="background:rgba(0,0,0,0.5);display:none;position:fixed;left:0;top:0;width:100%;height:100%;z-index:1000;"></div>';
	$("body").append(html);
});

//alert重组

//function alert(m){
//	 	$("#spansd_id").html(m);
//	    $("#tcc_phone_id").show();
//	    $("#phone_bg_id").show();
//}

function asureFun(){
	 $("#spansd_id").html('');
	 $("#tcc_phone_id").hide();
	 $("#phone_bg_id").hide();
}

//获取滚动条当前的位置 
function getScrollTop() { 
	var scrollTop = 0; 
	if (document.documentElement && document.documentElement.scrollTop) { 
		scrollTop = document.documentElement.scrollTop; 
	}else if (document.body) { 
		scrollTop = document.body.scrollTop; 
	} 
	return scrollTop; 
} 

//获取当前可是范围的高度 
function getClientHeight() { 
	var clientHeight = 0; 
	if (document.body.clientHeight && document.documentElement.clientHeight) { 
		clientHeight = Math.min(document.body.clientHeight, document.documentElement.clientHeight); 
	}else{ 
		clientHeight = Math.max(document.body.clientHeight, document.documentElement.clientHeight); 
	} 
	return clientHeight; 
} 

//获取文档完整的高度 
function getScrollHeight() { 
	return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight); 
}
