<?php
namespace Customer\Controller;

class TransferInfoController extends BaseController {
    public function index(){
        $o_code = I("o_code");
        //取session判断其是否登录
        //$session_User = jiema(session('userData'));
        //if($session_User['id']>0){
            if($o_code!=""){
                $transferRes = $this -> showInfo($o_code);
                $this -> assign('transfer',$transferRes);
                $this->display("MyOrder:transferinfo");
            }
        //}else{
        //    $this->display("HomePage:home_page");
        //}
    }
    
    public function showInfo($o_code){
        $tObj = D('TransferInfo');
        $tRes = $tObj -> showInfo($o_code);
        return $tRes;
    }
}