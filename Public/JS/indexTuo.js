	$.ajaxSetup({
	  async: false
	});
	
	function clearAll(){
		window.location.reload();
	}
	/**
	 * 公共请求方法
	 * string str 起始地
	 * string end 目的地
	 * string carid 车型id
	 */
	function getLine(str,end,carid){
		$.post("/Customer/OrderBook/getLine",{str:str,end:end,carid:carid},function(data){
			//console.log(data);
			if(data.flag){
				$("#lineprice").val(data.line);
				$("#product_type").val(data.product_type);
				$("#totalz").val(data.totel);
				$("#tiz").val(data.ti);
				$("#songz").val(data.song);
				$("#maoliz").val(data.mao);
				$("#flag").val("Y");
				$("#totelz").html(data.totel);
				$('#ttotla').html(data.totel);
				//totelFun();
			}else{
				$("#msg").val(data.msg);
				$("#flag").val("N");
				$('#totelz').html(0);
				$("#totalz").html(0);
				$('#ttotla').html(0);
				$("#product_type").val(data.product_type);
				/*
				$("#putong1").html(data.msg);
				$("#heji").html(0);
				$("#totelz").html(0);
				$("#putong2").hide();*/
			}
			
		},'json');
	}

	//费用计算公共方法
	function totelFun(){
		var yunPirce = parseInt($("#totelz").html());
		var baoPirce = parseInt($("#baoprice").html());
		
		var yuns = parseInt($("#yuns").html());
		var bao = parseInt($("#bao").html());
		//var xiprice = parseInt($("#xiprice").html());
                  var xiprice = 0;
		var smsprice = parseInt($("#smsprice").html());
		var youhui = parseInt($('#youhui').html());
		if(!isNaN(yunPirce) && !isNaN(baoPirce)){
			//计算第二步
			var totel = yunPirce+baoPirce;
			$("#totalz").val(totel);
			$("#heji").html(totel);
		}else if(!isNaN(yuns) && !isNaN(bao) && !isNaN(xiprice) && !isNaN(smsprice) && !isNaN(youhui)){
			//计算第三步
			if(youhui!=0){
				var totel = yuns+bao+xiprice+smsprice-youhui;
			}else{
				var totel = yuns+bao+xiprice+smsprice;
			}
			var totels = yuns+bao+xiprice+smsprice;
			$("#heji").html(totel);
			$("#rthrtwe").html(totels);
			$('#totalz').val(totels);
			/*var totel = yuns+bao+xiprice+smsprice-youhui;
			var totels = yuns+bao+xiprice+smsprice;
			$("#heji").html(totel);
			$('#totalz').val(totels);*/
		}
		
	}