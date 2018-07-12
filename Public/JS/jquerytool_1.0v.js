(function($) {
$.fn.upLoads = function(options) {
	 var defaults = {
        'LENGTH': 10*1024*1024
	 };
	 var settings = $.extend(defaults, options);
	 jQuery.upfile({'LENGTH':settings.LENGTH});

};

jQuery.extend({
	//私有函数
	XHR:function(){
		var xhr;
		try {
			xhr = new XMLHttpRequest();
	    }catch(e) {
				var IEXHRVers =["Msxml3.XMLHTTP","Msxml2.XMLHTTP","Microsoft.XMLHTTP"];
				for (var i=0,len=IEXHRVers.length;i< len;i++) {
					try {
						xhr = new ActiveXObject(IEXHRVers[i]);
					}catch(e) {
						continue;
					}
				}
		}
		return xhr;
	},
	//开始上传
	upfile:function(a){
		//设置文件切割起始和结束
		  start=0;
		  end=a.LENGTH+start;
		//调用真正上传函数
		  as={'LENGTH':end,'start':start};
		  jQuery.up(as);
	},
	//上传方法
	up:function(b){
		//创建XMLHttpRequest对象
		 var xhr= jQuery.XHR();
		 //文件名称
		 var name = '';
		 //获取显示进度的DIV对象
		 var des=document.getElementById('load');
		 //文件对象
		 var file;
		//设置上传进度
		 var pecent;
		//设置文件切割起始点
		 var start=b.start;
		//获取上传文件对象
		  file=document.getElementsByName('mof')[0].files[0];
		  //判断上传文件对象是否存在
		  if(!file){
			   alert('请选择文件');
			   return;
		  }
		  //console.log(file[0].files);return false;
		  //判断上传文件是否大于默认起始切割点0，也就是文件大小是否大于0
		  if(b.start<file.size){
		       //设置上传回调函数
			   xhr.onreadystatechange=function(){
		    		//获取服务器响应状态
				    if(this.readyState==4){
				    	     //获取服务响应状态
						     if(this.status>=200&&this.status<300){
						    	//获取服务器返回值
							      if(this.responseText.indexOf('failed') >= 0){
						    	       //如果上传失败，给出提示，并将进度条设置为0
								       alert('文件发送失败，请重新发送');
								       des.style.width='0%';
							       }else{
							    	   //如果上传成功继续切割文件上传
							    	   start = b.LENGTH;
							    	   end = start + b.LENGTH;
							    	   bs = {'LENGTH':end,'start':start};
							    	   setTimeout('jQuery.up('+JSON.stringify(bs)+')',1000);
							        }
					          }
				         }
	                 }
			  	   //时时更改上传进度条状态
			  	   xhr.upload.onprogress=function(ev){
					    if(ev.lengthComputable){
						     pecent=100*(ev.loaded+start)/file.size;
						     if(pecent>100){
						      pecent=100;
				     		 }
						     //num.innerHTML=parseInt(pecent)+'%';
						     des.style.width=pecent+'%';
						     des.innerHTML = parseInt(pecent)+'%';
					    }
				   }
		　　　       	   //分割文件核心部分slice
			  	   var fr = new FileReader();
				   var blob=file.slice(b.start,b.LENGTH);
				   var fd=new FormData();
				   fd.append('mof',blob);
				   fd.append('test',file.name);
				   fd.append('size',file.size);
				   //组装上传参数(上传方式，上传路径，是否异步执行)
				   xhr.open('POST','/index.php/Front/Base/fileUp',true);
				   //xhr.setRequestHeader("Content-Type",'multipart/form-data; boundary=' + boundary);
				   //发送上传请求
				   xhr.send(fd);
	  }else{
		  alert("上传完成");
	   	  //clearInterval(clock);
	  }
	},
	//过滤富文本编辑框html只提取文字
	RichTextHtml:function(str){
		str = str.replace(/(\n)/g, "");
		str = str.replace(/(\t)/g, "");
		str = str.replace(/(\r)/g, "");
		str = str.replace(/<\/?[^>]*>/g, "");
		str = str.replace(/\s*/g, "");
		return str;
	},
	//是否为正整数
	isPositiveNum:function(s){
		var re = /^[0-9]*[1-9][0-9]*$/ ;
		return re.test(s);
	},
	//验证手机号
	check_Mobile:function(val){
		//var mobile = /^((\+?86)|(\(\+86\)))?(13[012356789][0-9]{8}|15[012356789][0-9]{8}|18[02356789][0-9]{8}|147[0-9]{8}|1349[0-9]{7})$/;
		var mobile =/^0?1[3|4|5|7|8|9][0-9]\d{8}$/;
		if(mobile.test(val) == false){
			return false;
		}else{
			return true;
		}
	},
	//验证邮箱
	check_Emails:function(str){
		var email = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
		if(email.test(str) == false){
	        return false;
	    }else{
	    	return true;
	    }
	},
	/**
	 * msg:提示信息
	 * flag:标示  0/1，标示是否有取消按钮
	 * mark:标示 0/1/2, 是否有下一步链接地址/有下一步链接地址(为window.location地址)/form表单提交
	 * url: 地址或form ID
	 */
	anewAlert:function(msg,flag,mark,url){
		var html = '';
		html+='<div id="tcc_phone_id" style="width:40%!important;height:auto!important;padding:33px 15px 15px 15px!important;position:fixed!important;left:50%!important;margin-left:-22%!important;top:50%!important;margin-top:-175px!important;background:#fff!important;display:none!important;z-index:20001!important;text-align:center!important;line-height:80px!important;border-radius:8px!important;">';
		html+='<h3 id="spansd_id" style="padding:0px 15px 50px 15px!important;display:block!important;font-size:16px;border-bottom: solid 1px #ddd!important;line-height: 1.5!important;height: auto!important;word-wrap:break-word!important;text-align:center!important; ">jquerytool_1.0v</h3>';
		html+='<input type="hidden" id="new_mark" value="" />';
		html+='<input type="hidden" id="new_urlMark" value="" />';
		html+='<button class="btn1 erre" onclick="$.asureLwtFun(this);" style="margin-left:0px!important;margin-right:10px!important;padding:6px 35px;background:#30AD64;border-radius:3px;color:#fff;color:#fff;margin-top:15px;line-height:30px;border:0px;text-align:center;" type="button">确定</button>';
		if(flag==1){
			html+='<button class="btn3" onclick="$.asureLwtFunClose();" type="button" data-target=".bs-example-modal-lg" data-toggle="modal" style="margin-left:0px!important;padding:6px 35px;background:#999;border-radius:3px;color:#fff;color:#fff;text-align:center;margin-top:15px;line-height:30px;border:0px;">取消</button>';
		}
		html+='</div>';
		html+='<div id="phone_bg_id" style="background:rgba(0,0,0,0.5)!important;display:none!important;position:fixed!important;left:0!important;top:0!important;width:100%!important;height:100%!important;z-index:20000!important;"></div>';
		$("#tcc_phone_id").remove();
		$("#phone_bg_id").remove();
		$("body").append(html);
		$("#spansd_id").html(msg);
		$('#new_urlMark').val(url);
		$('#new_mark').val(mark);
		$("#tcc_phone_id").show();
		$("#phone_bg_id").show();
	},
	//alert(确定按钮)
	asureLwtFun:function(obj){
		 $("#spansd_id").html('');
		 $("#tcc_phone_id").hide();
		 $("#phone_bg_id").hide();
		 var new_mark = $('#new_mark').val();
		 var new_urlMark = $("#new_urlMark").val();
		 if(new_mark==1){
			 window.location.href = new_urlMark;
		 }else if(new_mark==2){
			 $("#"+new_urlMark).submit();
		 }else if(new_mark == 0){
			 return false;
		 }
	},
	asureLwtFunClose:function(){
		//$("#tcc_phone_id").remove();
		//$("#phone_bg_id").remove();
		$("#tcc_phone_id").hide();
		$("#phone_bg_id").hide();
	},
	isIdCardNo:function(num){
		//身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X。
		num = num.toUpperCase();
	    if (!(/(^\d{15}$)|(^\d{17}([0-9]|X)$)/.test(num))) {
	        //alert('输入的身份证号不正确！');
	        return false;
	    } //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
	     return true;
	}

});
})(jQuery);