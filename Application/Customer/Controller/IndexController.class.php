<?php
namespace Customer\Controller;
use Think\Controller;
class IndexController extends Controller {

	/**
	 * 入口
	 */
	public function index(){
		//echo 12313123;die();
		print_log("-------------------进入验证");
		$echoStr = I("echostr");
		$signature = I("signature");
		$timestamp = I("timestamp");
		$nonce = I("nonce");
		print_log("  echoStr:".$echoStr."|nonce:".$nonce."|timestamp:".$timestamp."|signature:".$signature);
		if($this->checkSignature($signature,$timestamp,$nonce)){
			print_log('  打印随机数:'.$echoStr);
			echo $echoStr;
			exit;
		}else{
			print_log("-----点击事件被触发此处-----");
			$this->responseMsg();
		}
	}
	
	/**
	 * 接入验证签名
	 * @param string $signature
	 * @param string $timestamp
	 * @param string $nonce
	 * @return boolean
	 */
	private function checkSignature($signature="",$timestamp="",$nonce=""){
		$token = C('WXTOKEN');
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		//print_log("参与签名数据:".implode( $tmpArr ));
		//print_log("签名数据:".$tmpStr);
		//print_log("前面验证".$tmpStr."-------------".$signature);
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 接收相应数据
	 */
	function responseMsg(){
		//$wxObj = A("Login");
		//接收微新传过来的xml消息数据
		print_log("返回信息:".json_encode($GLOBALS));//file_get_contents("php://input");//
		$postStr =  file_get_contents("php://input");
		print_log("responseMsg11111111方法接收XML：".$postStr);
		if (!empty($postStr)){
			//将接收的消息处理返回一个对象
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			//从消息对象中获取消息的类型 text  image location voice vodeo link
			$RX_TYPE =  strtolower(trim($postObj->MsgType));
			//消息类型分离, 通过RX_TYPE类型作为判断， 每个方法都需要将对象$postObj传入
			switch ($RX_TYPE){
				case "text":
					print_log("接受类型------------------------".$RX_TYPE);
					//$result =  self::getText($postObj);
					//$result = $this->receiveText($postObj);     //接收文本消息
					break;
				case "image":
					print_log("接受类型------------------------".$RX_TYPE);
					//$result = $this->receiveImage($postObj);   //接收图片消息
					break;
				case "location":
					print_log("接受类型------------------------".$RX_TYPE);
					//$this->receiveLocation($postObj);  //接收位置消息
					break;
				case "voice":
					print_log("接受类型------------------------".$RX_TYPE);
					//$result = $this->receiveVoice($postObj);   //接收语音消息
					break;
				case "video":
					print_log("接受类型------------------------".$RX_TYPE);
					//$result = $this->receiveVideo($postObj);  //接收视频消息
					break;
				case "link":
					print_log("接受类型------------------------".$RX_TYPE);
					//$result = $this->receiveLink($postObj);  //接收链接消息
					break;
				case "event":
					print_log("接受类型------------------------".$RX_TYPE);
					print_log("EventKey------------------------".$postObj->EventKey);
					$result =  $this->getEventReturn($postObj);//关注/取消
					break;
				default:
					print_log("接受类型------------------------".$RX_TYPE);
					$result = 0;//"unknown msg type: ".$RX_TYPE;   //未知的消息类型
					break;
			}
			//将响应的消息再次写入日志， 使用T标记响应的消息！
			//print_log("  T \n".$result);
			//输出消息给微新
			echo $result;
		}else {
			//如果没有消息则输出空，并退出
			echo "";
			exit;
		}
	}
	
	/*
	 * 接收文本类型
	 */
	public function getText($postObj){
		print_log("文本的内容都到这里接收-----------------------".$postObj->Content);
		//$wxair = A('Airlines');
		//$result = $wxair->wx_sent($postObj);
		return null;
	}
	
	/**
	 * 设置菜单
	 */
	function setMenu(){
		//课程
        //print_log("菜单设置URL:".$url_k);
        //$appid = C("APPID");
        //print_log("菜单设置APPID:".$appid);
        
        //$httpurl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect';
        //$urls = sprintf($httpurl,$appid,$redirect_uri);
        //print_log("菜单地址：".$urls);
        
        //print_log("菜单地址：".$lwturl5);
        //公司介绍
        $company = C('REQUEST_PATH')."/Customer/AboutTuoTuo/companyIntroduce";
        //安全保险
        $safetySafeguard = C('REQUEST_PATH')."/Customer/AboutTuoTuo/safetySafeguard";
        //常见问题
        $commonQuestion = C('REQUEST_PATH')."/Customer/AboutTuoTuo/commonQuestion";
        //我要下单
        $overBooking = $this->getAuthUrl('/Customer/OrderBook/overBooking');//网站定制
        //专业服务
        $services = $this->getAuthUrl('/Customer/AboutTuoTuo/services');
        //道路救援
        $rescue = $this->getAuthUrl('/Customer/AboutTuoTuo/rescue');
        //联系客服
        $customer = $this->getAuthUrl('/Customer/AboutTuoTuo/customer');
        
        //个人中心
        $lwturl = $this->getAuthUrl("/Customer/Worklwt/memberInfo");
        //订单列表
        $lwturl1 = $this->getAuthUrl("/Customer/Worklwt/myOrder");
        //委托合同
        $lwturl2 = $this->getAuthUrl("/Customer/Worklwt/entrustList");
        //承运保单
        $lwturl3 = $this->getAuthUrl("/Customer/Worklwt/policyList");
        //位置查询
        $lwturl4 = $this->getAuthUrl("/Customer/Worklwt/positionList");
        $jsonmenu = '
        {
		    "button": [
		        {
		            "name": "关于妥妥", 
		            "sub_button": [
		                {
		                    "type": "view", 
		                    "name": "公司介绍", 
		                    "url":"'.$company.'"
		                }, 
		                {
		                    "type": "view", 
		                    "name": "专业服务", 
		                    "url":"'.$services.'"
		                }, 
		                {
		                    "type": "view", 
		                    "name": "全程保险", 
		                    "url":"'.$safetySafeguard.'"
		                }, 
		                {
		                    "type": "view", 
		                    "name": "常见问题", 
		                    "url":"'.$commonQuestion.'"
		                }
		            ]
		        }, 
		        {
		            "name": "我要托运", 
		            "sub_button": [
		                {
		                    "type": "view", 
		                    "name": "价格查询", 
		                    "url":"'.$overBooking.'"
		                 }, 
		                {
		                    "type": "view", 
		                    "name": "道路救援", 
		                    "url":"'.$rescue.'"
		                },
		                {
		                    "type": "click", 
		                    "name": "联系客服", 
		                    "key":"KEFU"
		                }
		            ]
		        }, 
		        {
		            "name": "托运查询", 
		            "sub_button": [
		                {
		                    "type": "view", 
		                    "name": "订单列表", 
		                    "url":"'.$lwturl1.'"
		                 },
		                {
		                    "type": "view", 
		                    "name": "委托合同", 
		                    "url":"'.$lwturl2.'"
		                 },
		                 {
		                    "type": "view", 
		                    "name": "承运保单", 
		                    "url":"'.$lwturl3.'"
		                 },
		                 {
		                    "type": "view", 
		                    "name": "位置查询", 
		                    "url":"'.$lwturl4.'"
		                 }, 
		                {
		                    "type": "view", 
		                    "name": "个人中心", 
		                    "url":"'.$lwturl.'"
		                }
		            ]
		        }
		    ]
		}';
		print_log("请求地址：".$jsonmenu);
		$access_token = get_token(C('APPID'),C('SECRET'));
		$menuurl = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
		$result = https_request($menuurl, $jsonmenu);
		$retArr = json_decode($result,true);
		//print_log("");
		if($retArr['errcode']==0){
			echo "菜单生成成功";
		}else{
			echo "菜单生成失败:".$retArr['errmsg'];
		}
	}
	
	/**
	 * 获取要授权页面的url
	 * $str格式'/模块名/控制器名/方法名';
	 * @param unknown $str
	 * @return string
	 */
	function getAuthUrl($str,$state = "STATE"){
		print_log("----------------------------------------------".$state);
		$domain = C('REQUEST_PATH');
		$url = $domain.$str;
		$encodeUrl = urlencode($url);
		$appid = C("APPID");
		$httpurl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state='.$state.'#wechat_redirect';
		$outputUrl = sprintf($httpurl,$appid,$encodeUrl);
		print_log("----------------------------------------------url:".$appid);
		print_log("----------------------------------------------".$domain);
		return $outputUrl;
	}
	/**
	 * 菜单事件回复
	 * @param string $eventType
	 * @param string $mark
	 */
	function getEventReturn($object=null){
		/**
		 * 微信点击客服事件,关注/取消,触发
		 */
			$subscribe = $object->Event;
			$CLICK = $object->EventKey;
			print_log("---------客服-----------".$subscribe);
			$result="";
			if($subscribe=="subscribe"){//关注后发出信息
				//获取用户OpeniD
				$content = C("CONTENT");
				$xmlTpl = <<<xml
	             <xml>
	             <ToUserName><![CDATA[%s]]></ToUserName>
	             <FromUserName><![CDATA[%s]]></FromUserName>
	             <CreateTime>%s</CreateTime>
	             <MsgType><![CDATA[text]]></MsgType>
	             <Content><![CDATA[%s]]></Content>
	             </xml>
xml;
				$result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
			}else if($CLICK=="KEFU"){
				//获取用户OpeniD
				$content = C("KE_FU");
				$xmlTpl = <<<xml
	             <xml>
	             <ToUserName><![CDATA[%s]]></ToUserName>
	             <FromUserName><![CDATA[%s]]></FromUserName>
	             <CreateTime>%s</CreateTime>
	             <MsgType><![CDATA[text]]></MsgType>
	             <Content><![CDATA[%s]]></Content>
				 <MsgId>1234567890123456</MsgId>
	             </xml>
	             
xml;
				$result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
			}
			return $result;
	}

	/**
	 * 获取当前用户的基本信息
	 */
	function getuerInfo($userOpenId=""){
	    $access_token=$this->getAccsenToken();
	    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$userOpenId."&lang=zh_CN";
	    $result = https_request($url);
	    // $result = '{"subscribe":1,"openid":"oizv4snYKf42nqAdRrGxdfsLA4AI","nickname":"月下蓝貂","sex":1,"language":"zh_CN","city":"丰台","province":"北京","country":"中国","headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/wXJ5kSJT6ONAprP5e8Ia2kb33LQR2Picy45nAkmcTvLVCS0k7Hib9aUH6aURflSnbX7kGqguFs6NUL6rtunoDsPdn07rKRNsWia\/0","subscribe_time":1434366011,"remark":"","groupid":0}';
	    print_log("获取当前用户的基本信息:".$result);
	    $ret = json_decode($result,true);
	    session("userInfo",$ret);
	    session("tokenInfo",$ret);
	    return $ret;
	}
	/**
	 * 获取页面授权用户信息
	 * @param $code 回调code 微信返回code
	 */
	function memberAuthorization($code=''){
	    //$code = I("code");
        print_log("获取的回调code:".$code);
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code";
        $returl = sprintf($url,C("APPID"),C('SECRET'),$code);
        $result = https_request($returl);
        $tokenInfo = json_decode($result,true);
        print_log("------------------------------returl获取信息:".$result);
        if($tokenInfo['openid'] !=""){
            session("tokenInfo",$tokenInfo);
            $openid = $tokenInfo['openid'];
        }else{
            $tokenInfo = session("tokenInfo");
            $oauth2 = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=%s&grant_type=refresh_token&refresh_token=%s";
            $info = https_request(sprintf($oauth2,C("APPID"),$tokenInfo['refresh_token']));
            $tokenInfo = json_decode($info,true);
            $openid = $tokenInfo['openid'];
        }
        print_log("获取当前用户openid：".$openid);
        $urls = "https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN";
        $returls = sprintf($urls,$tokenInfo['access_token'],$openid);
        $results = https_request($returls);
        print_log("拉取当前用户信息：".$results);
        $tokenInfos = json_decode($results,true);
        //$userInfo = $this->getuerInfo($openid);//获取用户信息
        return $tokenInfos;
	}
	/**
	 * 获取微信accsen_token
	 */
	function getAccsenToken(){
	    //获取微信公众号信息
	    $APPID=C("APPID");
	    $APPSECRET=C("SECRET");
	    $times = S("accesstoken")['time'];
	    $time = time();
	    if($time - $times > 7000){
	        $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
	        $json = https_request($TOKEN_URL);
	        $result=json_decode($json,true);
	        $data['access_token'] = $result['access_token'];
	        $data['time'] = $time;
	        S("accesstoken",$data);
	        print_log("获取的token:".$result['access_token']);
	        print_log("获取是否成功:".$json);
	        $access_token = $result['access_token'];
	    }else{
	        $access_token = S("accesstoken")['access_token'];
	    }
	    return $access_token;
	}
}