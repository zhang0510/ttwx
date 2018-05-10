<?php
namespace Customer\Model;
class TransferInfoModel extends BaseModel{
    public function showInfo($o_code){
        //初始化
        $tInfo = M('verify_car_info');
        $tImg = M('verify_car_img');
        //拼装条件
        $map['order_code'] = array('eq',$o_code);
        //查询信息表
        $t_Info = $tInfo -> where($map) -> find();
        //查询图片表
        $t_Img = $tImg -> where($map) -> order('verify_img_type asc') -> select();
	//	var_dump($t_Img);
	//	exit;
        //拼装字段
        if($t_Info==""){
            $t_Info['verify_car_keys']=0;
            $t_Info['verify_spare_tire']=0;
            $t_Info['verify_tool_kit']=0;
            $t_Info['verify_lifting_jack']=0;
            $t_Info['verify_door_mat']=0;
            $t_Info['verify_km']=0;
            $t_Info['verify_instructions']=0;
            $t_Info['verify_warning_sign']=0;
            $t_Info['verify_lighter']=0;
            $t_Info['verify_fire_extinguisher']=0;
        }
        
        if($t_Img==""){
            for ($i=0;$i<7;$i++){
                $t_Img[$i]['verify_img_id']="暂无";
                $t_Img[$i]['verify_img_describe']="暂无";
                $t_Img[$i]['verify_img_upload']="";
            }
        }
        if($t_Img[7]['verify_img_upload']!=''){
            $t_Img['guaca'] = explode(';',$t_Img[7]['verify_img_upload']);
        }
        $t_Res['t_Info'] = $t_Info;
        $t_Res['t_Img'] = $t_Img;
        $t_Res['img_count'] = count($t_Img);
        return $t_Res;
    }
}