<?php
namespace Customer\Model;
class IndexModel extends BaseModel{
    /**
     * 获取城市列表
     * @date: 2016-9-4 下午4:00:06
     * @author: liuxin
     * @param string $pid 省id
     * @return Ambigous <\Think\mixed, boolean, string, NULL, mixed, multitype:, unknown, object>
     */
    public function getCity($pid=''){
        $map['area_pid'] = array('eq',$pid);
        $obj = M('area');
        $list = $obj -> where($map) -> select();
        return $list;
    }
    /**
     * 获取市区列表
     * @date: 2016-9-4 下午4:00:06
     * @author: liuxin
     * @param string $pid 市id
     * @return Ambigous <\Think\mixed, boolean, string, NULL, mixed, multitype:, unknown, object>
     */
    public function getBlock($pid=''){
        $map['area_pid'] = array('eq',$pid);
        $obj = M('area');
        $list = $obj -> where($map) -> select();
        return $list;
    }
    /**
     * 查询运价
     * @date: 2016-9-4 下午6:10:07
     * @author: liuxin
     * @param string $start 开始地点
     * @param string $end   结束地点
     * @return Ambigous <\Think\mixed, boolean, string, NULL, mixed, multitype:, unknown, object>
     */
    public function queryPrice($start='',$end=''){
        $map['line_star'] = array('eq',$start);
        $map['line_end'] = array('eq',$end);
        $obj = M('line');
        $list = $obj -> where($map) -> select();
        return $list;
    }
    /**
     * 获取车辆型号
     * @date: 2016-9-5 上午10:31:21
     * @author: liuxin
     * @param string $pid
     * @return Ambigous <\Think\mixed, boolean, string, NULL, mixed, multitype:, unknown, object>
     */
    public function getType($pid=''){
        $map['cxjj_pid'] = array('eq',$pid);
        $obj = M('carxing');
        $list = $obj -> where($map) -> select();
        return $list;
    }
    /**
     * 计算保险费用
     * @date: 2016-9-5 上午11:57:57
     * @author: liuxin
     * @param string $price 车费
     * @return number
     */
    public function getSecu($price=''){
        $obj = M('system');
        $list = $obj -> select();
        $per = $list[0]['acale'];
        $fee = ceil($price*$per/100);
        return $fee;
    }
    /**
     * 查询提车信息
     * @date: 2016-9-7 上午8:44:39
     * @author: liuxin
     * @param string $start 开始地code串
     * @return Ambigous <\Think\mixed, boolean, NULL, multitype:, mixed, unknown, string, object>
     */
    public function getTi($start=''){
        $obj = M('ti');
        $map['ti_star'] = array('eq',$start);
        $info = $obj -> where($map) -> find();
        if(!empty($info['ti_id'])){
            $arr = explode(',',$info['ti_end']);
            $info['spro'] = $arr[0];
        }
        return $info;
    }
    /**
     * 获取送车信息
     * @date: 2016-9-7 上午8:52:57
     * @author: liuxin
     * @param string $end 终点地code串
     * @return Ambigous <\Think\mixed, boolean, NULL, multitype:, mixed, unknown, string, object>
     */
    public function getSong($end=''){
        $obj = M('song');
        $map['song_end'] = array('eq',$end);
        $info = $obj -> where($map) -> find();
        if(!empty($info['song_id'])){
            $arr = explode(',',$info['song_star']);
            $info['epro'] = $arr[0];
        }
        return $info;
    }
    /**
     * 获取集散地之间运输信息
     * @date: 2016-9-7 上午8:55:12
     * @author: liuxin
     * @param string $st 集散地开始
     * @param string $end   集散地结束
     * @return Ambigous <\Think\mixed, boolean, NULL, multitype:, mixed, unknown, string, object>
     */
    public function getSan($st='',$end=''){
        $obj = M('line_san');
        $map['san_star'] = array('eq',$st);
        $map['san_end'] = array('eq',$end);
        $info = $obj -> where($map) -> find();
        return $info;
    }
    /**
     * 查询用户可用的优惠券
     * @date: 2016-9-7 上午8:55:58
     * @author: liuxin
     * @param string $usercode 用户code
     * @param string $tid   起始地code串
     * @param string $sid   终点地code串
     * @param string $sans  集散地起始地code串
     * @param string $sane  集散地终点地code串
     * @return Ambigous <\Think\mixed, boolean, string, NULL, mixed, multitype:, unknown, object>
     */
    public function getUserConpon($usercode='',$tid='',$sid='',$sans='',$sane=''){
        $obj = M('favorable');
        $now = date('Y-m-d H:i:s',time());
        
        $map['user_code'] = array('eq',$usercode);
        $map['fav_status'] = array('eq','N');
        $map['fav_flag'] = array('eq','Y');
        $map['fav_left'] = array('neq',0);
        $map['fav_startime'] = array('elt',$now);
        $map['fav_endtime'] = array('gt',$now);
        
        $map['_string'] = '(fav_type = "C" AND fav_star = "'.$tid.'") 
            OR (fav_type = "M" AND fav_end = "'.$sid.'") 
                OR (fav_type = "J" AND fav_star = "'.$sans.'" AND fav_end = "'.$sane.'") 
                    OR (fav_type = "W")'; 
        $list = $obj -> where($map) -> select();
        $data['list'] = $list;
        $data['cnum'] = count($list);
        return $data;
    }
    /**
     * 计算毛利信息
     * @date: 2016-9-7 上午8:57:30
     * @author: liuxin
     * @param string $spro 起始地省id
     * @param string $epro  终点地省id
     * @return unknown
     */
    public function getMaoli($spro='',$epro=''){
        $obj = M('maoli');
        $map['ml_star'] = array('eq',$spro);
        $map['ml_end'] = array('eq',$epro);
        $info = $obj -> where($map) -> find();
        $data = $info['ml_price'];
        return $data;
    }
}