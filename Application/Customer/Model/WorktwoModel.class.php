<?php
namespace Customer\Model;
use Think\Model;
/**
 * @author lwt
 *
 */
class WorktwoModel extends BaseModel {
    /*
     * 获取省份（妥妥）
     * @author: lwt
     * @return obj区域结果集
     */
    public function get_province(){
        //查询条件
        $map['area_pid'] = array('eq',1);
        //区域查询
        $pro = M('area') -> where($map) -> select();
        return $pro;
    }
    
    /**
     * 获取城市列表
     * @author: lwt
     * @param string $pid 省id
     * @return obj区域结果集
     */
    public function getCity($pid=''){
        //查询条件
        $map['area_pid'] = array('eq',$pid);
        //实例数据库
        $obj = M('area');
        //区域查询
        $list = $obj -> where($map) -> select();
        return $list;
    }
    /**
     * 获取区县列表
     * @author: lwt
     * @param string $pid 市id
     * @return obj区域结果集
     */
    public function getBlock($pid=''){
        //查询条件
        $map['area_pid'] = array('eq',$pid);
        //实例数据库
        $obj = M('area');
        //区域查询
        $list = $obj -> where($map) -> select();
        return $list;
    }
    /**
     * 验证区域是否可以提车
     * @param string 区域$id
     * @return msg状态信息  flag状态码true/false
     */
    public function checkGet($id='') {
        //初始化
        $obj = M('area');
        //查询条件
        $map['area_id'] = array('eq',$id);
        //查询数据
        $ret = $obj->where($map)->getField("area_get");
        if($ret=="Y"){
            $msg['flag'] = true;
        }else{
            $msg['flag'] = false;
            $msg['msg'] = "此区域暂不可提车";
        }
        return $msg;
    }
    /**
     * 验证区域是否可以送车
     * @param string 区域$id
     * @return msg状态信息  flag状态码true/false
     */
    public function checkSet($id='') {
        //初始化
        $obj = M('area');
        //查询条件
        $map['area_id'] = array('eq',$id);
        //查询数据
        $ret = $obj->where($map)->getField("area_set");
        if($ret=="Y"){
            $msg['flag'] = true;
        }else{
            $msg['flag'] = false;
            $msg['msg'] = "此区域暂不可送车";
        }
        return $msg;
    }
    /**
     * 判断路线类型
     * @param string $str起始点
     * @param string $end目的地
     * @return msg状态信息  flag状态码true/false product_type类型A:直发,B:常规
     */
    public function queryLine($str='',$end='') {
        //初始化
        $msg['flag'] = true;
        //查询集散地A
        $ti = $this->getTi($str);
        //查询直发路线
        $tl = $this->queryPrice($ti['ti_end'],$end);
        //查询集散地B
        $song = $this->getSong($end);
        //查询集散地
        $ls = $this->getSan($ti['ti_end'],$song['song_star']);
        //判断提车是否存在
        if($ti){
            //判断是直发还是常规
            if($tl){
                //直发
                $msg['product_type'] = "A";
            }else{
                //常规
                if($song && $ls){
                    $msg['product_type'] = "B";
                }else{
                    $msg['product_type'] = "B";
                    //$msg['msg'] = "线路正在完善中";
                    $msg['flag'] = false;
                }
            }
        }else{
            //不存在
            $msg['product_type'] = "B";
            //$msg['msg'] = "线路正在完善中";
            $msg['flag'] = false;
        }
        return $msg;
    }
    /**
     * 查询直发
     * @param string $start 开始地点
     * @param string $end   结束地点
     * @return obj数据结果集
     */
    public function queryPrice($start='',$end=''){
        //查询条件
        $map['line_star'] = array('eq',$start);
        $map['line_end'] = array('eq',$end);
        //实例化数据库
        $obj = M('line');
        //查询
        $data = $obj -> where($map) -> find();
        return $data;
    }
    /**
     * 获取车辆型号
     * @param string $id车id
     * @return obj数据结果集
     */
    public function getType($id=''){
        //查询条件
        $map['cxjj_id'] = array('eq',$id);
        //实例化数据库
        $obj = M('carxing');
        //查询
        $list = $obj -> where($map) -> find();
        return $list;
    }
    /**
     * 计算保险费用
     * @param string $price 车费
     * @param string $type 类型
     * @return number
     */
    public function getSecu($price='',$type=''){
        //实例化数据库
        $obj = M('system');
        //查询
        $list = $obj->where("id=1") -> find();
        $per = $list[$type];
        $fee = ceil($price*$per/100);
        return $fee;
    }
    /**
     * 查询提车信息
     * @param string $start 开始地code串
     * @return obj数据结果集
     */
    public function getTi($start=''){
        //实例化数据库
        $obj = M('ti');
        $info="";
        if($start!=""){
            $map['ti_star'] = array('eq',$start);
            //查询
            $info = $obj -> where($map) -> find();
            //拆出省份id
            if(!empty($info['ti_id'])){
                $arr = explode(',',$info['ti_end']);
                $info['spro'] = $arr[0];
            }
        }
        return $info;
    }
    /**
     * 获取送车信息
     * @param string $end 终点地code串
     * @return obj数据结果集
     */
    public function getSong($end=''){
        //实例化数据库
        $obj = M('song');
        $map['song_end'] = array('eq',$end);
        //查询
        $info = $obj -> where($map) -> find();
        if(!empty($info['song_id'])){
            //拆出省份id
            $arr = explode(',',$info['song_star']);
            $info['epro'] = $arr[0];
        }
        return $info;
    }
    /**
     * 获取集散地之间运输信息
     * @param string $st 集散地开始
     * @param string $end   集散地结束
     * @return obj数据结果集
     */
    public function getSan($st='',$end=''){
        //实例化数据库
        $obj = M('line_san');
        $map['san_star'] = array('eq',$st);
        $map['san_end'] = array('eq',$end);
        //查询
        $info = $obj -> where($map) -> find();
        return $info;
    }
    /**
     * 查询优惠券金额
     * @param string $code 优惠券code
     * @return flag状态true/false price金额
     */
    public function getUserConpon($code=''){
        //实例化数据库
        $obj = M('favorable');
        $now = date('Y-m-d H:i:s',time());
        $session_User = jiema(session('userData'));
        //查询条件
        $map['user_code'] = array('eq',$session_User['tel']);
        $map['fav_code'] = array('eq',$code);
        $map['fav_status'] = array('eq','N');
        $map['fav_flag'] = array('eq','Y');
        $map['fav_startime'] = array('elt',$now);
        $map['fav_endtime'] = array('gt',$now);
        //查询
        $list = $obj -> where($map) -> find();
        if($list){
            $map['fav_startime'] = array('eq','0000-00-00 00:00:00');
            $map['fav_endtime'] = array('eq','0000-00-00 00:00:00');
            $list = $obj -> where($map) -> find();
        }
        if($list){
            $msg['flag'] = true;
            $msg['price'] = $list['fav_price']/100;
        }else{
            $msg['flag'] = false;
        }
        return $msg;
    }
    /**
     * 计算毛利信息
     * @param string $spro 起始地省id
     * @param string $epro  终点地省id
     * @param string $carid  车id
     * @return number金额
     */
    public function getMaoli($spro='',$epro='',$carid=''){
        //实例化数据库
        $obj = M('maoli');
        //获取车信息
        $car = $this->getType($carid);
        $map['ml_star'] = array('eq',$spro);
        $map['ml_end'] = array('eq',$epro);
        //查询
        $info = $obj -> where($map) -> find();
        //获取毛利
        $data = $info[$car['cxjj_category']];
        return $data;
    }
    /**
     * 获取上门送车费
     * @param string $s 起始地区
     * @param string $e 结束结束
     * @return obj数据结果集
     */
    function getsmPrice($s='',$e=''){
        //实例化数据库
        $smObj = M("sm");
        $map['sm_star'] = array('eq',$s);
        $map['sm_end'] = array('eq',$e);
        //查询
        $info = $smObj->where($map)->find();
        return $info;
    }
    /**
     * 将地名code转换成名称
     * @date: 2016-9-27 下午7:47:20
     * @author: liuxin
     * @param string $id
     * @return 地区名称
     */
    public function getPlaceName($id=''){
        //查询
        $map['area_id'] = array('eq',$id);
        $data = M('area') -> where($map) -> find();
        //返回地区名称
        return $data['area_name'];
    }
    /**
     * 下单第一步，判断&&价格
     * @param string $str 出发地
     * @param string $end 目的地
     * @param string $carid 车型id
     */
    public function getPrice($str='',$end='',$carid=''){
        //判断区域是否可提车
        /* $get = $this->checkGet(explode(",",$str)[2]);
        if(!$get['flag']){
            return $get;
        }
        //判断目的地是否可以送车
        $set = $this->checkGet(explode(",",$end)[1]);
        if(!$set['flag']){
            return $set;
        } */
        //判断线路类型
        $lineType = $this->queryLine($str,$end);
        /* if(!$lineType['flag']){
            return $lineType;
        } */
        //计算提车费
        $ti = $this->getTi($str);
        //计算毛利
        $mao = $this->getMaoli(explode(',',$str)[0],explode(',',$end)[0],$carid);
        //拼装中文地区
        $lineType['str_add'] = $this->getPlaceName(explode(',',$str)[0])."-".$this->getPlaceName(explode(',',$str)[1])."-".$this->getPlaceName(explode(',',$str)[2]);
        $lineType['end_add'] = $this->getPlaceName(explode(',',$end)[0])."-".$this->getPlaceName(explode(',',$end)[1]);
        $lineType['ends'] = $this->getPlaceName(explode(',',$end)[1]);//目的地地区
        //判断直发还是常规送车
        if($lineType['product_type'] == 'A'){
            //直发
            $line = $this->queryPrice($ti['ti_end'],$end);
            $lineType['ti'] = $ti['ti_platelets_price']/100;
            $lineType['line'] = $line['line_price']/100;
            $lineType['mao'] = $mao/100;
            $lineType['totel'] = ($ti['ti_platelets_price']+$line['line_price']+$mao)/100;
        }else{
            //常规
            //送车费
            $song = $this->getSong($end);
            //集散地
            $ji = $this->getSan($ti['ti_end'],$song['song_star']);
            $lineType['ti'] = $ti['ti_platelets_price']/100;
            $lineType['line'] = $ji['san_price']/100;
            $lineType['mao'] = $mao/100;
            $lineType['song'] = $song['song_platelets_price']/100;
            $lineType['totel'] = $lineType['ti']+$lineType['line']+$lineType['mao']+$lineType['song'];
        }
        return $lineType;
    }
    /**
     * 客服下单生成订单
     * @param array $data 订单表数据
     * @param array $data_m 用户表数据
     */
    public function setOrder($data=''){
        $model = new Model();//开启事务
        $model -> startTrans();
        $obj = M("order");//订单表
        $oabj = M("order_assistant");//订单表
        $faObj = M("favorable");//优惠劵表
        
        $ret = $obj->add($data);
        $red = $oabj->add($data);
        if($data['fav_code'] !=""){
            $datas['fav_flag'] = "N";
            $datas['fav_status'] = "Y";
            $maps['fav_code'] = array('eq',$data['fav_code']);
            $res = $faObj->where($maps)->save($datas);
        }else{
            $res = true;
        }
        
        if($ret && $res && $red){
            $model->commit();
            return true;
        }else{
            $model->rollback();
            return false;
        }
        
    }
    /**
     * 查询订单信息
     */
    function getOrderInfo($code=''){
        $obj = M("order");
        $map['order_code'] = array('eq',$code);
        $info = $obj->where($map)->find();
        $info['str'] = $this->getPlaceName(explode(',',$info['order_info_star'])[1]);//目的地地区
        $info['end'] = $this->getPlaceName(explode(',',$info['order_info_end'])[1]);//目的地地区
        return $info;
    }
}