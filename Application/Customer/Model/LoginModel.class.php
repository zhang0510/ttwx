<?php
namespace Customer\Model;
class LoginModel extends BaseModel{
    
	function readUserInfo($openid=""){
		$userObj = M("user");
		if($openid!=""){
			$userMap['opid'] = array("eq",$openid);
			$ret = $userObj->where($userMap)->find();
			return $ret;
		}else{
			return array();
		}
	}
	
	function userInfoAdd($openid="",$tel=""){
		$userObj = M("user");
		$data['opid'] = $openid;
		$data['tel'] = $tel;
		$data['user_code'] = get_code("TU");
		$ret = $userObj->add($data);
		if($ret){
			$map['opid'] = array("eq",$openid);
			return $userObj->where($map)->find();
		}else{
			return array();
		}
	}
	
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
	 * 检测手机号是否已经存在
	 * @param string $tel
	 * @return boolean
	 */
	function loginAdmin($tel=""){
		$userObj = M("user");
		$map['tel'] = array("eq",$tel);
		$vo = $userObj->where($map)->find();
		if($vo){
			return true;
		}else{
			return false;
		}
	}
    
}