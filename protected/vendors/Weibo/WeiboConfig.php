<?php

require_once 'saetv2.ex.class.php';

class WeiboConfig {
    static private $_conifg = null;
    
    public static function getConfig() {
        if (self::$_conifg == null) {
            $config				= new stdClass;
            $config->AppKey		= '174356130';	//你的API Key，请自行申请
            $config->SecretKey	= '24a49908b1700ab6c5b7430b4221569f';	
            $config->CallbackUrl = 'http://10000km.cn/login/weiboCallback';
            self::$_conifg = $config;
        }
        
        return self::$_conifg;
    }

}
?>