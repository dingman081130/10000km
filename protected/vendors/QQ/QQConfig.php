<?php

class QQConfig {
    static private $_conifg = null;
    
    public static function getConfig() {
        if (self::$_conifg == null) {
            $config				= new stdClass;
            $config->AppKey		= 100353422;	//你的API Key，请自行申请
            $config->SecretKey	= '232318f2ae0e936e9e8bf7ca93f738b7';	
            $config->CallbackUrl = 'http://10000km.cn/login/qqCallback';
            $config->scope = "get_user_info,add_share,add_album,upload_pic,add_topic,add_one_blog,add_t,add_pic_t";
            self::$_conifg = $config;
        }
        
        return self::$_conifg;
    }

}
?>