<?php
namespace Customer\Model;

class PdfPrintModel extends BaseModel{
    //查询PDF数据
    public function selOrder($order_code){

        //初始化数据表
        $order_M = M('order_assistant');
        $yunObj = M('yundan');
        //写入条件
        $map['order_code'] = array('eq',$order_code);

        //查询订单信息表
        $order = $order_M -> where($map) -> find();
        $mapc['order_code'] = array('eq',$order_code);
        $mapc['yd_type'] = array('eq','A');
        $ti = $yunObj ->where($mapc)->find();
        //组装数据
        //提车人信息
        if($ti){
            //$result['tclxr'] = explode(",",$orderInfo['order_info_tclxr']);
            $result['tclxr'][0] =$ti['yd_name'];
            $result['tclxr'][1] =$ti['yd_indetity'];
            $result['tclxr'][2] =$ti['yd_tel'];
            $times = explode(" ",$ti['yd_time']);
            $result['car_time'] = explode("-",$times[0]);
        }else {
            $result['tclxr'][0] ="无";
            $result['tclxr'][1] ="无";
            $result['tclxr'][2] ="无";
            $result['car_time'][0] ="";
            $result['car_time'][1] ="";
            $result['car_time'][2] ="";
        }

        //发车人信息
        if($order['order_info_tclxr']!="" && $order['order_info_tclxr']!=null){
            $sclxr = explode(",",$order['order_info_tclxr']);
            $result['sclxr'] = $sclxr[0];
        }else {
            $result['sclxr'] ="无";
        }

        //当前时间
        $result['time'] = explode("-",date("Y-m-d"));

        //返回数据
        return $result;

    }

    public function pdf_Order($order_code){
        //初始化数据表.. 一堆表..嗯..
        $order_M = M('order_assistant');
        //$orderInfo_M = M('order_info');
        $carInfo_M = M('verify_car_info');
        $carImg_M = M('verify_car_img');
        $area_M = M('area');
        $yunda_M = M('yundan');
        $carOrder_M = M('car_order');

        //写入条件
        $map['order_code'] = array('eq',$order_code);

        //查询订单表
        $order_way = $order_M ->where($map)->find();
        $order = $order_M -> where($map) -> find();
        //查询订单信息表
        $orderInfo = $order_M ->where($map)->find();
        //
        $carOrder = $carOrder_M ->where($map)->find();
        //查询验车服务信息表
        $carInfo = $carInfo_M ->where($map)->find();

        //查询验车服务图片表
        $carImg = $carImg_M->field("verify_img_code") ->where($map)-> select();
        //查询运单表
        $yundan = $yunda_M ->where($map)->find();
        //接车方式
        if ($order_way['order_way']=="S"){
            $result['order_way']="司机接车";
        }else if ($order_way['order_way']=="X"){
            $result['order_way']="小板车接车";
        }else {
            $result['order_way']="";
        }

        //用户是否提车
        if($order['finish_time']!=''){
            $fname = explode(",",$orderInfo['order_info_tclxr']);
            $ftime = explode(' ',$order['finish_time']);
            $ftime = explode('-',$ftime[0]);
            $result['ftime'][0] =$ftime[0];
            $result['ftime'][1] =$ftime[1];
            $result['ftime'][2] =$ftime[2];
            $result['fname'] = $fname[0][0];
        }else{
            $result['ftime'][0] ='';
            $result['ftime'][1] ='';
            $result['ftime'][2] ='';
            $result['fname'] = '';
        }

        //委托时间-提车时间
        /* if ($carOrder['car_jc_time']!="" && $carOrder['car_jc_time']!=null){
         $times = explode(" ",$carOrder['car_jc_time']);
         $result['car_time'] = explode("-",$times[0]);
         }else{
         $result['car_time'][0] ="";
         $result['car_time'][1] ="";
         $result['car_time'][2] ="";
         } */

        //判断支付方式

        if($order_way['pay_way']=='W'){
            $result['payway'] = '微信支付';
        }else if($order_way['pay_way']=='Z'){
            $result['payway'] = '支付宝支付';
        }else{
            $result['payway'] = '货到付款';
        }


        //运输标底
        if ($orderInfo['order_info_tc']=="Y"){
            $result['order_info_tc']="目的地自提";
        }else if ($orderInfo['order_info_tc']=="N"){
            $result['order_info_tc']="目的地上门车";
        }else {
            $result['order_info_tc']="";
        }

        //切割运输时间
        $result['yd_j_time'] =$this->trim_Str($carInfo['verify_car_time'],"arr_time");

        //获取起始地参数
        $star = $this->trim_Str($orderInfo['order_info_star'],"arr_info");
        if ($star[0]!=""){
            //查询起始地名称
            $map['area_id'] = array('eq',$star[0]);
            $startList= $area_M -> where($map)->field('area_name')->find();
            //存储起始地
            $result['star']=implode($startList);
        }else {
            $result['star']="";
        }

        //获取结束地参数
        $end = $this->trim_Str($orderInfo['order_info_end'],"arr_info");
        if ($end[0]!=""){
            //查询起始地名称
            $map['area_id'] = array('eq',$end[0]);
            $endList= $area_M -> where($map)->field('area_name')->find();
            //存储结束地
            $result['end']=implode($endList);
        }else {
            $result['end']="";
        }

        //拆分提车联系信息
        $result['tclxr'] =$this->trim_Str($orderInfo['order_info_tclxr'],"arr_info");

        //拆分送车联系信息
        $result['sclxr'] =$dfg = $this->trim_Str($orderInfo['order_info_sclxr'],"arr_info");
        if($order['order_status']=="W"){
            $result['dfg'] =$dfg[0];
        }
        //终点地址
        $result['address'] =$this->trim_Str($orderInfo['order_info_end_address'],"str");

        //车辆名称
        $result['brand'] =$this->trim_Str($orderInfo['order_info_brand'],"str");

        //车牌号
        $result['carmark'] =$this->trim_Str($orderInfo['order_info_carmark'],"str");

        //车辆价值
        $result['car_price'] =$this->trim_Str($orderInfo['order_info_price'],"str");
        $result['car_price'] = $result['car_price']/100;
        //保险费
        $result['bxd_price'] =$this->trim_Str($orderInfo['order_info_bxd'],"str");
        $result['bxd_price'] = $result['bxd_price']/100;
        //运输费
        if($orderInfo['product_type'] == 'B'){
            $yun = $orderInfo['order_info_c_car']+$orderInfo['order_info_t_car']+$orderInfo['order_info_s_car']+$order['order_info_margin'];
        }else{
            $yun = $orderInfo['order_info_c_car']+$orderInfo['order_info_t_car']+$order['order_info_margin'];
        }
        $result['ct_price'] = $yun;
        $result['ct_price'] = ($result['ct_price']/100)+($orderInfo['order_smsc_car']/100);
        //其他
        $result['qita'] = ($orderInfo['bill_price']/100)+($orderInfo['car_washing']/100);
        //总价
        $result['zjia'] = $orderInfo['order_info_zj']/100;
        //车钥匙
        $result['car_key'] = $this->trim_Str($carInfo['verify_car_keys'],"str");
        //公里数
        $result['car_km'] = $this->trim_Str($carInfo['verify_km'],"str");
        //提车预计行驶公里数
        $result['car_ti_km'] = $this->trim_Str($carInfo['verify_predict_km'],"str");
        //行李工具备注
        $result['verify_xingli'] = $this->trim_Str($carInfo['verify_xingli'],"str");
        //车身外观
        $result['verify_car_wai'] = $this->trim_Str($carInfo['verify_car_wai'],"str");
        //备注
        $result['verify_bei'] = $this->trim_Str($carInfo['verify_bei'],"str");

        //照片
        $result['img'] = $carImg;
        //服务费用处理
        if ($orderInfo['order_info_zj']!="" && $orderInfo['order_info_zj']!=null){
            $order_info_zj = $orderInfo['order_info_zj']/100;
            $number = str_split($order_info_zj);
            $num = count($number);
            switch ($num){
                case 1 :
                    $result['number'][1] = "零";
                    $result['number'][2] = "零";
                    $result['number'][3] = "零";
                    $result['number'][4] = "零";
                    $result['number'][5] = $this->print_Total($number[0]);;
                    break;
                case 2 :
                    $result['number'][1] = "零";
                    $result['number'][2] = "零";
                    $result['number'][3] = "零";
                    $result['number'][4] = $this->print_Total($number[0]);
                    $result['number'][5] = $this->print_Total($number[1]);
                    break;
                case 3 :
                    $result['number'][1] = "零";
                    $result['number'][2] = "零";
                    $result['number'][3] = $this->print_Total($number[0]);
                    $result['number'][4] = $this->print_Total($number[1]);
                    $result['number'][5] = $this->print_Total($number[2]);
                    break;
                case 4 :
                    $result['number'][1] = "零";
                    $result['number'][2] = $this->print_Total($number[0]);
                    $result['number'][3] = $this->print_Total($number[1]);
                    $result['number'][4] = $this->print_Total($number[2]);
                    $result['number'][5] = $this->print_Total($number[3]);
                    break;
                case 5 :
                    $result['number'][1] = $this->print_Total($number[0]);
                    $result['number'][2] = $this->print_Total($number[1]);
                    $result['number'][3] = $this->print_Total($number[2]);
                    $result['number'][4] = $this->print_Total($number[3]);
                    $result['number'][5] = $this->print_Total($number[4]);
                    break;
                default:
                    print_log("总价数据异常!");
                    break;
            }
        }else {
            $result['number'][0] = "零";
            $result['number'][1] = "零";
            $result['number'][2] = "零";
            $result['number'][3] = "零";
            $result['number'][4] = "零";
            $result['number'][5] = "零";
        }
        //写入标题
        $result['pdf_Title'] = "车妥妥服务合同";

        //当前时间
        $result['time'] = explode("-",date("Y-m-d",time()));
        return $result;
    }

    //填充字段
    public function trim_Str($str,$type){
        switch ($type){
            case "str":
                if($str!=""&&$str!=null){

                }else{
                    $str="";
                }
                break;
            case "arr_time":
                if($str!=""&&$str!=null){
                    $str_Time = explode(" ",$str);
                    $str = explode("-",$str_Time[0]);
                }else{
                    $str[0]="";
                    $str[1]="";
                    $str[2]="";
                }
                break;
            case "arr_info":
                if($str!=""&& $str!=null){
                    $str = explode(",",$str);
                }else{
                    $str[0]="";
                    $str[1]="";
                    $str[2]="";
                }
                break;
        }
        return $str;
    }

    //替换数字为汉字
    public function print_Total($num){
        switch ($num){
            case 0 :
                $num = "零";
                break;
            case 1 :
                $num = "壹";
                break;
            case 2 :
                $num = "贰";
                break;
            case 3 :
                $num = "叁";
                break;
            case 4 :
                $num = "肆";
                break;
            case 5 :
                $num = "伍";
                break;
            case 6 :
                $num = "陸";
                break;
            case 7 :
                $num = "柒";
                break;
            case 8 :
                $num = "捌";
                break;
            case 9 :
                $num = "玖";
                break;
        }
        return $num;
    }
}