<?php


/**
 * 抛出异常处理
 * @param string $msg 异常消息
 * @param integer $code 异常代码 默认为0
 * @throws Think\Exception
 * @return void
 */
function ES($msg, $code=0) {
	//throw new Think\Exception($msg, $code);
	 
	$code = $code==0?"":"异常代码:".$code;
	echo <<<EOT
        <!DOCTYPE HTML>
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>错误信息提示</title>
        <style type="text/css">
        *{margin:0;padding:0;}
        ul,li{list-style:none;}
        a{text-decoration:none;}
        body{background:url(/Public/Image/background_gray.png) repeat;}
        .errowBox{width:830px;height:490px;position:absolute;left:50%;margin-left:-415px;top:50%;margin-top:-245px;background:#fff;border:solid 1px #ddd;border-radius:10px;z-index:1;}
        .rightPic{position:absolute;left:-6px;top:-6px;z-index:2px;}
        .errowImg{float:left;padding:120px 65px;}
        .errowCon{padding:50px 0;font-size:14px;color:#999;}
        .errowCon span{display:block;font-size:25px;color:#333;margin-bottom:10px;}
        .errowCon a:hover{text-decoration:underline;}

        </style>

        </head>

        <body>
        	<div class="errowBox">
            	<img src="/Public/Image/404_label.png" class="rightPic">
                <img src="/Public/Image/smiley.png" class="errowImg">
                <div class="errowCon">
                <span style="font-weight: bold;color:#999;letter-spacing: 5px;font-family:Arial;font-size: 40px;">OOOPS...</span>
               		<span style="padding-right: 50px;font-size: 20px;line-height: 1.5;letter-spacing: 2px">
                		{$code}<br>{$msg}
                	</span>
                </div>
            </div>
        </body>
        </html>

EOT;
exit();

}

/**************************************************************
 *  生成指定长度的随机码。
 *  @param int $length 随机码的长度。
 *  @access public
 *  @return 随机码
 **************************************************************/
function create_random_code($length) {
	$randomCode = "";
	$randomChars = '2345678923456789464523456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // 
	for($i = 0; $i < $length; $i ++) {
		$randomCode .= substr($randomChars, mt_rand(0, strlen($randomChars) - 1), 1);
	}
	Think\Log::record ( date ( 'Y-m-d H:i:s', time () ) . "  获取随机" . $length . "位数：" . $randomCode, "INFO" );
	return $randomCode;
}

/**
 * 获取验证码自定义配置
 * 
 * @param number $length
 *        	验证码长度
 * @param number $num
 *        	生成多少组验证码
 * @return number
 */
function random_code($length = 4, $num = 1, $config) {
	$verify = new \Think\Verify ( $config );
	return $verify->entry ();
}

/**
 * 检查验证码是否正确
 * 
 * @param unknown $code        	
 * @param string $id        	
 * @return boolean true/false
 */
function check_random_code($code, $id = '') {
	$verify = new \Think\Verify ();
	return $verify->check ( $code, $id );
}
/**
 * 获取验证码
 * 
 */
function verify_code_img() {
    $Verify = new \Think\Verify ();
    $Verify->fontSize = 48;
    $Verify->length = 4;
    $Verify->useNoise = false;
    $Verify->entry ();
}

/**
 * 创建表编号
 * 
 * @param unknown $table
 *        	编号起始字符
 * @return code
 */
function get_code($table) {
	// Think\Log::write('这里记录一下',WEB_LOG_DEBUG);
	$tb = strtoupper ( $table );
	$timestamp = time ();
	$randomCode = strtoupper ( create_random_code ( 4 ) );
	return $tb . $timestamp . $randomCode;
}

/**
 * 日志打印
 * @$msg 写入内容
 */
function print_log($msg = "", $type = "INFO") {
	Think\Log::record ( date ( 'Y-m-d H:i:s', time () ) . $msg, "INFO" );
}
/**
 * 去掉字符串中全部空格
 * @param unknown $str  
 * @return string      	
 */
function trimall($str) {
	$qian = array (
			" ",
			"　",
			"\t",
			"\n",
			"\r" 
	);
	$hou = array (
			"",
			"",
			"",
			"",
			"" 
	);
	return str_replace ( $qian, $hou, $str );
}

/**
 * 分页--启用
 * 
 * @param unknown $count        	
 * @param unknown $pageSize        	
 * @param unknown $par        	
 * @return unknown $Page->Fpage();
 */
function set_page($count, $pageSize, $par) {
	$Page = Pages\Page::getInstance ($count, $pageSize, parameter_dispose ( $par ) );
	return $Page;
}
/**
 * --启用
 * 分页参数处理
 * @param string $par
 */
function parameter_dispose($par){
	if($par!=""){
		 
		$size = count($par);
		$str = "";
		if($size>0){
    		foreach ($par as $key => $val){
    			$str .= $key."=".$val.'&';
    		}
		}
		$str = substr($str,0,strlen($str)-1);
		return $str;
	}
	return "";
}
/*
 * 防XSS攻击
 * @$val string
 * return string
 */
function remove_xss($val) {
	// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	// this prevents some character re-spacing such as <java\0script>
	// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	$val = preg_replace ( '/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val );
	
	// straight replacements, the user should never need these since they're normal characters
	// this prevents like <IMG SRC=@avascript:alert('XSS')>
	$search = 'abcdefghijklmnopqrstuvwxyz';
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$search .= '1234567890!@#$%^&*()';
	$search .= '~`";:?+/={}[]-_|\'\\';
	for($i = 0; $i < strlen ( $search ); $i ++) {
		// ;? matches the ;, which is optional
		// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
		
		// @ @ search for the hex values
		$val = preg_replace ( '/(&#[xX]0{0,8}' . dechex ( ord ( $search [$i] ) ) . ';?)/i', $search [$i], $val ); // with a ;
		                                                                                           // @ @ 0{0,7} matches '0' zero to seven times
		$val = preg_replace ( '/(�{0,8}' . ord ( $search [$i] ) . ';?)/', $search [$i], $val ); // with a ;
	}
	
	// now the only remaining whitespace attacks are \t, \n, and \r
	$ra1 = array (
			'javascript',
			'vbscript',
			'expression',
			'applet',
			'meta',
			'xml',
			'blink',
			'link',
			'style',
			'script',
			'embed',
			'object',
			'iframe',
			'frame',
			'frameset',
			'ilayer',
			'layer',
			'bgsound',
			'title',
			'base' 
	);
	$ra2 = array (
			'onabort',
			'onactivate',
			'onafterprint',
			'onafterupdate',
			'onbeforeactivate',
			'onbeforecopy',
			'onbeforecut',
			'onbeforedeactivate',
			'onbeforeeditfocus',
			'onbeforepaste',
			'onbeforeprint',
			'onbeforeunload',
			'onbeforeupdate',
			'onblur',
			'onbounce',
			'oncellchange',
			'onchange',
			'onclick',
			'oncontextmenu',
			'oncontrolselect',
			'oncopy',
			'oncut',
			'ondataavailable',
			'ondatasetchanged',
			'ondatasetcomplete',
			'ondblclick',
			'ondeactivate',
			'ondrag',
			'ondragend',
			'ondragenter',
			'ondragleave',
			'ondragover',
			'ondragstart',
			'ondrop',
			'onerror',
			'onerrorupdate',
			'onfilterchange',
			'onfinish',
			'onfocus',
			'onfocusin',
			'onfocusout',
			'onhelp',
			'onkeydown',
			'onkeypress',
			'onkeyup',
			'onlayoutcomplete',
			'onload',
			'onlosecapture',
			'onmousedown',
			'onmouseenter',
			'onmouseleave',
			'onmousemove',
			'onmouseout',
			'onmouseover',
			'onmouseup',
			'onmousewheel',
			'onmove',
			'onmoveend',
			'onmovestart',
			'onpaste',
			'onpropertychange',
			'onreadystatechange',
			'onreset',
			'onresize',
			'onresizeend',
			'onresizestart',
			'onrowenter',
			'onrowexit',
			'onrowsdelete',
			'onrowsinserted',
			'onscroll',
			'onselect',
			'onselectionchange',
			'onselectstart',
			'onstart',
			'onstop',
			'onsubmit',
			'onunload' 
	);
	$ra = array_merge ( $ra1, $ra2 );
	
	$found = true; // keep replacing as long as the previous round replaced something
	while ( $found == true ) {
		$val_before = $val;
		for($i = 0; $i < sizeof ( $ra ); $i ++) {
			$pattern = '/';
			for($j = 0; $j < strlen ( $ra [$i] ); $j ++) {
				if ($j > 0) {
					$pattern .= '(';
					$pattern .= '(&#[xX]0{0,8}([9ab]);)';
					$pattern .= '|';
					$pattern .= '|(�{0,8}([9|10|13]);)';
					$pattern .= ')*';
				}
				$pattern .= $ra [$i] [$j];
			}
			$pattern .= '/i';
			$replacement = substr ( $ra [$i], 0, 2 ) . '<x>' . substr ( $ra [$i], 2 ); // add in <> to nerf the tag
			$val = preg_replace ( $pattern, $replacement, $val ); // filter out the hex tags
			if ($val_before == $val) {
				// no replacements were made, so exit the loop
				$found = false;
			}
		}
	}
	return $val;
}

/**
 * 邮件发送函数
 * @$to 邮箱
 * @$subject 邮箱标题
 * @$content 邮箱内容
 */
function send_mail($to, $subject, $content) {
	Vendor ( 'PHPMailer.PHPMailerAutoload' );
	$mail = new PHPMailer (); // 实例化
	$mail->IsSMTP (); // 启用SMTP
	$mail->Host = C ( 'MAIL_HOST' ); // smtp服务器的名称（这里以126邮箱为例）
	$mail->SMTPAuth = C ( 'MAIL_SMTPAUTH' ); // 启用smtp认证
	$mail->Username = C ( 'MAIL_USERNAME' ); // 你的邮箱名
	$mail->Password = C ( 'MAIL_PASSWORD' ); // 邮箱密码
	$mail->From = C ( 'MAIL_FROM' ); // 发件人地址（也就是你的邮箱地址）
	$mail->FromName = C ( 'MAIL_FROMNAME' ); // 发件人姓名
	$mail->AddAddress ( $to, "name" );
	$mail->WordWrap = 50; // 设置每行字符长度
	$mail->SMTPSecure = "ssl"; // 设置加密的smtp服务，ssl的连接方式
	$mail->Port = 465; // 安全发送端口
	$mail->IsHTML ( C ( 'MAIL_ISHTML' ) ); // 是否HTML格式邮件true/false
	$mail->CharSet = C ( 'MAIL_CHARSET' ); // 设置邮件编码
	$mail->Subject = $subject; // 邮件主题
	$mail->Body = $content; // 邮件内容
	$mail->AltBody = "This is the body in plain text for non-HTML mail clients"; // 邮件正文不支持HTML的备用显示
	if (! $mail->Send ()) {
		// echo "Message could not be sent. <p>";
		// echo "Mailer Error: " . $mail->ErrorInfo;
		// exit();
		Think\Log::record ( $mail->ErrorInfo );
		$data ['msg'] = $mail->ErrorInfo;
		$data ['flag'] = false;
		return $data;
	} else {
		Think\Log::record ( date ( 'Y-m-d H:i:s', time () ) . "  邮件发送成功", "INFO" );
		$data ['msg'] = 'SUCCEE';
		$data ['flag'] = true;
		return $data;
	}
}

/**
 * 根据IP获取城市区域
 * @$ip ip地址
 * @return 区域
 */
function ip_location($ip = "") {
	$iplocation = new \Org\Net\IpLocation ( 'UTFWry.dat' );
	$area = $iplocation->getlocation ( $ip );
	$str = $area ['country'] . $area ['area'];
	return $str;
}

/**
 * 获取几天前是几号
 * 
 * @param int $day 几天前的天数    	
 * @param string $format 日期时间的格式
 * @param string $daydate 自定义的日期
 * @param data    	
 * return string 日期格式
 */
function date_to_today($format, $day, $daydate = "") {
	$t = "";
	if ($daydate != "") {
		$t = strtotime ( $daydate ) + 3600 * 8;
	} else {
		$t = time () + 3600 * 8;
	}
	$tget = $t - 3600 * 24 * $day;
	return date ( $format, $tget );
}
/**
 * 获取几天后是几号
 * 
 * @param int $day 几天后的天数    	
 * @param string $format 日期时间的格式
 * @param string $daydate 自定义的日期
 * @param data   	
 * return string 日期格式
 */
function today_to_date($format, $day, $daydate = "") {
	$time = "";
	if ($daydate != "") {
		$time = strtotime ( $daydate ) + $day * 24 * 3600;
	} else {
		$time = time () + $day * 24 * 3600;
	}
	$date = date ( $format, $time );
	return $date;
}
/**
 * 得到token
 * @return unknown
 */
function get_token($appid="",$secret="") {
	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
	$json = https_request($url);
	print_log("function获取token:".$json);
	$arr = json_decode($json, true);
	$access_token = $arr['access_token'];
	$data['expire_time'] = time() + 6000;
	$data['access_token'] = $access_token;
	fileWrite("access_token.json",$data);
	return $access_token;
}
/**
 * 文件写入
 */
function fileWrite($fileName="",$data=null){
	if($fileName!=""&&$data!=null){
		$fpath = C('PW').$fileName;
		//file_put_contents($fp, json_encode($data), FILE_APPEND);
		print_log("数据写入：".json_encode($data));
		$fp=fopen($fpath,'w');
		fwrite($fp,json_encode($data));
		fclose($fp);
		return true;
	}else{
		return false;
	}
}
/**
 * 文件读取
 */
function fileRead($fileName=""){
	if($fileName!=""){
		//$data = json_decode(file_get_contents(C('PW').$fileName),true);
		// print_log("---打印文件读取数据--".json_encode($data));
		$fpath = C('PW').$fileName;
		$fp=fopen($fpath,'r');
		$data = fread($fp,filesize($fpath));
		fclose($fp);
		return json_decode($data,true);
	}else{
		return null;
	}
		
}
/**
 * 文件写入
 * @param $fileName 文件名
 * @param $data 写入内容
 * return boolean
 */
function file_write($fileName = "", $data = null) {
    if($fileName!=""&&$data!=null){
        $fpath = C('PW').$fileName;//微信文件路径
        $fp = fopen($fpath, "w");//打开写入权限
        fwrite($fp, json_encode($data));//把data转成json内容写入进去
        fclose($fp);//关闭文件
        return true;
    }else{
        return false;
    }
}
/**
 * 文件读取
 * @param $fileName 文件名
 * return string
 */
function file_read($fileName = "") {
if($fileName!=""){
        return $data = json_decode(file_get_contents("./Public/".$fileName));
    }else{
        return null;
    }
}

/**
 * 删除二维数组中根据某一键值重复的数据
 * $arr 需要过滤的数组
 * $key 二维数组中需要根据这个key过滤
 * 
 * @param array $arr  	
 * @param string $key        	
 * @return array
 */
function assoc_unique($arr, $key) {
	$tmp_arr = array ();
	foreach ( $arr as $k => $v ) {
		if (in_array ( $v [$key], $tmp_arr )) { // 搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
			unset ( $arr [$k] );
		} else {
			$tmp_arr [] = $v [$key];
		}
	}
	sort ( $arr ); // sort函数对数组进行排序
	return $arr;
}

/**
 * 将HTML标签转义
 * 例如 :> &gt
 * @param $str 
 */
function html_encode($str = '') {
	return htmlspecialchars ( $str );
}
/**
 * 与html_encode方法相反还原HTML标签
 * @param $str 
 */
function html_decode($str = '') {
	return htmlspecialchars_decode ( $str );
}
/*
 * 富文本编辑框提取图片路径
 * @param $str 富文本的所有内容
 * return string
 */
function text_url($str = '') {
	$res = html_decode ( $str );
	preg_match_all ( '/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/', $res, $match );
	return $match;
}
/*
 * 富文本编辑框提取内容不包含图片
 * @param $str 富文本的所有内容
 * return string
 */
function text_content($str = '') {
	$cc = $str;
	$content = html_decode ( $cc );
	$content = preg_replace ( '/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/', '', $content );
	return $content;
}

/*
 * 提取富文本中的文字
 * @param $str 富文本的所有内容
 * return string
 */
function extractText($content = '') {
	$content = str_replace ( "\n", '', $content );
	$content = str_replace ( "\r", '', $content );
	$content = preg_split ( '/<[^>]+>/iU', $content );
	// $str = array_pop($content);
	$contentStr = implode ( $content );
	return $contentStr;
}
/**
 * 反编译data/base64数据流并创建图片文件
 * 
 * @author Lonny ciwdream@gmail.com
 * @param string $baseData
 *        	data/base64数据流
 * @param string $Dir
 *        	存放图片文件目录
 * @param string $fileName
 *        	图片文件名称(不含文件后缀)
 * @return mixed 返回新创建文件路径或布尔类型
 */
function base64_dec_img($baseData, $Dir, $fileName) {
	// 前台访问URL API
	$__URL__ = C ( "DOMAINNAME" );
	// 服务器根目录绝对路径获取API
	$__root__ = isset ( $_SERVER ['DOCUMENT_ROOT'] ) ? $_SERVER ['DOCUMENT_ROOT'] : (isset ( $_SERVER ['APPL_PHYSICAL_PATH'] ) ? trim ( $_SERVER ['APPL_PHYSICAL_PATH'], "\\" ) : (isset ( $_ ['PATH_TRANSLATED'] ) ? str_replace ( $_SERVER ["PHP_SELF"] ) : str_replace ( str_replace ( "/", "\\", isset ( $_SERVER ["PHP_SELF"] ) ? $_SERVER ["PHP_SELF"] : (isset ( $_SERVER ["URL"] ) ? $_SERVER ["URL"] : $_SERVER ["SCRIPT_NAME"]) ), "", isset ( $_SERVER ["PATH_TRANSLATED"] ) ? $_SERVER ["PATH_TRANSLATED"] : $_SERVER ["SCRIPT_FILENAME"] )));
	// 上诉两个变量，依据实际情况自行修改
	try {
		$expData = explode ( ';', $baseData );
		$postfix = explode ( '/', $expData [0] );
		if (strstr ( $postfix [0], 'image' )) {
			$postfix = $postfix [1] == 'jpeg' ? 'jpg' : $postfix [1];
			$storageDir = $Dir . DIRECTORY_SEPARATOR . $fileName . '.' . $postfix;
			$export = base64_decode ( str_replace ( "{$expData[0]};base64,", '', $baseData ) );
			$returnDir = str_replace ( str_replace ( '/', '\\', $__root__ ), '', $storageDir );
			try {
				file_put_contents ( $storageDir, $export );
				return $__URL__ . $returnDir;
			} catch ( Exception $e ) {
				return false;
			}
		}
	} catch ( Exception $e ) {
		return false;
	}
	return false;
}
/**
 * 上海创蓝 --- 短信发送第三方接口组件实现
 * 
 * @param int $mobile
 * @param string $content
 * return boolean
 */
function send_mobile_note_cl($mobile, $content){
    $data['account'] = C('MOBILE_NOTE_CONFIG')['API_ACCOUNT'];
    $data['pswd'] = C('MOBILE_NOTE_CONFIG')['API_PASSWORD'];
    $data['mobile'] = $mobile;
    $data['msg'] = $content;
    $data['needstatus'] = false;//是否需要状态报告，取值true或false
    $url = C('MOBILE_NOTE_CONFIG')['API_SEND_URL'];
    $stud = https_request($url,$data);   
    if(explode(',', $stud)[1]==0){
        return true;
    }else{
        return false;
    }
}

/**
 *  * 请求接口
 * @param unknown $url
 * @param string $data
 * @param string $httpheader  设置请求格式 例如 $httpheader = "content-type:text/json;charset=UTF-8";
 * @return mixed
 */
function https_request($url, $data=null,$httpheader="") {
    /*
     $data = "
     <xml>
     <appid>wxb1ca8b4f1e19223c</appid>
     <attach>支付测试</attach>
     <body>NATIVE支付测试</body>
     <mch_id>1242440402</mch_id>
     <nonce_str>wstrlc7c576bj74epf9h4nrrk8mse1xw</nonce_str>
     <notify_url>http://www.27um.com/notify_url.php</notify_url>
     <out_trade_no>98</out_trade_no>
     <spbill_create_ip>14.23.150.211</spbill_create_ip>
     <total_fee>1</total_fee>
     <trade_type>NATIVE</trade_type>
     <sign>2575A297F88D7D8B3ADBC3F2D16515CD</sign>
     </xml>";
     */
    //$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
    //$postFields = http_build_query($data);//将参数格式化  例如：account=jiekou-bjcs-01&pswd=Beijing107
    //print_log("------------------------------发送的数据:".$postFields);
    $curl = curl_init();//初始化一个curl
    if($httpheader!=""){
        $header[] = $httpheader;//"content-type:text/json;charset=UTF-8";
        curl_setopt($curl,CURLOPT_HTTPHEADER,$header);//设置 HTTP 头字段
    }
    curl_setopt($curl, CURLOPT_URL, $url);//需要获取的url
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//FALSE 禁止 cURL 验证对等证书
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//检查服务器SSL证书中是否存在一个公用名
    if(!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);//1 时会发送 POST 请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);// 全部数据使用HTTP协议中的 "POST" 操作来发送 
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//1时以字符串返回
    $output = curl_exec($curl);//执行一个会话
    curl_close($curl);//关闭会话
    
    return $output;
}
/**
 *使用带有证书的CURL请求
 * @param string $url url链接
 * @param unknown $vars
 * @param number $second  秒数
 * @param unknown $aHeader
 * @return mixed|boolean
 */
function curl_post_ssl($url, $vars, $second=30,$aHeader=array()){
    $ch = curl_init();
    //超时时间
    curl_setopt($ch,CURLOPT_TIMEOUT,$second);//允许url执行最大秒数
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);//获取的信息以字符串返回
    //这里设置代理，如果有的话
    //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
    //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
    curl_setopt($ch,CURLOPT_URL,$url);//获取url地址
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);//FALSE 禁止 cURL 验证对等证书
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);//检查服务器SSL证书中是否存在一个公用名

    //以下两种方式需选择一种

    //第一种方法，cert 与 key 分别属于两个.pem文件
    //默认格式为PEM，可以注释
    curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');//证书类型
    curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/Public/cret/apiclient_cert.pem');//一个包含 PEM 格式证书的文件名。 
    //默认格式为PEM，可以注释
    curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');//私钥的加密
    curl_setopt($ch,CURLOPT_SSLKEY,getcwd().'/Public/cret/apiclient_key.pem');//包含 SSL 私钥的文件名。


    curl_setopt($ch,CURLOPT_CAINFO,getcwd().'/Public/cret/rootca.pem');//一个保存着1个或多个用来让服务端验证的证书的文件名
    //第二种方式，两个文件合成一个.pem文件
    //curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');

    if( count($aHeader) >= 1 ){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);//设置 HTTP 头字段
    }
    //使用带有参数的POST请求
    if(!empty($vars)) {
        curl_setopt($ch, CURLOPT_POST, 1);//TRUE 时会发送 POST 请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);//全部数据使用HTTP协议中的 "POST" 操作来发送
    }

    $data = curl_exec($ch);//执行会话
    if($data){
        curl_close($ch);//关闭会话
        return $data;
    }else{
        $error = curl_errno($ch);//返回最后一次的错误号
        print_log("错误码：".$error);
        echo "call faild, errorCode:$error";
        curl_close($ch);//关闭会话
        return false;
    }
}

/**
 * 文件请求CURL
 * @param string $url 
 * @return multitype:
 */
function http_two_dimension_code($url=''){
    $curl = curl_init($url);//创建会话
    curl_setopt($curl,CURLOPT_HEADER,0);//启用时会将头文件的信息作为数据流输出。
    curl_setopt($curl,CURLOPT_NOBODY,0);//TRUE 时将不输出 BODY 部分。同时 Mehtod 变成了 HEAD。修改为 FALSE 时不会变成 GET。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//FALSE 禁止 cURL 验证对等证书
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//设置为 1 是检查服务器SSL证书中是否存在一个公用名
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//以字符串返回
    $package = curl_exec($curl);//执行会话
    $httpinfo = curl_getinfo($curl);//获取一个cURL连接资源句柄的信息
    curl_close($curl);//关闭会话
    return  array_merge(array("body"=>$package),array("header"=>$httpinfo));
}

/**
 * DES数据加密
 * @param $str 加密的内容
 * @param $key 密钥
 * return string
 */
function des_encrypt_php($str,$key=""){
    $engine = DES_PHP\DESPHP::getInstance($key);
    return $engine->encrypt($str);
}
/**
 * DES数据解密
 * @param $str 密文的内容
 * @param $key 密钥
 * return string
 */
function des_decrypt_php($str,$key=""){
    $engine = DES_PHP\DESPHP::getInstance($key);
    return $engine->decrypt($str);
}

/**
 * 防止SQL注入，进行参数过滤
 * @param unknown $str
 * @return mixed
 */
function dowith_sql($str){
        $str = str_replace("and","",$str);
        $str = str_replace("execute","",$str);
        $str = str_replace("update","",$str);
        $str = str_replace("count","",$str);
        $str = str_replace("chr","",$str);
        $str = str_replace("mid","",$str);
        $str = str_replace("master","",$str);
        $str = str_replace("truncate","",$str);
        $str = str_replace("char","",$str);
        $str = str_replace("declare","",$str);
        $str = str_replace("select","",$str);
        $str = str_replace("create","",$str);
        $str = str_replace("delete","",$str);
        $str = str_replace("insert","",$str);
        $str = str_replace("between","",$str);
        $str = str_replace("AND","",$str);
        $str = str_replace("EXECUTE","",$str);
        $str = str_replace("UPDATE","",$str);
        $str = str_replace("COUNT","",$str);
        $str = str_replace("CHR","",$str);
        $str = str_replace("MID","",$str);
        $str = str_replace("MASTER","",$str);
        $str = str_replace("TRUNCATE","",$str);
        $str = str_replace("CHAR","",$str);
        $str = str_replace("DECLARE","",$str);
        $str = str_replace("SELECT","",$str);
        $str = str_replace("CREATE","",$str);
        $str = str_replace("DELETE","",$str);
        $str = str_replace("INSERT","",$str);
        $str = str_replace("BETWEEN","",$str);
        //$str = str_replace("OR","",$str);
        $str = str_replace("'","",$str);
        $str = str_replace("","",$str);
        //$str = str_replace(" ","",$str);
        //$str = str_replace("or","",$str);
        //$str = str_replace("=","",$str);
        $str = str_replace("%20","",$str);
        
    return $str;
}
/**
 * Excel表导出
 * @param string $expTitle
 * @param Array $expCellName
 * @param Array $expTableData
 */
function exportExcel($expTitle="",$expCellName="",$expTableData=""){
    $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
    $fileName = $xlsTitle.date('Y-m-d');//or $xlsTitle 文件名称可根据自己情况设定
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    vendor("PHPExcel.PHPExcel");
    $objPHPExcel = new PHPExcel();
    $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
    $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
    for($i=0;$i<$cellNum;$i++){
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
    }
    // Miscellaneous glyphs, UTF-8
    for($i=0;$i<$dataNum;$i++){
        for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3),$expTableData[$i][$expCellName[$j][0]]);
        }
    }
    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    return true;
}

/**
 * 生成随机数
 * @param number $len
 * @param string $format
 * @return string
 */
function randpw($len=8,$format='ALL'){
    $is_abc = $is_numer = 0;
    $password = $tmp ='';
    switch($format){
        case 'ALL':
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
        case 'CHAR':
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        case 'NUMBER':
            $chars='0123456789';
            break;
        default :
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
    }
    
    while(strlen($password)<$len){
        (mt_rand()%strlen($chars));
        $tmp =substr($chars,(mt_rand()%strlen($chars)),1);
        
        if(($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 )|| $format == 'CHAR'){
            $is_numer = 1;
        }
        if(($is_abc <> 1 && preg_match('/[a-zA-Z]/',$tmp)) || $format == 'NUMBER'){
            $is_abc = 1;
        }
        if($tmp==""&&$is_numer <> 1 && $is_abc <> 1){
            $tmp = "a";
        }else if($tmp==""&&$is_numer == 1){
            $tmp = 1;
        }else if($tmp==""&&$is_abc == 1){
            $tmp = "a";
        }
        $password.= $tmp;
    }
    if($password==""&&$is_numer <> 1 && $is_abc <> 1){
            $password = "a";
    }else if($password==""&&$is_numer == 1){
            $password = 1;
    }else if($password==""&&$is_abc == 1){
            $password = "a";
    }
    if($is_numer <> 1 || $is_abc <> 1 || empty($password) ){
            $password = randpw($len,$format);
    }
    return strtoupper($password);
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}


/**
 * 遍历删除目录
 * @param unknown $dirname 目录名
 * @return boolean
 */
function rmdirr($dirname) {
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    if ($dir) {
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            //递归
            rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
        }
    }
}

/**
 * 获取文件修改时间
 * @param string $file 文件名
 * @param string $DataDir 文件路径
 * @return string
 */
function getfiletime($file, $DataDir) {
    $a = filemtime($DataDir . $file);
    $time = date("Y-m-d H:i:s", $a);
    return $time;
}

/**
 * 获取文件的大小
 * @param string $file 文件名
 * @param string $DataDir 文件路径
 * @return string
 */
function getfilesize($file, $DataDir) {
    $perms = stat($DataDir . $file);
    $size = $perms['size'];
    // 单位自动转换函数
    $kb = 1024;         // Kilobyte
    $mb = 1024 * $kb;   // Megabyte
    $gb = 1024 * $mb;   // Gigabyte
    $tb = 1024 * $gb;   // Terabyte

    if ($size < $kb) {
        return $size . " B";
    } else if ($size < $mb) {
        return round($size / $kb, 2) . " KB";
    } else if ($size < $gb) {
        return round($size / $mb, 2) . " MB";
    } else if ($size < $tb) {
        return round($size / $gb, 2) . " GB";
    } else {
        return round($size / $tb, 2) . " TB";
    }
}

/**
 * 获取session中的值 仅适用于以下的加密处理方式
 *  $array['gm_code'] = $userCode;
 *  $array['gm_username'] = $username;
 *  $arr['obj'] = $array;
 *  $arr['logintime'] = time();
 *  $data = des_encrypt_php(json_encode($arr));
 * @param string $str session中用户的字段加密后的字符串 例如 session('member')
 * @param string $key 会员信息对象 obj
 * @return mixed
 */
function get_value($str,$key){
    $des_decrypt = des_decrypt_php($str);
    $memObj = json_decode($des_decrypt,true);
    $returnStr = $memObj[$key];
    return $returnStr; 
}
/**
 *  短信验证码信息存入方式 
 *  $data['code'] = $code;
 *  $data['times'] = time()+120;
 *  session("verifiedCode",$data);
 *  @param string $noteCode 短信验证码
 * @return Ambigous <boolean, mixed, NULL, unknown>
 */
function checkMobileCodePeriod($noteCode){
    $data = session("verifiedCode");
    $ret['flag1'] = false;
    $ret['flag2'] = false;
    if($data){
        if((time()-$data['times']) < C('STATIC_PROPERTY')['NOTE_CODE_TIME']){
            $ret['flag1'] = true;
            $ret['code'] = session("verifiedCode")['code'];
            if(strtoupper($noteCode) == $ret['code']){
                $ret['flag2'] = true;
            }
        }
    }
    if($ret['flag1']&&$ret['flag2']){
        $return = true;
    }else{
        $return = false;
    }
    return $return;
}
/**
 * 检测当前请求的方法是否在限定的范围内
 * 如果存在返回true
 * 否则 返回 false
 * @return boolean
 */
function check_method_name(){
    $fun = $_SERVER['PATH_INFO'];
    if(substr_count($fun,'/')>1){
        $str = substr($fun,strpos($fun,'/')+1);
        $str = substr($str,0,strpos($str,'/'));
    }else{
        $str = substr($fun,strpos($fun,'/')+1);
    }
    if($str!=""){
        return in_array($str,C("STATIC_PROPERTY")['LOGIN_DETECTION_METHOD']);
    }else{
        return false;
    }
}

/**
 * 判断当前请求是否为AJAX请求
 * 是AJAX请求 返回true
 * 否则返回 false
 * @return boolean
 */
function is_ajax() {
    if(array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER)) {
        if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])){
            return true;
        }
    }
    //如果参数传递的参数中有ajax
    if(!empty($_POST['ajax']) || !empty($_GET['ajax'])){
        return true;
    }
    return false;
}

 /**
  * 计算距现在的时间差
  * @param data $datatime 输入时间
  * @return string
  */
function get_date_time($datatime){
    $times = time()-strtotime($datatime);
    
    $datatimrs = "";
    if($times>0){
        if(0 < $times&&$times < 3600){
            $datatimrs = time2Units($times); //date("i分",$times);
        }else if($times==3600){
            $datatimrs = "1小时";
        }else if($times>3600&&$times<3600*24){
            
            $datatimrs = time2Units($times);//date("H时i分",$times);
        }else{
            $datatimrs = time2Units($times);;
        }
    }else{
        $datatimrs = "1分";
    }
        return $datatimrs;
}
/**
 * 两个时间的时间差
 * @param string $time 相差的秒数
 * @return string
 */
function time2Units ($time)
{
    $year   = floor($time / 60 / 60 / 24 / 365);
    $time  -= $year * 60 * 60 * 24 * 365;
    $month  = floor($time / 60 / 60 / 24 / 30);
    $time  -= $month * 60 * 60 * 24 * 30;
    $week   = floor($time / 60 / 60 / 24 / 7);
    $time  -= $week * 60 * 60 * 24 * 7;
    $day    = floor($time / 60 / 60 / 24);
    $time  -= $day * 60 * 60 * 24;
    $hour   = floor($time / 60 / 60);
    $time  -= $hour * 60 * 60;
    $minute = floor($time / 60);
    $time  -= $minute * 60;
    $second = $time;
    $elapse = '';

    $unitArr = array('年'  =>'year', '个月'=>'month',  '周'=>'week', '天'=>'day',
        '小时'=>'hour', '分钟'=>'minute', '秒'=>'second'
    );

    foreach ( $unitArr as $cn => $u )
    {
        if ( $$u > 0 )
        {
            $elapse = $$u . $cn;
            break;
        }
    }

    return $elapse;
}

/**
 * 检查登陆状态
 * @return boolean
 */
/* function check_login_status() {
    //设置登陆超时为60分钟
    $nowtime = time();
    if($_SESSION['member']){
        $s_time = getvalue($_SESSION['member'],'logintime');
        if($s_time){
            if (($nowtime - $s_time) > C('STATIC_PROPERTY')['FRONT_LOGIN_TIME']) {
                unset($_SESSION['member']);
                return false;
                //$this->showErrorPage('登录超时，请重新登录', U("Login/index"));
            } else {
                $memObj = M('gypt_member');
                $member['obj'] = getvalue(session('member'),'obj');
                $memMap['gm_code'] = array('eq',$member['obj']['gm_code']);
                $objs = $memObj->where($memMap)->find();
                $member['obj']['gm_username'] = $objs['gm_name']==""||$objs['gm_name']==null?$objs['gm_username']:$objs['gm_name'];
                $member['obj']['photopath'] = $objs['gm_photo'];
                $member['logintime'] = $nowtime;
                $_SESSION['member']= des_encrypt_php(json_encode($member));
                return true;
            }
        }
    }
    return false;
} */

/**
 * 将UNICODE编码后的内容进行解码
 * @param string $name 输入的字符串
 * @return Ambigous <string, unknown>
 */
function unicode_decode($name=""){
    // 转换编码，将Unicode编码转换成可以浏览的utf-8编码
    $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
    preg_match_all($pattern, $name, $matches);
    if (!empty($matches)){
        $name = '';
        for ($j = 0; $j < count($matches[0]); $j++){
            $str = $matches[0][$j];
            if (strpos($str, '\\u') === 0){
                $code = base_convert(substr($str, 2, 2), 16, 10);
                $code2 = base_convert(substr($str, 4), 16, 10);
                $c = chr($code).chr($code2);
                $c = iconv('UCS-2', 'UTF-8', $c);
                $name .= $c;
            }else{
                $name .= $str;
            }
        }
    }
    return $name;
}
/**
 * 解密session
 * @param array $session session中字段对应的值
 * @return mixed
 */
 function jiema($session){
 	$info = json_decode(des_decrypt_php($session),true);
 	return $info;
 }
 /*
  * @无限级分类递归查询
  * @param number $pid    父ID 默认0
  * @param string $tbname 表名
  * @param string $pidn   父ID字段名
  * @param string $idn    子ID字段名
  * @param string $names  名称字段名
  * @param string $order  排序字段 自定义
  * @param number $mark   返回数据拼接（自定义）方法名getStr
  * @$data  扩展参数<可以是数组也可以是单个参数>
  * @return $data  Array 返回值，可扩展
  */
 function recursion($id=0,$pidn="",$idn="",$data) {
 	$list = array();
 	foreach($data as $v) {
 		if($v[$pidn] == $id) {
 			$v['son'] = recursion($v[$idn],$pidn,$idn,$data);
 			if(empty($v['son'])) {
 				unset($v['son']);
 			}
 			array_push($list, $v);
 		}
 	}
 	return $list;
 }
 /**
  * SMS 短信
  *
  * @param int $mobile
  * @param string $content
  * return boolean
  */
 function send_mobile_sms($mobile, $content){
     $target = "http://api.chanzor.com/send";
     //替换成自己的测试账号,参数顺序和wenservice对应
     $post_data = "account=".C("DUAN_ADMIN")."&password=".strtoupper(md5(C("DUAN_PASWORD")))."&mobile={$mobile}&sendTime=&content=".rawurlencode($content)."【妥妥运车】";
     //$binarydata = pack("A", $post_data);
     $url_info = parse_url($target);
     $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
     $httpheader .= "Host:" . $url_info['host'] . "\r\n";
     $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
     $httpheader .= "Content-Length:" . strlen($post_data) . "\r\n";
     $httpheader .= "Connection:close\r\n\r\n";
     //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
     $httpheader .= $post_data;
     print_log("短信发送：".$httpheader);
     $fd = fsockopen($url_info['host'], 80);
     fwrite($fd, $httpheader);
     $gets = "";
     while(!feof($fd)) {
         $gets .= fread($fd, 128);
     }
     fclose($fd);
     //return $gets;
     //$gets = Post($post_data, $target);
	 print_log("短信返回原始值：".$gets);
     $start=strpos($gets,"<?xml");
     $data=substr($gets,$start);
     $xml=simplexml_load_string($data);
     //print_log("短信回执:".$data);
     return json_decode(json_encode($xml),TRUE);
 }
 /**
  * 验证码验证
  * @param unknown $code
  * @param string $id
  * @return boolean
  */
 function check_verify($code, $id = ""){
 	$verify = new \Think\Verify();
 	return $verify->check($code, $id);
 }
 /**
  * Token令牌生成
  * 防止重复提交
  */
 function rand_token(){
     $Token_rand = time().mt_rand(100000, 999999);
      session('Session_From_Token',$Token_rand);    
     return $Token_rand;
 }
 
 /**
  * Token令牌验证
  * 防止重复提交
  */
 function from_token($token=""){
 	if($token==""){
 		$token = I('From_Token');
 	}
     $result = remove_xss($token) === session('Session_From_Token') ? true : false;
     session('Session_From_Token',null);
     //rand_token();
     return $result;
 }
 /*
  * 获取省份（妥妥）
  */
 function get_province(){
     $map['area_pid'] = array('eq',1);
     $pro = M('area') -> where($map) -> select();
     return $pro;
 }
 /*
  * 获取车品牌(妥妥)
  */
 function get_brand(){
     $map['cxjj_pid'] = array('eq',0);
     $brand = M('carxing') -> where($map) -> select();
     return $brand;
 }
 
  /*
  * 中英文字符串截取(妥妥)  mbstr($str,20);
  */
  function mbstr($str, $len, $charset="utf-8")
{
    //如果截取长度小于等于0，则返回空
    if( !is_numeric($len) or $len <= 0 )
    {
        return "";
    }

    //如果截取长度大于总字符串长度，则直接返回当前字符串
    $sLen = strlen($str);
    if( $len >= $sLen )
    {
        return $str;
    }
 
    //判断使用什么编码，默认为utf-8
    if ( strtolower($charset) == "utf-8" )
    {
        $len_step = 3; //如果是utf-8编码，则中文字符长度为3  
    }else{
        $len_step = 2; //如果是gb2312或big5编码，则中文字符长度为2
    } 

    //执行截取操作
    $len_i = 0; 
    //初始化计数当前已截取的字符串个数，此值为字符串的个数值（非字节数）
    $substr_len = 0; //初始化应该要截取的总字节数

    for( $i=0; $i < $sLen; $i++ )
    {
        if ( $len_i >= $len ) break; //总截取$len个字符串后，停止循环
        //判断，如果是中文字符串，则当前总字节数加上相应编码的中文字符长度
        if( ord(substr($str,$i,1)) > 0xa0 )
        {
            $i += $len_step - 1;
            $substr_len += $len_step;
        }else{ //否则，为英文字符，加1个字节
            $substr_len ++;
        }
    $len_i ++;
    }
    $result_str = substr($str,0,$substr_len );
    return $result_str;
}