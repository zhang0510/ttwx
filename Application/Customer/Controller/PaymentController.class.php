<?php
namespace Customer\Controller;
/**
 * 微信客户端支付控制器
 * @author Administrator
 *
 */
class PaymentController extends BaseController {
    
    public function __construct(){
        $obj = A('Index');
        $obj -> getAccsenToken();
        $this -> wx_jsapiticket();
         
    }

    /**
     * curl Post请求
     * @param unknown $url
     * @param unknown $post
     * @return mixed
     */
    function curlPost($url,$post){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
    
        $returnInfo = curl_exec($ch);
        curl_close ($ch);
        return $returnInfo;
    }
    
    /**
     * get方法向微信服务器发送请求
     */
    public function curlGet($url,$data=''){
        if($data){
            $strData = http_build_query($data);
            $url = $url.'?'.$strData;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $returnInfo = curl_exec($ch);
        curl_close ($ch);
        //dump(curl_error($ch));
        //return json_encode($data,true);
        return $returnInfo;
    }
    
    /**
     * 获取jsapi的ticket
     */
    function wx_jsapiticket(){
        $ticket = S("jsapiticket");
        if($ticket == "" || $ticket == null){
            $acctoken = S("accesstoken")['access_token'];
            $url= "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi";
            $url = sprintf($url,$acctoken);
            $ret = https_request($url);
            $ret = json_decode($ret, true);
            S("jsapiticket",$ret['ticket'],'7000');
            $ticket = $ret['ticket'];
        }
        return $ticket;
    }
    /**
     * 获取jsapi的签名并返回相关信息
     */
    
    function getJsSign(){
        $url = I('post.url');
        //$url = 'http://www.chetuotuo.cn/Customer/Worklwt/memberOrder/';
        /*
        $ticket = S('ticket');
        $access_token = parent::getAccsenToken();
        $now = time();
        if(!isset($ticket['ticket'])||$now-$ticket['time']>=7200){
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
            $ch = curl_init();
            curl_setopt ($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $returnInfo = curl_exec($ch);
            curl_close ($ch);
            $res = json_decode($returnInfo,true);
            if($res['errmsg']=='ok'){
                $tic['ticket'] = $res['ticket'];
                $tic['time'] = time();
                S('ticket',$tic);
                $jsTic = $res['ticket'];
            }
        }else{
            $jsTic = $ticket['ticket'];
        } */
        $jsTic = S("jsapiticket");
        //print_log('js票据-----------------------------------'.$jsTic);
        $strs = 'Wm3WZYTPz0wzccnW';//获取随机字符串
        $timestamp = time();
        //$url = '';
        $str = 'jsapi_ticket='.$jsTic.'&noncestr='.$strs.'&timestamp='.$timestamp.'&url='.$url;
        $sign = sha1($str);
        $appid = C('APPID');
        $arr['appId'] = $appid;
        $arr['timestamp'] = $timestamp;
        $arr['nonceStr'] = 'Wm3WZYTPz0wzccnW';
        $arr['signature'] = $sign;
        $arr['ticket'] = $jsTic;
        $this -> ajaxReturn($arr);
    }
    
    /**
     * 传入参数 支付操作(用于jsapi 在支付页面进行ajax调用)
     */
    public function startPay(){
        $opid = session('openid');
        $code = I('post.code');
        $total = I('post.total');
		$total = $total*100;
        //print_log('EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE'.I('post.paySign'));
        if(I('post.paySign')=='Y'){
            $array = array('startAddress'=>I('post.start'),'endAddress'=>I('post.end'));
            S($code,$array);
            //print_log('第一次code'.$code);
        }
       // $opid = $_POST['opid'];
        $code = $code.'_'.randpw(4);//加随机数防止出现商户号重复的情况
		print_log("总-------------金-------------------额:".$total);
        $unifiedRes = $this -> unifiedPayment($code,$total);
        //print_log('!@!@!@!@!@!@!@!@!@!@!@@!@!@!@!@!@!@!@!@!@!!@!@!@!@!'.json_encode($unifiedRes));
        if($unifiedRes['return_code']=='SUCCESS'&&$unifiedRes['result_code']=='SUCCESS'){
            $appid = C("APPID");
            $str = '5K8264ILTKCH16CQ2502SI8ZNMTM67VS';//$this->getStr();
            $time = time();
            $packages = 'prepay_id='.$unifiedRes['prepay_id'];
            $arr = array('appId'=>$appid,
                'nonceStr'=>$str,
                'package'=>$packages,
                'signType'=>'MD5',
                'timeStamp'=>$time,
            );
            $paySign = $this -> getWxSign($arr);
            $array = array('appid'=>$appid,
                'timeStamp'=>$time.'',
                'nonceStr'=>$str,
                'packages'=>$packages,
                'signType'=>'MD5',
                'paySign'=>$paySign,
            );
            //print_log('12333221'.json_encode($array));
        }else{
            $array =1;
        }
        //print_log('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
        $this -> ajaxReturn($array);
    
    }
    
    /**
     * 统一支付
     */
    function unifiedPayment($code='',$total=''){
        $opid = session("tokenInfo")['openid'];
        $opid = $opid == ''?session('openid'):$opid;
        $appid = C('APPID');
        $mch_id = C('SH_HAO');
        //$info = S('product_id');
        $body = '车妥妥-微信支付';
        $nonce_str = '5K8264ILTKCH16CQ2502SI8ZNMTM67VS';//$this -> getStr();
        $out_trade_no =$code; //$this -> getTradeNum(); 订单code
        $ip = $_SERVER["REMOTE_ADDR"];//获取终端ip
        $notify_url = C("REQUEST_PATH").'/Customer/Payment/notifyPay';//该方法要在支付授权目录下 下面有注释
        //$str = '5K8264ILTKCH16CQ2502SI8ZNMTM67VS';
        //$times = $time;
		//print_log("总价：".$total);
        $fee = $total;//总价
        $type = "JSAPI";
        $arr = array('appid'=>$appid,
            'mch_id'=>$mch_id,
            'body'=>$body,
            'nonce_str'=>$nonce_str,
            'notify_url'=>$notify_url,
            'openid'=>$opid,
            'out_trade_no'=>$out_trade_no,
            'spbill_create_ip'=>$ip,
            'total_fee'=>$fee,
            'trade_type'=>$type,
        );
        $sign = $this -> getWxSign($arr);
		/*
        print_log('appid'.$appid);
        print_log('opid'.$opid);
        print_log('mch_id'.$mch_id);
        print_log('body'.$body);
        print_log('nonce_str'.$nonce_str);
        print_log('out_trade_no'.$out_trade_no);
        print_log('ip'.$ip);
        print_log('notify_url'.$notify_url);
        print_log('fee'.$total);
        print_log('sign'.$sign);
		*/
       // print_log('appid'.$appid);
        
        
        $xml = '<xml>
            <appid>%s</appid>
            <mch_id>%s</mch_id>
            <body>%s</body>
            <nonce_str>%s</nonce_str>
            <sign>%s</sign>
            <out_trade_no>%s</out_trade_no>
            <total_fee>%s</total_fee>
            <spbill_create_ip>%s</spbill_create_ip>
            <notify_url>%s</notify_url>
            <trade_type>%s</trade_type>
            <openid>%s</openid>
            </xml>';
        
        $xml = sprintf($xml,$appid,$mch_id,$body,$nonce_str,$sign,$code,$fee,$ip,$notify_url,$type,$opid);
        //print_log($xml);
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $returnInfo = $this -> curlPost($url, $xml);
        //print_log('!@!@!@!@!@!@!@!@!@!@!@@!@!@!@!@!@!@!@!@!@!!@!@!@!@!'.$returnInfo);
        $res = simplexml_load_string($returnInfo, 'SimpleXMLElement', LIBXML_NOCDATA);
        $res = json_encode($res);
        $res = json_decode($res,true);
        return $res;
    
    }
    
    /**
     * 支付回调
     */
    public function notifyPay(){
        //print_log("-----------------------------进入支付回调");
        //获取通知的数据
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		//print_log("回调支付:".$xml);
        $postObj = json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        //print_log("----------------------------支付回调参数:".$postObj);
        echo json_decode($postObj,'true')['result_code'];
        if(json_decode($postObj,'true')['result_code']=='SUCCESS'){
            /* $userInfo = json_decode(des_decrypt_php(session('userData')),true);
            $usercode = $userInfo['usercode']; */
            $opid = session("tokenInfo")['openid'];
            $out_trade_no = json_decode($postObj,'true')['out_trade_no']; //订单code
            $out_trade_no = explode('_',$out_trade_no)[0];
            $transaction_id = json_decode($postObj,'true')['transaction_id'];//微信支付订单号
            $obj = M('order');
            $iObj = M('order_info');
            $map['order_code'] = array('eq',$out_trade_no);
            $info = $obj->where($map)->find();
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //如果有做过处理，不执行商户的业务程序
            if($info['pay_status'] == "W"){
                if($info['guanliancode'] == ""){
                    $maps['order_code'] = array('eq',$out_trade_no);
                    //$infos = $iObj->where($maps) ->find();
        
                    $reb = $iObj -> where($maps) -> setField('pay_time',date('Y-m-d H:i:s',time()));
                    $ret = $obj->where($map)->setField('pay_status','Y');
                    if($reb && $ret){
                        //$this->success('充值成功!',U("MyOrder/index"));
                        $sign = 'S';
                        $info = '如有问题请致电：'.C('PHONE');
                        $title = '付款成功，我们会尽快派人前去提车 >>点击查看订单详情';
                    }else{
                        //$this->error('充值异常(02)，请联系客服或管理员。（需提供当前订单号【'.$out_trade_no.'】和支付宝交易号【'.$trade_no.'】）');
                        $sign = 'E1';
                        $info = '出现异常（01） 请联系客服：'.C('PHONE');
                        $title = '付款成功，数据出现异常 >>点击查看订单详情';
                    }
                }else{
                    $result = $this -> batchUpdate($info['guanliancode']);
                    if($result=='S'){
                        //$this->success('充值成功!',U("MyOrder/index"));
                        $sign = 'S';
                        $info = '如有问题请致电：'.C('PHONE');
                        $title = '付款成功，我们会尽快派人前去提车 >>点击查看订单详情';
                    }else if($result=='E01'){
                        //此异常为主表同批生成订单的订单付费状态不统一
                        //$this->error('充值异常(03)，请联系客服或管理员。（需提供当前订单号【'.$out_trade_no.'】和支付宝交易号【'.$trade_no.'】）');
                        $sign = 'E3';
                        $info = '出现异常（03） 请联系客服：'.C('PHONE');
                        $title = '付款成功，数据出现异常 >>点击查看订单详情';
                    }else if($result=='E02'){
                        //写入问题
                        //$this->error('充值异常(01)，请联系客服或管理员。（需提供当前订单号【'.$out_trade_no.'】和支付宝交易号【'.$trade_no.'】）');
                        $sign = 'E1';
                        $info = '出现异常（01） 请联系客服：'.C('PHONE');
                        $title = '付款成功，数据出现异常 >>点击查看订单详情';
                    }
                }
            }else{
                $sign = 'E2';
                $info = '出现异常（02） 请联系客服：'.C('PHONE');
                $title = '付款成功，数据出现异常 >>点击查看订单详情';
                //$this->error('充值异常(02)，请联系客服或管理员。（需提供当前订单号【'.$out_trade_no.'】和支付宝交易号【'.$trade_no.'】）');
            }
            //支付成功发送通知
            
            $opid = json_decode($postObj,'true')['openid'];
            $access_token = S("accesstoken")['access_token'];
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
            $data['template_id'] = "B7zNZzPk2GRagQwyHBeWcwEPEW0Lr6zSSIARXDX1dH4";//消息模板id
            $data['touser'] = $opid;
            //print_log('opid~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~'.$opid);
            //print_log('第二个code'.$out_trade_no);
            
            $uobj = M('user');
            $mapuser['opid'] = array('eq',$opid);
            $userInfo = $uobj -> where($mapuser) -> find();
            if(S($out_trade_no)==''||S($out_trade_no)==null){
                $code = md5($userInfo['user_code']);
                $datas = S($code);
                $startAddress = $datas['startAddress'];//出发地
                $endAddress = $datas['endAddress'];//目的地
            }else{
                //print_log('%^&^&*^(*^(*&^&^%^&*&^&%*&((*&*&&&^&(*&((*&(*&啊啊啊啊啊啊啊啊');
                $datas = S($out_trade_no);
                $startAddress = $datas['startAddress'];//出发地
                $endAddress = $datas['endAddress'];//目的地
            }
            $gbObj = D("GroupBuy");
            $aimPCAList = $gbObj -> getAimCityArea($endAddress);//获取目的地上门送车区域，区域List
            $startPCAInfo = $gbObj->getStartCityArea($startAddress);//获取出发地省市区信息
            $infofee = (string)json_decode($postObj,'true')['total_fee']/100;
            $data['url'] = C('DOMAIN').'/Customer/Worklwt/orderInfo/code/'.$out_trade_no;
            $data['data'] = array(
                'first'=>array("value"=>$title,"color"=>"#173177"),
                'keyword1'=>array("value"=>$userInfo['user_name'],"color"=>"#173177"),
                'keyword2'=>array("value"=>$out_trade_no,"color"=>"#173177"),
                'keyword3'=>array("value"=>'￥'.$infofee.'元',"color"=>"#173177"),
                'keyword4'=>array("value"=>$startPCAInfo['province']['area_name'].' 至 '.$aimPCAList['voProvince']['area_name'],"color"=>"#173177"),
                'remark'=>array("value"=>$info,"color"=>"#173177")
                	
            );
            $resi = $this -> curlPost($url,json_encode($data));
            //print_log($resi);
            S($code,null);
            /* $array = array('sign'=>$sign,'out_trade_no'=>$out_trade_no,'transaction_id'=>$transaction_id);
            S($opid.'wxpay',$array); */
        }
    }
    
    /**
     * 生成微信签名
     * @param 签名中需要的字段生成的关联数组 $array
     * @return string
     */
    public function getWxSign($array){
        ksort($array);
        $keys = C('SECRETS');
        $strData = '';
        foreach($array as $key=>$value){
            $strData.=$key.'='.$value.'&';
        }
        $str = $strData.'key='.$keys;
        $str = md5($str);
        $sign = strtoupper($str);
        return $sign;
    }
    /**
     * 批量更新订单数据
     * @date: 2016-9-28 上午11:39:06
     * @author: liuxin
     */
    public function batchUpdate($update=''){
        $obj = M('order');
        $iobj = M('order_info');
        $map['guanliancode'] = array('eq',$update);
        $data = $obj -> where($map) -> select();
    
        $sign1 = 1;
        //判断投标数据是否全为未支付状态
        for($i=0;$i<count($data);$i++){
            if($data[$i]['pay_status']=='W'){
                $signs = true;
                $sign1 = $sign1&&$signs;
            }else{
                $signs = false;
                $sign1 = $sign1&&$signs;
            }
        }
    
        if(!$sign1){
            $return = 'E01';//第一种数据错误 头表数据出现问题
            return $return;
        }
        $sign2 = 1;
        for($i=0;$i<count($data);$i++){
            $maps['order_code'] = array('eq',$data[$i]['order_code']);
            $res = $obj -> where($maps) -> setField('pay_status','Y');
            $res1 = $iobj -> where($maps) -> setField('pay_time',date('Y-m-d H:i:s',time()));
            $signs = $sign2&&$res&&res1;
        }
        if($sign2){
            return 'S';
        }else{
            return 'E02';
        }
    
    }

}