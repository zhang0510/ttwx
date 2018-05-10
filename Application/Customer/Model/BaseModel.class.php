<?php
namespace Customer\Model;
use Think\Model;
/**
 * Model基类
 * @author Administrator
 *
 */
class BaseModel extends Model {
    Protected $autoCheckFields = false;
    /**
     * 生成 订单，运单，提车单，财务  单号
     * 054+00001//订单
     * 036+00001//运单
     * 012+00001//提车单
     * 097+00001//财务
	 * 055+00001//订单明细code
     * 订单/运单/提车单/财务/订单明细(D/Y/T/C/M)
     */
    function getTablecode($codemark="D"){
    	$tcObj = M("tablecode");
		if($codemark=="D"){
			$obj = M("order");//订单表
			$orderTouMap="1=1 AND source='B'";
			$listTou = $obj->where($orderTouMap)->order('order_id desc')->select();
			$order_code = $listTou[0]['order_code'];//头表code
			$order_code_linshi = $order_code + 1;
			$headCode = "0".$order_code_linshi;
			return $headCode;
		}else if($codemark=="M"){
			$oiObj = M("order_info");//明细表
			$oiMap = "1=1";
			$oiList = $oiObj->where($oiMap)->order('order_info_id desc')->select();
			$order_info_code = $oiList[0]['order_info_code'];;//头表code
			$order_info_code_linshi = $order_info_code + 1;
			$infocode = "0".$order_info_code_linshi;
			return $infocode;
		}else if($codemark=="Y"){//运单
			$oiObj = M("yundan");//明细表
			$oiMap = "1=1";
			$oiList = $oiObj->where($oiMap)->order('yd_id desc')->select();
			$order_info_code = $oiList[0]['yd_code'];;//头表code
			$order_info_code_linshi = $order_info_code + 1;
			$infocode = "0".$order_info_code_linshi;
			return $infocode;
		}else if($codemark=="T"){//提车
			$oiObj = M("car_order");//明细表
			$oiMap = "1=1";
			$oiList = $oiObj->where($oiMap)->order('car_id desc')->select();
			$order_info_code = $oiList[0]['car_order_code'];;//头表code
			$order_info_code_linshi = $order_info_code + 1;
			$infocode = "0".$order_info_code_linshi;
			return $infocode;
		}else if($codemark=="C"){//财务
			$oiObj = M("car_order");//明细表
			$oiMap = "1=1";
			$oiList = $oiObj->where($oiMap)->order('car_id desc')->select();
			$order_info_code = $oiList[0]['car_order_code'];;//头表code
			$order_info_code_linshi = $order_info_code + 1;
			$infocode = "0".$order_info_code_linshi;
			return $infocode;
		}else{
			$map['codemark'] = array("eq",$codemark);
			$ret = $tcObj->where($map)->find();
			$code = $ret['code'];
			$data['code'] = $code+1;
			$tablecode = $this->stringJoint($data['code'],$codemark);
			if($tablecode!=""){
				$tcObj->where($map)->save($data);
				return $tablecode;
			}else{
				return 'N';
			}
		}
    }
    /**
     * 字符串拼接
     */
    function stringJoint($code='',$codemark=''){
	    	$len = strlen($code);
	    	$str="";
	    	switch($len){
	    		case 1;
	    		$str = '0000'.$code;
	    		break;
	    		case 2;
	    		$str = '000'.$code;
	    		break;
	    		case 3;
	    		$str = '00'.$code;
	    		break;
	    		case 4;
	    		$str = '0'.$code;
	    		break;
	    		case 5;
	    		$str = $code;
	    		break;
	    		default:
	    			$str = 'N';
	    	}
	    
    	if($codemark=="D"&&$str!="N"){
    		return '054'.$str;
    	}else if($codemark=="Y"&&$str!="N"){
    		return '036'.$str;
    	}else if($codemark=="T"&&$str!="N"){
    		return '012'.$str;
    	}else if($codemark=="C"&&$str!="N"){
    		return '097'.$str;
    	}else if($codemark=="M"&&$str!="N"){
    		return '055'.$str;
    	}else{
    		return "";
    	}
    }
}