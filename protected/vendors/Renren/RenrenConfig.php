<?php
class RenrenConfig {
    static private $_conifg = null;
    
    public static function getConfig() {
        if (self::$_conifg == null) {
            $config				= new stdClass;

            $config->APIURL		= 'http://api.renren.com/restserver.do'; //RenRen网的API调用地址，不需要修改
            $config->APIKey		= '59fec8294f6e4ee88d07dcd7ab0a7a3c';	//你的API Key，请自行申请
            $config->SecretKey	= 'd5d01054186346df89cea89e41d2e8fe';	//你的API 密钥
            $config->CallbackUrl = 'http://10000km.cn/login/renrenCallback';
            $config->APIVersion	= '1.0';	//当前API的版本号，不需要修改
            $config->decodeFormat	= 'json';	//默认的返回格式，根据实际情况修改，支持：json,xml
            self::$_conifg = $config;
        }
        
        return self::$_conifg;
    }

}
?>