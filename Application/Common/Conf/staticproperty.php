<?php
/**
 * 静态属性文件配置
 */
return array(
   //设置后台登陆时间
   'BACK_LOGIN_TIME' =>7200,
   'ADV_CONFIG' => array(
           'A' =>'首页(1000*1000)',
           'B' =>'列表(1000*1000)',
           'C' =>'优惠列表(1200*450)',//优惠路线banner
   ),
    'OTHER_INFO' => array(
        'GY' =>'妥妥运车',
        'XW' =>'新闻中心',
        'LX' =>'服务产品',
        'JR' =>'帮助中心',
        'QQ' =>'配送中心',
        'FK' =>'支付方式',
        'BZ' =>'关于妥妥',
    ),
    'INFO_TITLE' =>array(
        'MEMBERTITLE' => '您已通过实名认证',
        'UNMEMBERTITLE' => '您未通过实名认证'
    ),
    //(图片)
    'POSITION_CONFIG' => array( 
        'OPINION' => array('W'=>10000,'H'=>10000),//观点、课堂、专栏缩略图
        'A' => array('W'=>1000,'H'=>1000),//测试
        'B' => array('W'=>1000,'H'=>1000),//测试2
        'NUMIMG' => array('W'=>1200,'H'=>1200),//系统管理数量输入
        'XWZX' => array('W'=>1000,'H'=>1000),//新闻中心
        'LINKIMG' => array('W'=>1000,'H'=>1000),//友情链接
        'C' => array('W'=>1200,'H'=>451),//优惠路路线banner
        'SLT' => array('W'=>386,'H'=>234),//优惠路线图片
    ),
    //前台会员登陆时间设定,单位：秒
    'FRONT_LOGIN_TIME'=>3600,
    //默认头像
    'DEFAULT_IMAGE'=>'/Public/Front/images/privates/head99.jpg',
    //短信验证码有效期单位秒
    'NOTE_CODE_TIME' => 1200,
    //检测登陆状态
    'LOGIN_DETECTION_METHOD'=>array(
        'ownTel',
        'ownOwn',
        'ownShimi',
        'ownRzadd',
        'ownEdit',
        'ownzYaoqm',
        'bindingTelPage',
        'bindingTel',
        'bindingMailPage',
        'sendMailVerify',
        'bindingMail',
        'myDynamic',
        'privateMessage',
        'privateMessageInfo',
        'continueToCarry',
        'privateMessageReply',
        'privateMessageReplyFun',
        'sendPrivateMessagePage',
        'sendPrivateMassage',
        'conversionRimFun',
        'userScoreShow',
        'myCollection',
        'userscoreshow',
        'mycollection',
        'masterWorkList',
        'myFans',
        'myFocus',
        'submitMes',
        'setWork',
        'masterWorkList',
        'masterWorkYDetail',
        'masterWorkXDetail',
        'masterWorkHDetail',
        'workHedit',
        'workXedit',
        'workYedit',
        'setWorknext',
        'workInsert',
        'setWorkThree',
        'logout',
        'submitNovelMes',
        'submitMes',
        'submitNovelRe',
        'delNovelMe',
        'delNovelMeRe',
        'submitArticleMes',
        'submitArticleRe',
        'delArticleMe',
        'delArticleMeRe',
        'submitMusicMes',
        'submitMusicRe',
        'delMusicMe',
        'delMusicMeRe',
    ),
    'OPEN_ACC_BANK' =>array(
        'CYF' =>'民生银行',
        'CYG' =>'工商银行',
        'CYJ' =>'交通银行',
        'JSY' =>'建设银行',
        'NYY' =>'农业银行',
        'ZSY' =>'招商银行',
        'YZH' =>'邮政银行',
    ),
    //动态里的链接
    'URL_PATH' => array(
        'Y'=>'Moudelfuns/workYDetail',
        'X'=>'Moudelfuns/workXDetail',
        'H'=>'Moudelfuns/workHDetail'
    ),
    
)
?>