<?php
namespace Customer\Controller;
/**
 * 下单流程
 * @author Administrator
 *
 */
class OrderBookController extends BaseController {
	
	 /**
	  * 判断是否已经绑定过手机号
	  */
	 function overBooking(){
	 	$code= I("code");
		$appid=C("APPID");   //appid
		$secret=C("SECRET");   //appid
		$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code";
		$outputUrl = sprintf($url,$appid,$secret,$code);
		$res=json_decode(https_request($outputUrl),true);
		print_log("--------进入下单------------".json_encode($res));
		$openid_s = $res['openid']==""||$res['openid']==null?session("openid"):$res['openid'];//'oGkTyv0YBT2e_9_r1M7orMB0Ilxg';//
		$arr = fileRead('access_token.json');
		$times = time();
		if($times<$arr['expire_time']){
			$access_token = $arr['access_token'];
		}else{
			$access_token = get_token($appid,$secret);
		}
		print_log("打印客户OpenId:".$openid_s);
		if($openid_s!=""){   //获取openid
			session("openid",$openid_s);
			$obj = D('Login');
			$ret = $obj->readUserInfo($openid_s);
			$urls = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid_s.'&lang=zh_CN';
			$ress=json_decode(https_request($urls),true);
			session("userInfo",$ress);
			print_log("info:".json_encode($ress));
			print_log("data:".json_encode($ret));
			if(count($ret)>0){
				session("userdata",$ret);
				$pro = get_province();
				$brand = get_brand();
				$this->assign("userInfo",$ress);
				$this->assign("member",$ret);
				$this->assign("pro",$pro);
				$this->assign("brand",$brand);
				$this->display("OrderInfo:order_one");
			}else{
				$this->display("Login:mobile_login_order");
			}
		}else{
			//暂定
			$this->display("Login:mobile_login_order");
		}
	 	
	 }
	 
	 /**
	  * 进入下单页1
	  */
	 function mobileBooking(){
	 	$ret = session("userdata");
	 	$pro = get_province();
         /*$pro = array();
        foreach($proc as &$v){
            $szm = $this->getFirstCharter($v['area_name']);
            $pro[$szm][] = $v;
        }
        ksort($pro);*/
	 	$brand = get_brand();
	 	$this->assign("member",$ret);
	 	$this->assign("pro",$pro);
	 	$this->assign("brand",$brand);
	 	$this->display("OrderInfo:order_one");
	 }

	 /**
      * 获取文字首字母
      */
    function getFirstCharter($str){
        if(empty($str)){return '';}
        $fchar=ord($str{0});
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
        $s1=iconv('UTF-8','gb2312',$str);
        $s2=iconv('gb2312','UTF-8',$s1);
        $s=$s2==$str?$s1:$str;
        $asc=ord($s{0})*256+ord($s{1})-65536;
        if($asc>=-20319&&$asc<=-20284) return 'A';
        if($asc>=-20283&&$asc<=-19776) return 'B';
        if($asc>=-19775&&$asc<=-19219) return 'C';
        if($asc>=-19218&&$asc<=-18711) return 'D';
        if($asc>=-18710&&$asc<=-18527) return 'E';
        if($asc>=-18526&&$asc<=-18240) return 'F';
        if($asc>=-18239&&$asc<=-17923) return 'G';
        if($asc>=-17922&&$asc<=-17418) return 'H';
        if($asc>=-17417&&$asc<=-16475) return 'J';
        if($asc>=-16474&&$asc<=-16213) return 'K';
        if($asc>=-16212&&$asc<=-15641) return 'L';
        if($asc>=-15640&&$asc<=-15166) return 'M';
        if($asc>=-15165&&$asc<=-14923) return 'N';
        if($asc>=-14922&&$asc<=-14915) return 'O';
        if($asc>=-14914&&$asc<=-14631) return 'P';
        if($asc>=-14630&&$asc<=-14150) return 'Q';
        if($asc>=-14149&&$asc<=-14091) return 'R';
        if($asc>=-14090&&$asc<=-13319) return 'S';
        if($asc>=-13318&&$asc<=-12839) return 'T';
        if($asc>=-12838&&$asc<=-12557) return 'W';
        if($asc>=-12556&&$asc<=-11848) return 'X';
        if($asc>=-11847&&$asc<=-11056) return 'Y';
        if($asc>=-11055&&$asc<=-10247) return 'Z';
        return null;
    }

	 /**
	  * 下单第一步，判断&&价格
	  */
	 public function getLine(){
	     $this->ajaxReturn(D('Worktwo')->getPrice(I("str"),I("end"),I("carid")));
	 }
	 
	 /**
	  * 手机端下单进入第二页
	  */
	 function mobileOrderTwo(){
	    $flag = I("flag");
	    if($flag == 'Y'){
	        $userInfo = session("userdata");
	        $code = md5($userInfo['user_code']);
	        //$data = S($code);
	        $pinpai = I("pinpai_name");//品牌
	        $data['flag'] = $flag;
	        $data['product_type'] = I("product_type");
	        $data['order_way'] = 'S';
	        $data['order_info_brand'] = explode('-',$pinpai)[0];
	        $data['order_info_type'] = explode('-',$pinpai)[1];
	        $data['totalz'] = I("totalz");
	        $data['order_info_c_car'] = I("lineprice");
	        $data['order_info_t_car'] = I("tiz");
	        $data['order_info_s_car'] = I("songz");
	        $data['order_info_star'] = I("chufa");
	        $data['order_info_end'] = I("mudi");
	        $data['order_info_margin'] =I("maoliz");
	        $data['chufa_name'] =I("chufa_name");
	        $data['mudi_name'] =I("mudi_name");

	        $code = md5($userInfo['user_code']);
	        S($code,$data);
	        //dump($data);die();
            $brand = get_brand();
            /*foreach($brandc as &$v){
                $szm = $this->getFirstCharter($v['cxjj_brand']);
                if($v['cxjj_brand'] == '讴歌'){
                    $szm = 'O';
                }
                $brand[$szm][] = $v;
            }
            ksort($brand);*/
            $this->assign("brand",$brand);
	        $this->assign("data",$data);
	        $this->assign("name1",explode("-",$data['chufa_name'])[1]);
	        $this->assign("name2",explode("-",$data['mudi_name'])[1]);
	        $this->display("OrderInfo:order_two");
	    }else{
	        $userInfo = session("userdata");
	        $code = md5($userInfo['user_code']);
	        //$data = S($code);
	        $pinpai = I("pinpai_name");//品牌
	        $data['flag'] = $flag;
	        $data['product_type'] = I("product_type");
	        $data['order_way'] = 'S';
	        $data['order_info_brand'] = explode('-',$pinpai)[0];
	        $data['order_info_type'] = explode('-',$pinpai)[1];
	        $data['totalz'] = I("totalz");
	        $data['order_info_c_car'] = I("lineprice");
	        $data['order_info_t_car'] = I("tiz");
	        $data['order_info_s_car'] = I("songz");
	        $data['order_info_star'] = I("chufa");
	        $data['order_info_end'] = I("mudi");
	        $data['order_info_margin'] =I("maoliz");
	        $data['chufa_name'] =I("chufa_name");
	        $data['mudi_name'] =I("mudi_name");
	        $code = md5($userInfo['user_code']);
	        S($code,$data);
	        //dump($data);die();
	        $this->assign("data",$data);
	        $this->assign("name1",explode("-",$data['chufa_name'])[1]);
	        $this->assign("name2",explode("-",$data['mudi_name'])[1]);
	        //$data['totalz'] = $data['totalz'] + $data['order_info_bxd'];
	        $gbObj = D("GroupBuy");
	        $linkList = $gbObj->getGroupAddCommon($userInfo['user_code']);//当前用户的常用联系人
	        //dump($data);die();
	        S($code,$data);
	        $this->assign("data",$data);
	        $this->assign("linkListsize",count($linkList));
	        $this->assign("linkList",$linkList);//常用联系人
	        $this->display("OrderInfo:order_three");
	    }
	 	
	 }
	 /**
	  * 获取保费
	  */
	 public function getAcale(){
	     $this->ajaxReturn(D('Worktwo')->getSecu(I("car_baojia")*10000,"acale_clent"));
	 }
	 /**
	  * 车妥妥第三部
	  */
	 function groupThree(){
	 	$userInfo = session("userdata");
	 	$code = md5($userInfo['user_code']);
	 	$data = S($code);
	 	$data['flag'] = I("flag");
	 	$data['order_info_bxd'] = I("order_info_bxd");
	 	$data['order_info_price'] = I("order_info_price");
	 	$pinpai = I("pinpai_name");//品牌
	 	$data['order_info_brand'] = explode('-',$pinpai)[0];
	 	$data['order_info_type'] = explode('-',$pinpai)[1];
	 	//$data['totalz'] = $data['totalz'] + $data['order_info_bxd'];
	 	$gbObj = D("GroupBuy");
	 	$linkList = $gbObj->getGroupAddCommon($userInfo['user_code']);//当前用户的常用联系人
	 	//dump($data);die();
	 	S($code,$data);
	 	$this->assign("data",$data);
	 	$this->assign("linkListsize",count($linkList));
	 	$this->assign("linkList",$linkList);//常用联系人
	 	$this->display("OrderInfo:order_three");
	 }
	 
	 /**
	  * 订单第4步
	  */
	 function groupFour(){
	 	$token = rand_token();
	 	$flag = I("flag");
	 	if($flag == 'Y'){
	 	    $userInfo = session("userdata");
	 	    $code = md5($userInfo['user_code']);
	 	    $data = S($code);
	 	    $car_plate_num = I("order_info_carmark");//车牌号码
	 	    $send_name = I("send_name");//发件人姓名
	 	    $send_tel = I("send_tel");//发件人电话
	 	    $send_code = I("send_code");//发件人证件号
	 	    
	 	    $gather_name = I("gather_name");//收件人姓名
	 	    $gather_tel = I("gather_tel");//收件人电话
	 	    $gather_code = I("gather_code");//收件人证件号
	 	    
	 	    $send_car_address = I("send_car_address");//发件地址
	 	    $gather_car_address = I("gather_car_address");//收件地址
	 	    
	 	    $remark = I("remark");//备注
	 	     
	 	    $data['order_info_tclxr'] = $send_name.",".$send_tel.",".$send_code;
	 	    $data['order_info_sclxr'] = $gather_name.",".$gather_tel.",".$gather_code;
	 	    $data['order_info_carmark'] = $car_plate_num;
	 	    $data['order_info_star_address'] = $send_car_address;
	 	    $data['order_info_end_address'] = $gather_car_address;
	 	    if($data['product_type'] == 'B'){
	 	        $yun = $data['order_info_c_car']+$data['order_info_t_car']+$data['order_info_s_car']+$data['order_info_margin'];
	 	    }else{
	 	        $yun = $data['order_info_c_car']+$data['order_info_t_car']+$data['order_info_margin'];
	 	    }
	 	    S($code,$data);
	 	    //dump($data);die();
	 	    $this->assign("saddress",$gather_car_address);
	 	    $this->assign("order",$data);
	 	    $this->assign("yun",$yun);
	 	    $this->assign("name1",explode("-",$data['chufa_name'])[1]);
	 	    $this->assign("name2",explode("-",$data['mudi_name'])[1]);
	 	    $this->assign("block",D("worktwo")->getBlock(explode(",",$data['order_info_end'])[1]));
	 	    $this->display("OrderInfo:order_four");
	 	}else{
	 	    $userInfo = session("userdata");
	 	    $code = md5($userInfo['user_code']);
	 	    $data = S($code);
	 	    $objs = D("Worktwo");
	 	    $time = date("Y-m-d H:i:s",time());
	 	    $data['order_code'] = $objs->getTablecode("D");
	 	    $data['user_code'] = $userInfo['tel'];
	 	    $data['order_time'] = $time;
	 	    $data['order_status'] = "S";
	 	    $data['pay_status'] = 'W';
	 	    $data['source'] = "B";
	 	    $data['order_way'] = 'S';
	 	    $data['kefu_shi'] = 'N';
	 	    
	 	    $car_plate_num = I("order_info_carmark");//车牌号码
	 	    $send_name = I("send_name");//发件人姓名
	 	    $send_tel = I("send_tel");//发件人电话
	 	    $send_code = I("send_code");//发件人证件号
	 	     
	 	    $gather_name = I("gather_name");//收件人姓名
	 	    $gather_tel = I("gather_tel");//收件人电话
	 	    $gather_code = I("gather_code");//收件人证件号
	 	     
	 	    $send_car_address = I("send_car_address");//发件地址
	 	    $gather_car_address = I("gather_car_address");//收件地址
	 	     
	 	    $remark = I("remark");//备注
	 	    
	 	    $data['order_info_tclxr'] = $send_name.",".$send_tel.",".$send_code;
	 	    $data['order_info_sclxr'] = $gather_name.",".$gather_tel.",".$gather_code;
	 	    $data['order_info_carmark'] = $car_plate_num;
	 	    $data['order_info_star_address'] = $send_car_address;
	 	    $data['order_info_end_address'] = $gather_car_address;
	 	    
	 	    $ret = $objs->setOrder($data);
    	 	if($ret){
    	         S($code,null);
    	         $opid = session("openid");
    	         $this->redirect("Worklwt/myOrder", array('opid' => $opid));
    	     }else{
    	         $this->showErrorPage("下单失败,请联系客服");
    	     }
	 	}
	 	
	 }
	 
	 /**
	  * 判断是否上门送车&&计算价格
	  */
	 public function smscFun(){
	     $masObj = D('Worktwo');
	     $id = I("id");
	     $type = I("type");
	     $sm_star = I("str");
	     $sm_end = $sm_star.",".$id;
	     $msg['prices'] = ($masObj->getsmPrice($sm_star,$sm_end)['sm_platelets_price'])/100;
	     //print_log("----------------------:".$type);
	     if($type=='Y'){
	         $msg['flag'] = true;
	     }else{
	         $msg['flag'] = false;
	     }
	     $this->ajaxReturn($msg);
	 }
	 /**
	  * 获取优惠劵金额
	  */
	 function getFavPrice(){
	     $this->ajaxReturn(D('Worktwo')->getUserConpon(I("code")));
	 }
	 /**
	  * 正常下单第四页面
	  * @date: 2016-9-7 上午8:40:37
	  * @author: liuxin
	  */
	 public function OrderTh(){
	     $userInfo = session("userdata");
	 	 $code = md5($userInfo['user_code']);
	 	 $order = S($code);
	     $aaObj = D('Worktwo');
	     
	     $order['order_info_end_address'] = I("order_info_end_address");
	     $order['order_info_smsc'] = I("order_info_smsc");
	     $order['car_washing'] = I("car_washing");
	     $order['fav_code'] = I("fav_code");
	     $order['order_info_zj'] = I("totalz");
	     $str = '';
    	 if($order['product_type'] == 'B'){
    	 	 $yun = $order['order_info_c_car']+$order['order_info_t_car']+$order['order_info_s_car']+$order['order_info_margin'];
    	 }else{
    	 	 $yun = $order['order_info_c_car']+$order['order_info_t_car']+$order['order_info_margin'];
    	 }
	     $str .="运输费:+".$yun."元";
	     $str .="保费:+".$order['order_info_bxd']."元";
	     if($order['order_info_smsc'] == "Y"){
	         $order['order_info_smscd'] = $order['order_info_end'].",".I("city_qus");
	         $order['order_smsc_car'] = I("order_smsc_car");
	         $str .="上门送车:+".$order['order_smsc_car']."元";
	         $this->assign("citys","-".$aaObj->getPlaceName(I("city_qus")));
	     }
	 
	     $fav = $aaObj->getUserConpon($order['fav_code']);
	     if($order['car_washing'] !=0){
	         $str .="洗车:+50元";
	     }
	     if($fav['flag']){
	         $prie = $order['order_info_zj'] - $fav['price'];
	         $str .="优惠劵:-".$fav['price']."元";
	     }else{
	         $prie = $order['order_info_zj'];
	     }
	     S($code,$order);
	     $this -> assign('order',$order);
	     $this -> assign("tclxr",explode(",",$order['order_info_tclxr']));
	     $this -> assign("sclxr",explode(",",$order['order_info_sclxr']));
	     $this -> assign("str",$str);
	     $this -> assign("price",$prie);
	     $this -> assign('user',$userInfo);
	     $this -> display('OrderInfo:quick_step_four');
	 }
	 /**
	  * 确认下单
	  */
	 public function orderInsert(){
	     $userInfo = session("userdata");
	     $code = md5($userInfo['user_code']);
	     $data = S($code);
	     $objs = D("Worktwo");
	     $time = date("Y-m-d H:i:s",time());
	     $data['order_code'] = $objs->getTablecode("D");
	     $data['user_code'] = $userInfo['tel'];
	     $data['order_time'] = $time;
	     $data['order_status'] = "S";
	     $data['pay_status'] = 'W';
	     $data['source'] = "B";
	     $data['order_way'] = 'S';
	     $data['kefu_shi'] = 'N';
	     $data['order_info_price'] = $data['order_info_price']*100;
	     $data['order_info_c_car'] = $data['order_info_c_car']*100;
	     $data['order_info_t_car'] = $data['order_info_t_car']*100;
	     $data['order_info_s_car'] = $data['order_info_s_car']*100;
	     $data['order_smsc_car'] = $data['order_smsc_car']*100;
	     $data['order_info_bxd'] = $data['order_info_bxd']*100;
	     $data['order_info_margin'] =$data['order_info_margin']*100;
	     $data['car_washing'] =$data['car_washing']*100;
	     $fav = $objs->getUserConpon($data['fav_code']);
	     if($fav['flag']){
	         $data['order_info_zj'] = ($data['order_info_zj']-$fav['price'])*100;
	         $data['fav_code'] = $data['fav_code'];
	     }else{
	         $data['order_info_zj'] = $data['order_info_zj']*100;
	     }
	     $ret = $objs->setOrder($data);
	     if($ret){
	         S($code,null);
	         $msg['code'] = $data['order_code'];
	         $msg['flag'] = true;
	     }else{
	         $msg['flag'] = false;
	     }
	     $this->ajaxReturn($msg);
	 }
	 /**
	  * 订单成功页面
	  */
	 public function suOrder(){
	     $code = I("code");
	     $this->assign("info",D("Worktwo")->getOrderInfo($code));
	     $this->display("OrderInfo:su_order");
	 }
	 /**
	  * 获取城市
	  * @date: 2016-9-4 下午4:01:35
	  * @author: liuxin
	  */
	 public function getCity(){
	 	$pid = I('post.pid');
	 	$obj = D('Login');
	 	$data = $obj -> getCity($pid);
	 	$this -> ajaxReturn($data);
	 }
	 /**
	  * 获取市区
	  * @date: 2016-9-4 下午4:01:35
	  * @author: liuxin
	  */
	 public function getBlock(){
	 	$pid = I('post.pid');
	 	$obj = D('Login');
	 	$data = $obj -> getBlock($pid);
	 	$this -> ajaxReturn($data);
	 }
	 
	 /**
	  * 获取市区
	  * @date: 2016-9-4 下午4:01:35
	  * @author: liuxin
	  */
	 public function getType(){
	 	$pid = I('post.pid');
	 	$obj = D('Login');
	 	$data = $obj -> getType($pid);
	 	$this -> ajaxReturn($data);
	 }
	 /**
	  * 保险费用计算
	  */
	 function insurance(){
	 	$val = I("val")==''?0:I("val");//单位万元
	 	$num = I("num")==''?1:I("num");//车数量
	 	$wlObj = D("WorklwtSD");
	 	$bl = $wlObj->getBxBl();//例如30%，使用时除以100
	 	$mval = $val*$bl*$num*100;//单位元
	 	$this->ajaxReturn($mval);
	 }
	 /**
	  * 团购第二步费用相关计算
	  */
	 function costCalculate(){
	 	$startAddress = I("startAddress");//出发地
	 	$endAddress = I("endAddress");//目的地
	 }
	 /**
	  * 获取上门送车费
	  * S司机
	  * X小板
	  */
	 function getsmPrice(){
	 	$type = I("extractCarWay")=="Y"?"X":'S';//送车方式
	 	$endpca = I("endpca");//送车省市
	 	$smendpca = I("smendpca");//送车省市区
	 
	 	$wlObj = D("WorklwtSD");
	 	$data = $wlObj->getsmPrice($endpca,$smendpca,$type);
	 	$price=0;
	 	if($data!=null&&$data!=""){
	 		if($type=='S'){
	 			$price = $data['sm_platelets_price'];
	 		}else{
	 			$price = $data['sm_driver_price'];
	 		}
	 	}
	 	$this->ajaxReturn($price);
	 }
	 
	 /**
	  * 获取城市
	  */
	 function getGroupCity(){
	 	$pid = I("id");
	 	$gbObj = D("Index");
	 	$list = $gbObj->getCity($pid);
	 	$this->ajaxReturn($list);
	 }
	 /**
	  * 获取区域
	  */
	 function getGroupArea(){
	 	$pid = I("id");
	 	$gbObj = D("Index");
	 	$list = $gbObj->getBlock($pid);
	 	$this->ajaxReturn($list);
	 }
	 /**
	  * 品牌-车型
	  */
	 function getCarType(){
	 	$pid = I("id");
	 	$gbObj = D("Index");
	 	$list = $gbObj->getType($pid);
	 	$this->ajaxReturn($list);
	 }
	 
	 
	 /**
	  * 文件下载（一）
	  * @date: 2016-10-9 下午2:43:07
	  * @author: liuxin
	  */
	 public function downloads(){
	     $name = I('get.name');
	     $file_url = dirname($_SERVER['DOCUMENT_ROOT']).'/TTYC'.str_replace('_', '/', $name);
	     $name = '';
	     $this -> download($file_url,$name);
	 }
	 /**
	  * 文件下载（二）
	  * @date: 2016-10-9 下午2:43:07
	  * @author: liuxin
	  */
	 public function download($file_url,$new_name=''){
	     if(!isset($file_url)||trim($file_url)==''){
	         return '500';
	     }
	     if(!file_exists($file_url)){ //检查文件是否存在
	         return '404';
	     }
	     $file_name=basename($file_url);
	     $file_type=explode('.',$file_url);
	     $file_type=$file_type[count($file_type)-1];
	     $file_name=trim($new_name=='')?$file_name:urlencode($new_name);
	     $file_type=fopen($file_url,'r'); //打开文件
	     //输入文件标签
	     header("Content-type: application/octet-stream");
	     header("Accept-Ranges: bytes");
	     header("Accept-Length: ".filesize($file_url));
	     header("Content-Disposition: attachment; filename=".$file_name);
	     //输出文件内容
	     echo fread($file_type,filesize($file_url));
	     fclose($file_type);
	 }
	 public function paynow(){
	     $opid = session("openid");
	     $payway = I('post.payway');
	     $fee = I('post.fee');
	     $code = I('post.code');
	     $datas['pay_way'] = $payway;
	     $maps['order_code'] = array('eq',$code);
	     M("order") ->where($maps)->save($datas);
	     M("order_assistant") ->where($maps)->save($datas);
	     $this->redirect("Worklwt/myOrder", array('opid' => $opid));
	 }

	 public function groupOrder(){
        $code = I("post.code");
         $maps['order_code'] = array('eq',$code);
         $order = M("order") ->where($maps)->find();
         if(!empty($order)){
             $this->ajaxReturn(array('code'=>$code,'flag'=>true,'total'=>$order['order_info_zj']/100));
         }else{
             $this->ajaxReturn(array('flag'=>flase));
         }
     }
}