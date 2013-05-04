<?php

require_once 'doubanv2.class.php';

class DoubanConfig {
    static private $_conifg = null;
    
    public static function getConfig() {
        if (self::$_conifg == null) {
            $config				= new stdClass;
            $config->AppKey		= '0c48a15a6d4c645c24d9e40f47e52d14';	//你的API Key，请自行申请
            $config->SecretKey	= 'fd359f4919610104';	
            $config->CallbackUrl = 'http://10000km.cn/login/doubanCallback';
            self::$_conifg = $config;
        }
        
        return self::$_conifg;
    }

}
?>