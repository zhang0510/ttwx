<?php
namespace Customer\Controller;
/**
 * 控制器
 */
class WorklwtController extends IndexController {
    /**
     * 个人信息
     */
    function memberInfo(){
        $lwtObj = D("Worklwt");
        $code = I('code');
        if($code == ""){
            $opid = session("tokenInfo")['openid']==""||session("tokenInfo")['openid']==null?I("opid"):session("tokenInfo")['openid'];
           // $opid = 'oizv4spagfeILyMJHyONUwOnX7J4';
            $ret = $lwtObj->overBooking($opid);
            $info = $this->getuerInfo($opid);
        }else{
            $info = $this->memberAuthorization($code);
            $opid = $info['openid'];
            //$opid = 'oizv4spagfeILyMJHyONUwOnX7J4';
            $ret = $lwtObj->overBooking($info['openid']);
        }
        $this->assign("opid",$opid);
        $this->assign("urls","Worklwt/memberInfo");
        if($ret['flag']){
            $this->assign("ret",$ret['ret']);
            $this->assign("info",$info);
            $this->display("Member:member_infos");
        }else{
            $this->display("Login:mobile_login");
        }
    }
    /**
     * 个人信息
     */
    function member_info(){
        $lwtObj = D("Worklwt");
        $opid = session("tokenInfo")['openid']==""||session("tokenInfo")['openid']==null?I("opid"):session("tokenInfo")['openid'];
        // $opid = 'oizv4spagfeILyMJHyONUwOnX7J4';
        $ret = $lwtObj->overBooking($opid);
        $info = $this->getuerInfo($opid);
        if($ret['flag']){
            $this->assign("ret",$ret['ret']);
            $this->assign("info",$info);
            $this->display("Member:member_info");
        }else{
            $this->display("Login:mobile_login");
        }
    }
    /**
     * 个人信息修改
     */
    function memberUpdate(){
        $lwtObj = D("Worklwt");
        $data['user_name'] = I("user_name");
        $data['email'] = I("email");
        $data['sex'] = I("sex");
        $data['by_tel'] = I("by_tel");
        $data['identity'] = I("identity");
        $opid = session("tokenInfo")['openid'];
        $ret = $lwtObj->memberUpdate($opid,$data);
        $this->ajaxReturn($ret);
    }
    /**
     * 常用联系人列表
     */
    function topContacts(){
        $lwtObj = D("Worklwt");
        $opid = session("tokenInfo")['openid'];
        $list = $lwtObj->getLinkManList($opid);
        $this->assign("list",$list);
        $this->display("Linkman:link_man");
    }
    /**
     * 常用联系人添加
     */
    function linkManAdd(){
        $lwtObj = D("Worklwt");
        $opid = session("tokenInfo")['openid'];
        $ret = $lwtObj->overBooking($opid);
        $this->assign("ret",$ret['ret']);
        $this->display("Linkman:link_manadd");
    }
    /**
     * 联系人插入数据
     */
    function linkManInsert(){
        $lwtObj = D("Worklwt");
        $data['link_tel'] = I("tel");
        $data['link_num'] = I("idcode");
        $data['link_name'] = I("name");
        $data['user_code'] = I("user_code");
        $data['link_code'] = get_code("LCB");
        $this->ajaxReturn($lwtObj->linkManInsert($data));
    }
    /**
     * 常用联系人删除
     */
    function linkManDel(){
        $lwtObj = D("Worklwt");
        $id = I("id");
        $this->ajaxReturn($lwtObj->linkManDel($id));
    }
    /**
     * 我的订单
     */
    function memberOrder(){
        $lwtObj = D("Worklwt");
        $code = I('code');
        $mark = I('mark');
        if($code == ""){
            if($mark=="Q"){
                $mark='';
            }
            $opid = session("tokenInfo")['openid']==""||session("tokenInfo")['openid']==null?I("opid"):session("tokenInfo")['openid'];
            $list = $lwtObj->memberOrder($opid,$mark);
            $ret = $lwtObj->overBooking($opid);
        }else{
            $info = $this->memberAuthorization($code);
            $opid = $info['openid'];
            $ret = $lwtObj->overBooking($opid);
            $list = $lwtObj->memberOrder($info['openid']);
        }
        $this->assign("opid",$opid);
        $this->assign("urls","Worklwt/memberOrder");
        if($ret['flag']){
            $this->assign("mark",$mark);
            $this->assign("list",$list);
            $this->display("MyOrder:order_list");
        }else{
			print_log("-----000000000000000000------");
            $this->display("Login:mobile_login");
        }
    }
    /**
     * 订单详情
     */
    /* function orderInfo(){
        $id = I("code");
        $lwtObj = D("Worklwt");
        $order = $lwtObj->Order_Detail($id);
		print_log("订单信息:".json_encode($order));
        $carList = $order['carList'];
        $this -> assign('carList',$carList);
        $this -> assign('order',$order);
        $this -> assign('status',$order['status']);
        $this->display("MyOrder:order_info");
    } */
    //取消订单or退款
    public function delOrder($orderId=0,$type=""){
        $orderId = $orderId=0?I("orderId"):$orderId;
        $type = $type=""?I("type"):$type;
        if ($type!=""&&$orderId>0){
            //初始化Model
            $mObj = D("Worklwt");
            $orderDel = $mObj -> Order_Del($orderId,$type);
            $this-> ajaxReturn($orderDel);
        }
    }
    
    /**
     * ---------------------------------------------二期-----------------------
     */
    /**
     * 订单列表
     */
    public function myOrder(){
        $lwtObj = D("Worklwt");
        $code = I('code');
        if($code == ""){
            $opid = session("tokenInfo")['openid']==""||session("tokenInfo")['openid']==null?I("opid"):session("tokenInfo")['openid'];
            //$opid = 'oizv4spagfeILyMJHyONUwOnX7J4';
            $ret = $lwtObj->overBooking($opid);
            $list = $lwtObj->myOrder_list($ret['ret']['tel']);
        }else{
            $info = $this->memberAuthorization($code);
            $opid = $info['openid'];
            //$opid = 'oizv4spagfeILyMJHyONUwOnX7J4';
            $ret = $lwtObj->overBooking($opid);
            $list = $lwtObj->myOrder_list($ret['ret']['tel']);
        }
        $this->assign("opid",$opid);
        $this->assign("urls","Worklwt/myOrder");
        if($ret['flag']){
            //$this -> assign('show',$list['show']);
            $this -> assign('oList',$list['list']);
            $this -> assign('flag',$list['flag']);
            //$this -> assign('num',$list['number']);
            $this->display("MyOrder:order_list");
        }else{
            $this->display("Login:mobile_login");
        }
    }
    /**
     * 订单详情页
     */
    public function orderInfo(){
        //查询
        $orderCode = I("code");
        //初始化Model
        $mObj = D("Worklwt");
        $myVo = $mObj -> myOrder_info($orderCode);
        $this -> assign('info',$myVo);
        //进入页面
        $this->display("MyOrder:order_info");
    }
    
    /**
     * 承运保单列表
     */
    public function policyList(){
        $lwtObj = D("Worklwt");
        $code = I('code');
        if($code == ""){
            $opid = session("tokenInfo")['openid']==""||session("tokenInfo")['openid']==null?I("opid"):session("tokenInfo")['openid'];
            //$opid = 'oizv4spagfeILyMJHyONUwOnX7J4';
            $ret = $lwtObj->overBooking($opid);
            $list = $lwtObj->myOrder_list($ret['ret']['tel']);
        }else{
            $info = $this->memberAuthorization($code);
            $opid = $info['openid'];
            //$opid = 'oizv4spagfeILyMJHyONUwOnX7J4';
            $ret = $lwtObj->overBooking($opid);
            $list = $lwtObj->myOrder_list($ret['ret']['tel']);
        }
        $this->assign("opid",$opid);
        $this->assign("urls","Worklwt/policyList");
        if($ret['flag']){
            //$this -> assign('show',$list['show']);
            $this -> assign('oList',$list['list']);
            $this -> assign('flag',$list['flag']);
            //$this -> assign('num',$list['number']);
            $this->display("MyOrder:my_policy");
        }else{
            $this->display("Login:mobile_login");
        }
    }
    /**
     * 验车照片
     */
    function verifyCar(){
        $code = I("code");
        $ret = D("Worklwt")->verifyCar($code);
        $this->assign("ret",$ret);
        $this->display("MyOrder:verify_car");
    }
    /**
     * 委托合同列表
     */
    public function entrustList(){
        $lwtObj = D("Worklwt");
        $code = I('code');
        if($code == ""){
            $opid = session("tokenInfo")['openid']==""||session("tokenInfo")['openid']==null?I("opid"):session("tokenInfo")['openid'];
            //$opid = 'oizv4spagfeILyMJHyONUwOnX7J4';
            $ret = $lwtObj->overBooking($opid);
            $list = $lwtObj->myOrder_list($ret['ret']['tel']);
        }else{
            $info = $this->memberAuthorization($code);
            $opid = $info['openid'];
            //$opid = 'oizv4spagfeILyMJHyONUwOnX7J4';
            $ret = $lwtObj->overBooking($opid);
            $list = $lwtObj->myOrder_list($ret['ret']['tel']);
        }
        $this->assign("opid",$opid);
        $this->assign("urls","Worklwt/entrustList");
        if($ret['flag']){
            //$this -> assign('show',$list['show']);
            $this -> assign('oList',$list['list']);
            $this -> assign('flag',$list['flag']);
            //$this -> assign('num',$list['number']);
            $this->display("MyOrder:my_entrust");
        }else{
            $this->display("Login:mobile_login");
        }
    }
    /**
     * 位置列表
     */
    public function positionList(){
        $lwtObj = D("Worklwt");
        $code = I('code');
        if($code == ""){
            $opid = session("tokenInfo")['openid']==""||session("tokenInfo")['openid']==null?I("opid"):session("tokenInfo")['openid'];
            //$opid = 'oizv4spagfeILyMJHyONUwOnX7J4';
            $ret = $lwtObj->overBooking($opid);
            $list = $lwtObj->myOrder_list($ret['ret']['tel']);
        }else{
            $info = $this->memberAuthorization($code);
            $opid = $info['openid'];
            //$opid = 'oizv4spagfeILyMJHyONUwOnX7J4';
            $ret = $lwtObj->overBooking($opid);
            $list = $lwtObj->myOrder_list($ret['ret']['tel']);
        }
        $this->assign("opid",$opid);
        $this->assign("urls","Worklwt/positionList");
        if($ret['flag']){
            //$this -> assign('show',$list['show']);
            $this -> assign('oList',$list['list']);
            $this -> assign('flag',$list['flag']);
            //$this -> assign('num',$list['number']);
            $this->display("MyOrder:my_position");
        }else{
            $this->display("Login:mobile_login");
        }
    }
    /**
     * 优惠劵列表
     */
    public function mycoupon_list(){
        $lwtObj = D("Worklwt");
        $code = I("code");
        $myArray['user_code'] = array('eq',$code);
        $myList = $lwtObj -> mycoupon_list($myArray);
        $time = time();
        
        for($i=0;$i<count($myList['list']);$i++){
            if($myList['list'][$i]['fav_startime'] == '0000-00-00 00:00:00' && $myList['list'][$i]['fav_endtime'] =='0000-00-00 00:00:00'){
                if($myList['list'][$i]['fav_status'] == 'Y'){
                    $myList['list'][$i]['msg'] = "已用";
                }else{
                    $myList['list'][$i]['msg'] = "未用";
                }
            }elseif(strtotime($myList['list'][$i]['fav_startime']) <= $time && strtotime($myList['list'][$i]['fav_endtime']) >$time){
                if($myList['list'][$i]['fav_status'] == 'Y'){
                    $myList['list'][$i]['msg'] = "已用";
                }else{
                    $myList['list'][$i]['msg'] = "未用";
                }
            }else{
                $myList['list'][$i]['msg'] = "已作废";
            }
            if($myList['list'][$i]['fav_startime'] == '0000-00-00 00:00:00' && $myList['list'][$i]['fav_endtime'] =='0000-00-00 00:00:00'){
                $myList['list'][$i]['fav_startime'] = '';
                $myList['list'][$i]['fav_endtime'] = "长期有效";
            }else{
                $myList['list'][$i]['fav_startime'] = str_replace("-",".",substr($myList['list'][$i]['fav_startime'],0,10));
                $myList['list'][$i]['fav_endtime'] = str_replace("-",".",substr($myList['list'][$i]['fav_endtime'],0,10));
            }
        }
        //$this -> assign('show',$myList['show']);
        $this -> assign('cList',$myList['list']);
        $this -> assign('flag',$myList['flag']);
        $this -> assign("code",$code);
        $this -> assign("count",count($myList['list']));
        //$this -> assign('num',$myList['number']);
        $this->display("MyCoupon:my_coupon");
    }
    /**
     * 注销微信
     */
    public function cellationWx(){
        $opid = session("tokenInfo")['openid']==""||session("tokenInfo")['openid']==null?"":session("tokenInfo")['openid'];
        $tel = I("tel");
        $this->ajaxReturn(D("Worklwt")->wxClose($tel,$opid));
    }
    /**
     * 加载更多ajax获取数据
     */
    function wx_ajaxfollowUpList(){
        $wxObj = D("Worklwt");
        $ret = $wxObj->overBooking(I("opid"));
        $data = $wxObj -> myOrder_list($ret['ret']['tel'],I("pages"));
        $this->ajaxReturn($data);
    }
    /**
     * 加载更多ajax获取数据
     */
    function wx_ajaxFaveList(){
        $lwtObj = D("Worklwt");
        $code = I("code");
        $myArray['user_code'] = array('eq',$code);
        $myList = $lwtObj -> mycoupon_list($myArray,I("pages"));
        $time = time();
        for($i=0;$i<count($myList['list']);$i++){
            if(strtotime($myList['list'][$i]['fav_startime']) <= $time && strtotime($myList['list'][$i]['fav_endtime']) >$time){
                if($myList['list'][$i]['fav_status'] == 'Y'){
                    $myList['list'][$i]['msg'] = "已用";
                }else{
                    $myList['list'][$i]['msg'] = "未用";
                }
            }else{
                $myList['list'][$i]['msg'] = "已作废";
            }
            $myList['list'][$i]['fav_startime'] = str_replace("-",".",substr($myList['list'][$i]['fav_startime'],0,10));
            $myList['list'][$i]['fav_endtime'] = str_replace("-",".",substr($myList['list'][$i]['fav_endtime'],0,10));
        }
        $this->ajaxReturn($myList);
    }
}