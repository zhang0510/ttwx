<?php
namespace Customer\Controller;

class LoginController extends BaseController {
    
    function logins(){
    	$code = I("code");
    	print_log("进来没有获取code:".$code);
    	
    }
    //验证码
    public function verify_c() {
        $Verify = new \Think\Verify ();
        $Verify->fontSize = 48;
        $Verify->length = 4;
        $Verify->useNoise = false;
        $Verify->entry ();
    }
    
    //验证手机号
    function verifyPhone(){
        $tel=I("post.tel");
        $obj = D('Login');
        $flag = $obj -> loginAdmin($tel);
        $this -> ajaxReturn($flag);
    }
    
    //验证验证码填写是否正确
    function verifyCode(){
        //验证码
        function check_verify($code, $id = ""){
            $verify = new \Think\Verify();
            return $verify->check($code, $id);
        }
        $vCode = remove_xss(I('VCode',''));
        if(!check_verify($vCode)){
            $data = "亲，验证码输错了哦！";
            $this -> ajaxReturn($data);
        }
    }
    
    //发送短信验证码
    public function tel_SMS(){
        $mobileNum = I("mobileNum");
        $tel = I("tel")==""||I("tel")==null?$mobileNum:I("tel");
        //$tel="17778067725";
        $rand_num = mt_rand(1000, 9999);
        $mes = "亲爱的用户！您的验证码为：".$rand_num." 请您尽快填写！";
        $result['code'] = $rand_num;
		print_log("短信发送是否成功验证码:".$rand_num);
        $rets = send_mobile_sms($tel,$mes);
        print_log("短信发送是否成功:".$rets['returnstatus']);
        if($rets['status']==0){
            $result['massion'] =true;
            //$result['massion'] =false;
        }else{
            $result['massion'] =false;
            //$result['massion'] =true;
        }
        $this -> ajaxReturn($result);
    }
    
    //手机登录验证
    public function mobile_Login_Verify(){
        $result['type'] = 0;
        //Post接收
		$mobileNum = I("mobileNum");
        $tel = I("tel")==""||I("tel")==null?$mobileNum:I("tel");
		print_log("用户手机号:".$tel);
        if ($_POST){
        	print_log("--------bbbbbbbbbbbbbbbbbbbb------------");
            //接收参数
            
            $SM_VCode   =   remove_xss(I("SM_VCode"));
            $SMS_VCode  =   remove_xss(I("SMS_VCode"));
            $opid = I("opid");
            if($SM_VCode!=$SMS_VCode){
            	print_log("--------aaaaaaaaaaaaaaaaaaa------------");
                $result['info'] = "亲，动态验证码输错了哦！";
                $this -> ajaxReturn($result);
            }else{
                $data['opid'] = $opid;
                $Login = D("Login");
                $LOG = $Login->readUserInfo($opid);
                print_log("--------11111111111111111111------------");
                if(count($LOG)>0){
                	print_log("--------11112222222222222222222222211------------");
	                $map['tel'] = array('eq',$tel);
	                $ret = M("user")->where($map)->save($data);
	                if($ret){
	                    $result['type'] = 1;
	                }else{
	                    $result['info'] = "登录失败";
	                }
	                $this -> ajaxReturn($result);
                }else{
                	print_log("--------3333333333333333333------------");
                	$mapss['tel'] = array('eq',$tel);
                	$vsss = M("user")->where($mapss)->find();
                	if(count($vsss)==0){
                		print_log("--------4444444444444444444------------");
                		$red = $Login ->userInfoAdd($opid,$tel);
						$result['type'] = 1;
                		$this -> ajaxReturn($result);
                	}else{
                		$datadddd['opid'] = $opid;
                		M("user")->where($mapss)->save($datadddd);
                		print_log("--------555555555555555555555------------");
						$result['type'] = 1;
                		$this -> ajaxReturn($result);
                	}
                	
                }
            }
            
        }else{
        	print_log("--------ccccccccccccccccccccccc------------");
            $result['info'] = "请传入参数！";
            $this -> ajaxReturn($result);
        }
    }
}