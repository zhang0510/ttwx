<?php
namespace Customer\Model;

class WorklwtSDModel extends BaseModel {
    /**
     * 优惠列表
     */
    function lineDisList(){
        $obj = M("line");
        
        //$inObj = D("Index");
        //$inObj->getTi();
        $map['line_status'] = array('eq','Y');
        $size = $obj->where($map) ->count();
        $page = set_page($size,12);
        $list = $obj->where($map)->order("line_id asc")->limit($page->limit)->select();
        $show = $page->FrontPage();
        $num = count($list);
        if($num>0){
            $data['show'] = $show;
        }
        $city = M("area");
        for ($i = 0; $i < $num; $i++) {
            /* $star = explode(",",$list[$i]['line_star']);
            $end = explode(",",$list[$i]['line_end']);
            $map_s['area_id'] = array('eq',$star[0]);
            $map_e['area_id'] = array('eq',$end[0]);
            $s_area = $city->where($map_s)->find();
            $e_area = $city->where($map_e)->find(); */
            $list[$i]['start_city'] = $this -> getAreaName($list[$i]['line_star']);
            $list[$i]['end_city'] = $this -> getAreaName($list[$i]['line_end']);
            //dump($inObj->getTi($list[$i]['line_star']));
            //$ss = $inObj->getTi($list[$i]['line_star']);
            $list[$i]['price'] = $this->getPrice($list[$i]['line_star'],$list[$i]['line_end']);
        }
        $data['list'] = $list;
        return $data;
    }
    /**
     * 优惠路线广告
     */
    function advLine($type=''){
        $aiObj = M("adv_img");
        $map['adv_code'] = array('eq',$type);
        $ret = $aiObj->where($map)->find();
        return $ret;
    }
    /**
     * 获取集散地 提车 送车费
     */
    function getPrice($start='',$end=''){
        $obj_1 = M('ti');
        $map_1['ti_star'] = array('eq',$start);
        $info_1 = $obj_1 -> where($map_1) -> find();
        $obj_2 = M('song');
        $map_2['song_end'] = array('eq',$end);
        $info_2 = $obj_2 -> where($map_2) -> find();
        $obj_3 = M('line_san');
        $map_3['san_star'] = array('eq',$info_1['ti_end']);
        $map_3['san_end'] = array('eq',$info_2['song_star']);
        $info_3 = $obj_3 -> where($map_3) -> find();
        
        $obj_4 = M('maoli');
        $map_4['ml_star'] = array('eq',explode(',',$info_1['ti_end'])[0]);
        $map_4['ml_end'] = array('eq',explode(',',$info_2['song_star'])[0]);
        $info_4 = $obj_4 -> where($map_4) -> find();
        
        //司机
        $data['san_price'] = $info_3['san_price']/100;
        $data['ti_price'] = $info_1['ti_platelets_price']/100;
        $data['so_price'] = $info_2['song_platelets_price']/100;
        //小板车
        //$data['san_price'] = $info_3['san_price']/100;
        $data['ti_driver_price'] = $info_1['ti_driver_price']/100;
        $data['song_driver_price'] = $info_2['song_driver_price']/100;
        
        $maoli = $info_4['ml_price']/100; 
        $data['maoli'] = $maoli;
        $data['totle'] = $data['san_price']+$data['ti_price']+$data['so_price'];
        $data['fuc'] = $data['san_price']+$data['ti_price']+$data['so_price']+$maoli;
        $data['fuc_xiaoban'] = $data['ti_driver_price']+$data['song_driver_price']+$data['san_price']+$maoli;
        return $data;
    }
    /**
     * 验证会员是否登录
     */
    function checkLogin(){
        $accs = jiema(session('userData'));
        $msg['flag'] = false;
        $msg['kk'] = 'N';
        //dump($accs);
        if($accs){
            $msg['flag'] = true;
        }else{
            $msg['msg'] = '请先登录';
            $msg['kk'] = 'Y';
        }
        return $msg;
    }
    /**
     * 优惠路线第一步
     * @param 路线code
     * @param 车型加价
     */
    function lineDis($code='',$erice=''){
        $obj = M("line");
        $map['line_status'] = array('eq','Y');
        $map['line_code'] = array('eq',$code);
        $list = $obj->where($map)->find();
        $city = M("area");
        /* $star = explode(",",$list['line_star']);
        $end = explode(",",$list['line_end']);
        $map_s['area_id'] = array('eq',$star[0]);
        $map_e['area_id'] = array('eq',$end[0]);
        $s_area = $city->where($map_s)->find();
        $e_area = $city->where($map_e)->find(); */
        $list['start_city'] = $this -> getAreaName($list['line_star']);
        $list['end_city'] = $this -> getAreaName($list['line_end']);
        $list['price'] = $this->getPrice($list['line_star'],$list['line_end']);
        if($erice!=""){
            $list['price']['order_info_add_price'] = $erice;
        }
        $list['price']['totle'] = $list['price']['totle']+$erice;
        //dump($list);
        return $list;
    }
    
    /**
     * 优惠路线第一步
     * @param 路线code
     * @param 车型加价
     */
    function lineDis_f($start_cityarea='',$end_cityarea='',$erice=''){
//     	$obj = M("line");
//     	$map['line_status'] = array('eq','Y');
//     	$map['line_code'] = array('eq',$code);
//     	$list = $obj->where($map)->find();
//     	$city = M("area");
    	/* $star = explode(",",$list['line_star']);
    	 $end = explode(",",$list['line_end']);
    	 $map_s['area_id'] = array('eq',$star[0]);
    	 $map_e['area_id'] = array('eq',$end[0]);
    	 $s_area = $city->where($map_s)->find();
    	$e_area = $city->where($map_e)->find(); */
    	$list = array();
    	$list['start_city'] = $this -> getAreaName($start_cityarea);
    	$list['end_city'] = $this -> getAreaName($end_cityarea);
    	$list['price'] = $this->getPrice($start_cityarea,$end_cityarea);
    	if($erice!=""){
    		$list['price']['order_info_add_price'] = $erice;
    	}
    	$list['price']['totle'] = $list['price']['totle']+$erice;
    	//dump($list);
    	return $list;
    }
    
    /**
     * 获取保险比例
     */
    function getBaoxian(){
        $syObj = M("system");//保险
        $bao = $syObj ->where('id=1')->find();//保险
        return $bao['acale'];
    }
    /**
     * 获取提车费
     * @param string $start
     * @param string $type
     * @return Ambigous <\Think\mixed, boolean, NULL, multitype:, mixed, unknown, string, object>
     */
    public function getTi($start='',$type=''){
        $obj = M('ti');
        $map['ti_star'] = array('eq',$start);
        $info = $obj -> where($map) -> find();
        if($type == 'S'){
            $data = $info['ti_platelets_price']/100;
        }else{
            $data = $info['ti_driver_price']/100;
        }
        return $data;
    }
    /**
     * 获取送车费
     * @param string $end
     * @param string $type
     * @return Ambigous <\Think\mixed, boolean, NULL, multitype:, mixed, unknown, string, object>
     */
    public function getSong($end='',$type=''){
        $obj = M('song');
        $map['song_end'] = array('eq',$end);
        $info = $obj -> where($map) -> find();
        if($type == 'S'){
            $data = $info['song_platelets_price']/100;
        }else{
            $data = $info['song_driver_price']/100;
        }
        return $data;
    }
    /**
     * 计算价格
     */
    function getPrce(){
        $accs = jiema(session("userData"));
        $data = S($accs['user_code'].'AK');
        if($data['toi']['fav_code']!=""){
            $faObj = M("favorable");
            $map['fav_code'] = array('eq',$data['toi']['fav_code']);
            $info = $faObj->where($map)->find();
        }
        $data['toi']['order_info_zj'] = $data['toi']['order_info_c_car']+
                                        $data['toi']['order_info_t_car']+
                                        $data['toi']['order_info_s_car']+
                                        $data['toi']['order_smsc_car']+
                                        $data['toi']['order_info_margin']+
                                        $data['toi']['order_info_add_price']+
                                        $data['toi']['order_info_bxd']-
                                        $data['kks']['dis'];
        
        /* S($accs['user_code'].'AK',null);
        S($accs['user_code'].'AK',$data,2*3600); */
        $msg['price_s'] = $data['toi']['order_info_zj'];
        $msg['pfs'] = $data['toi']['order_smsc_car']==""?0:$data['toi']['order_smsc_car'];//送车上门价
        
        $msg['total'] = $data['toi']['order_info_zj']+$data['kks']['dis'];//原总价
        $data['kks']['total'] = $msg['total'];
        $msg['price'] = $data['toi']['order_info_zj'] - $info['fav_price']/100;
        $data['toi']['order_info_zj'] = $msg['price'];
        $msg['bx'] = $data['toi']['order_info_bxd']==""?0:$data['toi']['order_info_bxd'];
        
        $msg['tprice'] = $data['toi']['order_info_c_car']+
                         $data['toi']['order_info_t_car']+
                         $data['toi']['order_info_s_car']+
                         $data['toi']['order_info_margin']+
                         $data['toi']['order_info_add_price']-
                                        $data['kks']['dis'];//总运价
        $data['kks']['tprice'] = $msg['tprice'];
        S($accs['user_code'].'AK',null);
        S($accs['user_code'].'AK',$data,2*3600);
        return $msg;
    }
    /**
     * 获取上门送车费
     */
    function getsmPrice($s='',$e='',$type=''){
        $smObj = M("sm");
        $map['sm_star'] = array('eq',$s);
        $map['sm_end'] = array('eq',$e);
        $info = $smObj->where($map)->find();
        if($type=='S'){
            $data = $info['sm_platelets_price']/100;
        }else{
            $data = $info['sm_driver_price']/100;
        }
        return $data;
    }
    /**
     * 获取毛利
     */
    function getmlPrice($s='',$e=''){
        $smObj = M("maoli");
        $map['ml_star'] = array('eq',$s);
        $map['ml_end'] = array('eq',$e);
        $info = $smObj->where($map)->find();
        return $info['ml_price']/100;
    }
    /**
     * 获取保险费比例
     */
    function getBxBl(){
        $ssObj = M("system");
        return $ssObj->where("id=1")->find()['acale'];
    }
    /**
     * 获取优惠劵价额
     */
    function getyxPrice($code=''){
        $faObj = M("favorable");
        $map['fav_code'] = array('eq',$code);
        return $faObj->where($map)->find()['fav_price']/100;
    }
    /**
     * 存入订单
     */
    function setOrders($data=''){
        $okObj = M("order");
        $oiObj = M("order_info");
        $map['order_code'] = array('eq',$data['to']['order_code']);
        $res = $okObj->where($map)->find();
        //$res = $okObj -> add($data['to']);
        if(!$res){
            $ret = $okObj->add($data['to']);
            if($ret){
                $data['toi']['order_id'] = $ret;
                $oiObj->add($data['toi']);
            }
            //dump($ret);
            return $ret;
        }else{
            return false;
        }
     }
     public function getAreaName($str=''){
         $arr = explode(',',$str);
         $num = count($arr);
         $name = '';
         $objss = M('area');
         for($i=0;$i<$num;$i++){
             $mapsa['area_id'] = array('eq',$arr[$i]);
             $info = $objss -> where($mapsa) -> find();
             $name.='-'.$info['area_name'];
         }
         $name = trim($name,'-');
         return $name;
     }
        
}