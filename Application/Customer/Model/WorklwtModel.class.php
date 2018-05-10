<?php
namespace Customer\Model;
class WorklwtModel extends BaseModel{
    /**
     * 判断当前会员是否绑定
     */
    function overBooking($opid){
        $msg['flag'] = false;
        $map['opid'] = array('eq',$opid);
        $ret = M("user")->where($map)->find();
        if($ret){
            $msg['ret'] = $ret;
            $msg['flag'] = true;
        }
        return $msg;
    }
    /**
     * 会员修改信息
     */
    function memberUpdate($opid='',$data=''){
        $map['opid'] = array('eq',$opid);
        $ret = M("user")->where($map)->save($data);
        if($ret){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 获取常用联系人
     */
    function getLinkManList($opid=''){
        $map['opid'] = array('eq',$opid);
        $ret = M("user")->where($map)->find();
        $maps['user_code'] = array('eq',$ret['user_code']);
        return M("linkman")->where($maps)->select();
    }
    /**
     * 添加常用联系人
     */
    function linkManInsert($data){
        $ret = M("linkman")->add($data);
        if($ret){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 删除常用联系人
     */
    function linkManDel($id){
        $map['link_id'] = array('eq',$id);
        $ret = M("linkman")->where($map)->delete();
        if($ret){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 获取用户订单
     */
    function memberOrder($opid='',$mark=''){
        $arObj = M("area"); //地区表
        $oiObj = M("order_info");
        $map['opid'] = array('eq',$opid);
        $rets = M("user")->where($map)->find();
        if($mark!=''){
            if($mark=='Y'){
                $maps['order_status'] = array('in',array('Z','Y'));
            }else{
                $maps['order_status'] = array('eq',$mark);
            }
            
        }else{
            $maps['order_status'] = array('not in',array('C','R'));
        }
        $maps['user_code'] = array('eq',$rets['user_code']);
        $ret = M("order")
            //->join("left join tuo_order_info ON tuo_order_info.order_code = tuo_order.order_code")
            ->where($maps)->order("order_time desc")->select();
        $size = count($ret);
        $num = 1;
        for ($i = 0; $i < $size; $i++) {
            $str = '';
            $arr = '';
            $map_1['order_code'] = array('eq',$ret[$i]['order_code']);
            if($ret[$i]['yh_type'] == 'T'){
                $res = $oiObj->field("order_info_star,order_info_end,order_info_brand,order_info_type,order_info_zj")->where($map_1)->select();
                $nums = count($res);
                if($nums>0){
                    for ($j = 0; $j < $nums; $j++) {
                        if($j!=0){
                            if($res[$j-1]['order_info_type'] == $res[$j]['order_info_type']){
                                $num++;
                                $arr[$res[$j]['order_info_brand']." ".$res[$j]['order_info_type']] = $num;
                                //$str .= $res[$j]['order_info_brand']." ".$res[$j]['order_info_type']." X ".$num.",";
                            }else{
                                $num = 1;
                                $arr[$res[$j]['order_info_brand']." ".$res[$j]['order_info_type']] = $num;
                                //$str .= $res[$j]['order_info_brand']." ".$res[$j]['order_info_type']." X ".$num.",";
                            }
                        }else{
                            $arr[$res[$j]['order_info_brand']." ".$res[$j]['order_info_type']] = 1;
                            //$str .= $res[$j]['order_info_brand']." ".$res[$j]['order_info_type']." X 1,";
                        }
                    }
                }
                foreach ($arr as $k=>$v){
                    $str .= $k." X ".$v.",";
                }
                $sheng = explode(",",$res[0]['order_info_star']);
                $zhong = explode(",",$res[0]['order_info_end']);
                if($sheng[0] != ""){
                    $ret[$i]['c_sheng'] = $arObj -> where("area_id=".$sheng[0])->find()['area_name'];
                    $ret[$i]['c_shi'] = $arObj -> where("area_id=".$sheng[1])->find()['area_name'];
                    $ret[$i]['c_qu'] = $arObj -> where("area_id=".$sheng[2])->find()['area_name'];
                }
                if($zhong[0] != ""){
                    $ret[$i]['z_sheng'] = $arObj -> where("area_id=".$zhong[0])->find()['area_name'];
                    $ret[$i]['z_shi'] = $arObj -> where("area_id=".$zhong[1])->find()['area_name'];
                }
                $ret[$i]['chexing'] = rtrim($str,',');
                $ret[$i]['order_info_zj'] = $res[0]['order_info_zj'];
                $ret[$i]['orderStart'] = $res[0]['order_info_star'];
                $ret[$i]['orderEnd'] = $res[0]['order_info_end'];
            }else{
                $res = $oiObj->where($map_1)->find();
                $sheng = explode(",",$res['order_info_star']);
                $zhong = explode(",",$res['order_info_end']);
                if($sheng[0] != ""){
                    $ret[$i]['c_sheng'] = $arObj -> where("area_id=".$sheng[0])->find()['area_name'];
                    $ret[$i]['c_shi'] = $arObj -> where("area_id=".$sheng[1])->find()['area_name'];
                    $ret[$i]['c_qu'] = $arObj -> where("area_id=".$sheng[2])->find()['area_name'];
                }
                if($zhong[0] != ""){
                    $ret[$i]['z_sheng'] = $arObj -> where("area_id=".$zhong[0])->find()['area_name'];
                    $ret[$i]['z_shi'] = $arObj -> where("area_id=".$zhong[1])->find()['area_name'];
                }
                $ret[$i]['chexing'] = $res['order_info_brand']." ".$res['order_info_type'];
                $ret[$i]['order_info_zj'] = $res['order_info_zj'];
                $ret[$i]['orderStart'] = $res['order_info_star'];
                $ret[$i]['orderEnd'] = $res['order_info_end'];
            }
            
            $ret[$i]['count'] =  $oiObj-> where($map_1) -> count();
            
        }
        return $ret;
    }
    /**
     * 查看订单详情
     * @param unknown $orderId
     * @return string
     */
    function Order_Detail($orderId){
        if ($orderId!=""){
            //初始化
            //订单表
            $o_M = M("order");
            //订单详细表
            $oi_M = M("order_info");
            //地图表
            $a_M = M("area");
            //物流表
            $w_M = M("wuliu");
            //优惠券表
            $f_M = M("favorable");
            //运单表
            $y_M = M("yundan");
            //保险单
            $b_M = M("bxd");
    
            //拼装条件
            $map['order_code'] = array('eq',$orderId);
            //查询订单表
            $order = $o_M -> where($map) -> find();
            //查询订单详细表
            $order_info = $oi_M -> where($map) -> select();
            //查询物流表
            $wuliu = $w_M -> where($map) -> find();
            //查询运单表
            $yundan = $y_M -> where($map) -> find();
            //
            $bxd = $b_M -> where($map) -> find();
            //拼装条件
            $map['fav_code'] = array('eq',$order_info[0]['fav_code']);
            //查询优惠表
            $favorable = $f_M -> where($map) -> find();
            
            //判断起始地
            if ($order_info[0]['order_info_star']!="" && $order_info[0]['order_info_star']!=null){
                //获取起始地参数
                $star = explode(",",$order_info[0]['order_info_star']);
                //查询起始地名称
                for($i=0;$i<count($star);$i++){
                    $map['area_id'] = array('eq',$star[$i]);
                    $startList[$i]= implode($a_M -> where($map)->field('area_name')->find());
                    $startList[$i] = $startList[$i].'-';
                }
                $myOrder['start'] = trim(implode($startList),'-');
            }else {
                $myOrder['start'] ="暂无";
            }
            
            //判断结束地
            if ($order_info[0]['order_info_end']!="" && $order_info[0]['order_info_end']!=null){
                //获取结束地参数
                $end = explode(",",$order_info[0]['order_info_end']);
                //查询起始地名称
                for($j=0;$j<count($end);$j++){
                    $map['area_id'] = array('eq',$end[$j]);
                    $endList[$j]= implode($a_M -> where($map)->field('area_name')->find());
                    $endList[$j] = $endList[$j].'-';
                }
                $myOrder['end'] = trim(implode($endList),'-');
            }else {
                $myOrder['end'] ="暂无";
            }
            
            //订单ID
            $myOrder['id']  =   $order['order_id'];
            
            //订单CODE
            $myOrder['order_code'] = $order['order_code'];
            
            //支付状态
            $myOrder['pay_status'] = $order['pay_status'];
            
            //提车人CODE
            $myOrder['c_code']  =   $order['car_code'];
            
            //订单状态
            $myOrder['status']  =  $order['order_status'];
            
            //发车人信息
            if($order_info[0]['order_info_tclxr']!="" && $order_info[0]['order_info_tclxr']!=null){
                $startInfo = explode(",",$order_info[0]['order_info_tclxr']);
                $myOrder['star_name'] = $startInfo[0];
                $myOrder['star_tel'] = $startInfo[2];
            }else {
                $myOrder['star_name'] = "";
                $myOrder['star_tel'] = "";
            }
            
            //发车地址
            $myOrder['star_address'] = $order_info[0]['order_info_star_address'];
            
            //收车人信息
            if($order_info[0]['order_info_sclxr']!="" && $order_info[0]['order_info_sclxr']!=null){
                $end = explode(",",$order_info[0]['order_info_sclxr']);
                $myOrder['end_name'] = $end[0];
                $myOrder['end_tel'] = $end[2];
            }else {
                $myOrder['end_name'] = "";
                $myOrder['end_tel'] = "";
            }
            
            //收车地址
            $myOrder['end_address'] = $order_info[0]['order_info_end_address'];
            
            //循环读取订单信息
            for ($i=0;$i<count($order_info);$i++){
                
                //车型
                $carList[$i]['car_type'] = $order_info[$i]['order_info_type'];
                
                //车品牌
                $carList[$i]['car_name'] = $order_info[$i]['order_info_brand'];
                
                //车价格
                if ($order_info[$i]['order_info_price']!="" && $order_info[$i]['order_info_price']!=null){
                    $carList[$i]['car_price'] = $order_info[$i]['order_info_price']/1000000;
                }
                
                //运输价格
                if ($order_info[$i]['order_info_smsc']!="" && $order_info[$i]['order_info_smsc']!=null){
                    if ($order_info[$i]['order_info_smsc']=="Y"){
                        $sumPrice = $order_info[$i]['order_info_t_car']+$order_info[$i]['order_info_c_car']+$order_info[$i]['order_info_s_car']+$order_info[$i]['order_smsc_car'];
                        $carList[$i]['sumPrice'] = $sumPrice/100;
                    }else{
                        $sumPrice = $order_info[$i]['order_info_t_car']+$order_info[$i]['order_info_c_car']+$order_info[$i]['order_info_s_car'];
                        $carList[$i]['sumPrice'] = $sumPrice/100;
                    }
                }else{
                    $carList[$i]['sumPrice'] = 0;
                }
                
                
                //送车价格
                if ($order_info[$i]['order_info_s_car']!="" && $order_info[$i]['order_info_s_car']!=null){
                    $carList[$i]['sendCar'] = $order_info[$i]['order_info_s_car']/100;
                }else{
                    $carList[$i]['sendCar'] = 0;
                }
                
                
                //优惠券
                if ($favorable['fav_price']>0){
                    $carList[$i]['fav'] = $favorable['fav_price']/100;
                }else {
                    $carList[$i]['fav'] = 0;
                }
                
            }
            
            //订单信息数量
            $myOrder['carList'] = $carList;
            
            //下单时间
            $myOrder['cTime'] = $order['order_time'];
            
            //付款时间
            $myOrder['pTime'] = $order_info[0]['pay_time'];
            
            //收车时间
            $myOrder['sTime'] = $yundan['yd_s_time'];
            
            //物流信息
            $wl_time=null;
            if(count($wuliu)>0){
	            $wl_time = explode(" ",$wuliu['wl_time']);
            }
            
            //替换物流日期
            $weekarray=array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
            $myOrder['wlday'] = $wl_time==null?"":$wl_time[0];
            $myOrder['week'] = $wl_time==null?"":$weekarray[date("w",$wl_time[0])];
            $myOrder['wlTime'] = $wl_time==null?"":$wl_time[1];
            
            //物流信息
            $myOrder['info'] = $wuliu['wl_info'];
            
            //保险单下载
            $myOrder['bxd'] = $bxd['bxd_file']==""||$bxd['bxd_file']==null?"":$bxd['bxd_file'];
            $myOrder['bxd'] = str_replace('/', '_', $myOrder['bxd']);
        }
        //返回结果
        return $myOrder;
    }
    //取消订单&&退款
    function Order_Del($orderId,$type){
        //初始化变量
        $order['result'] = 0;
        //判断类型
        if($type == "C"){
            $info ="取消订单";
        }else if ($type == "R"){
            $info ="退款申请";
        }
        //初始化订单表
        $o_M = M("order");
        //拼装条件
        $map['order_id'] = array('eq',$orderId);
        //查询是否重复
        $selOrder = $o_M -> where($map) -> find();
        if($selOrder['order_status']==$type){
            $order['info'] = $info."已提交过!";
            return $order;
        }
        //组装值
        $data = array(
            'order_status' => $type
        );
        //开始更新状态
        $order['result'] = $o_M -> where($map) -> save($data);
        if($order['result']){
            $order['info'] = $info."提交成功!";
        }else{
            $order['info'] = $info."提交失败!";
        }
        return $order;
    }
    
    //查询列表
    function myOrder_list($tel='',$page=1,$num=5){
        //初始化
        $myOrder = array();
        $o_M = M("order_assistant");
        $area = M("area");
        $poObj = M("position_tracking_record");//位置跟踪
        $bObj = M("bxd");//保险单
        //拼装条件
        $map['user_code'] = array('eq',$tel);
        //计算总数
        $myOrder['number'] = $o_M -> where($map) -> count();
        //计算页数
        //$page = set_page( $myOrder['number'], 8);
        //查询订单表
        $list = $o_M -> where($map) 
        ->field('order_code,order_status,order_info_star,order_info_end,order_info_type,pay_way')
        -> order('order_time desc') -> limit(($page-1)*5,$num) -> select();
        $count = count($list);
        for($i=0;$i<$count;$i++){
            $start = explode(",",$list[$i]['order_info_star'])[1];
            $end = explode(",",$list[$i]['order_info_end'])[1];
            $map1['area_id'] = array("eq",$start);
            $map2['area_id'] = array("eq",$end);
            $objStart = $area->where($map1)->field('area_name')->find();
            $objEnd = $area->where($map2)->field('area_name')->find();
            $list[$i]['start_name'] = $objStart['area_name'];
            $list[$i]['end_name'] = $objEnd['area_name'];
            $map3['order_code'] = array('eq',$list[$i]['order_code']);
            $posit = $poObj->field("time,content") ->where($map3)->order("time desc")->select();
            //$list[$i]['posit']
            foreach ($posit as $k=>$v){
                $list[$i]['posit'][$k]['time'] = date("Y年m月d日",strtotime($v['time']));
                $list[$i]['posit'][$k]['cont'] = $v['content'];
            }
            $list[$i]['bxd'] = $bObj ->where($map3) ->getField("bxd_file");
        }
        if($myOrder['number']-($num*$page) <= 0){
            $myOrder['flag'] = 'N';
        }else{
            $myOrder['flag'] = 'Y';
        }
        $myOrder['list']=$list;
        //分页条
        //$myOrder['show'] = $page->FrontPage();
        //返回结果
        return $myOrder;
    }
    
    function myOrder_info($orderCode=""){
        $o_M = M("order_assistant");
        $area = M("area");
        $area = M("area");
        $faObj = M("favorable");
        $bObj = M("bxd");//保险单
        //拼装条件
        $map['order_code'] = array('eq',$orderCode);
        $orderVo = $o_M -> where($map)->find();
        //出发地，目的地 省市区编号转汉字
        $start = explode(",",$orderVo['order_info_star'])[1];
        $end = explode(",",$orderVo['order_info_end'])[1];
        $map1['area_id'] = array("eq",$start);
        $map2['area_id'] = array("eq",$end);
        $objStart = $area->where($map1)->field('area_name')->find();
        $objEnd = $area->where($map2)->field('area_name')->find();
        $orderVo['start_name'] = $objStart['area_name'];
        $orderVo['end_name'] = $objEnd['area_name'];
        $orderVo['order_smsc_car'] = $orderVo['order_smsc_car']/100;
        $orderVo['car_washing'] = $orderVo['car_washing']/100;
        $orderVo['order_info_zj'] = $orderVo['order_info_zj']/100;
        if($orderVo['fav_code']!=null&&$orderVo['fav_code']!=""){
            $map3['fav_code'] = array("eq",$orderVo['fav_code']);
            $faVo = $faObj->where($map3)->find();
            if($faVo['fav_price']!=""&&$faVo['fav_price']!=null){
                $orderVo['fav_price'] = $faVo['fav_price']/100;
            }
        }
        //$orderVo['bxd'] = $bObj ->where($map) ->getField("bxd_file");
        return $orderVo;
    }
    //查询优惠劵列表
    function mycoupon_list($map='',$page=1,$num=5){
        //初始化
        $sel = M("favorable");
        //获取条数
        $myCoupon['number'] = $sel -> where($map) -> count();
        //计算页数
        //$myCoupon['page'] = set_page($myCoupon['number'],12);
        //查询
        $myCoupon['list'] = $sel -> where($map) -> order('fav_id desc') -> limit(($page-1)*5,$num) -> select();
        //分页条
        //$myCoupon['show'] = $myCoupon['page']->FrontPage();
        if($myCoupon['number']-($num*$page) <= 0){
            $myCoupon['flag'] = 'N';
        }else{
            $myCoupon['flag'] = 'Y';
        }
        //返回结果
        return $myCoupon;
    }
    /**
     * 查询验车信息
     * @param string $code 订单code
     */
    public function verifyCar($code=''){
        $vcObj = M("verify_car_info");
        $vciObj = M("verify_car_img");
        $map['order_code'] = array('eq',$code);
        $ret = $vcObj->where($map)->find();
        $res = $vciObj->field("verify_img_upload")->where($map)->select();
        $ret['imgs'] = $res;
        return $ret;
    }
    /**
     * 微信注销
     * @param string $tel 手机号
     * @param string $opid opid
     */
    public function wxClose($tel,$opid){
        $ret = $this->overBooking($opid);
        $msg['flag'] = false;
        if($ret['flag']){
            $data['opid'] = '';
            $map['opid'] = array('eq',$opid);
            $res = M("user")->where($map)->save($data);
            if($res){
                session("tokenInfo",null);
                session("userInfo",null);
                $msg['flag'] =true;
            }
        }
        return $msg;
    }
}