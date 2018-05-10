<?php
namespace Customer\Controller;

class AboutTuoTuoController extends BaseController {

	/**
	 * 公司介绍
	 */
    function companyIntroduce(){
        $ret = M("article") ->where(array('article_id'=>"49"))->find();
        $this->assign("ret",$ret);
    	$this->display("AboutTuoTuo:company_introduce");
    }

    /**
     * 安全保险
     */
    function safetySafeguard(){
        $ret = M("article") ->where(array('article_id'=>"50"))->find();
        $this->assign("ret",$ret);
    	$this->display("AboutTuoTuo:safety_safeguard");
    }

    /**
     * 常见问题
     */
    function commonQuestion(){
        $ret = M("article") ->where(array('article_id'=>"18"))->find();
        $this->assign("ret",$ret);
    	$this->display("AboutTuoTuo:common_question");
    }
    /**
     * 专业服务
     */
    function services(){
        $ret = M("article") ->where(array('article_id'=>"51"))->find();
        $this->assign("ret",$ret);
    	$this->display("AboutTuoTuo:services");
    }
    /**
     * 道路救援
     */
    function rescue(){
        $ret = M("article") ->where(array('article_id'=>"44"))->find();
        $this->assign("ret",$ret);
    	$this->display("AboutTuoTuo:rescue");
    }
    /**
     * 联系客服
     */
    function customer(){
    	$this->display("AboutTuoTuo:common_question");
    }
}