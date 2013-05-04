<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.vendors.*');
Yii::import('application.models.AR.SocialAccountAR');

/**
 * Description of SocialAccount
 *
 * @author yemin
 */
class SocialAccount {
    //put your code here
    
    const REGISTER_SHARE_MSG = '我刚刚加入了一万公里旅行网；穷游，找驴友，找沙发，就跟我一起来加入吧，我的旅程即将开启http://10000km.cn';
    
    public static function registerShare($account_type, $account_id, $access_token) {
        if ($account_type == SocialAccountAR::TYPE_WEIBO) {
            return self::publishWeiboStatus($account_id, $access_token);
        } else if ($account_type == SocialAccountAR::TYPE_QQ) {
            return self::publishQQStatus($account_id, $access_token);
        } else if ($account_type == SocialAccountAR::TYPE_RENREN) {
            return self::publishRenrenStatus($account_id, $access_token);
        }
    }
    
    
    
    private static function publishWeiboStatus($account_id, $access_token) {
        require_once 'Weibo/WeiboConfig.php';
        $config = WeiboConfig::getConfig();
        $client = new SaeTClientV2($config->AppKey, $config->SecretKey, $access_token);
        $client->update(self::REGISTER_SHARE_MSG . ' @一万公里旅行');
    }
    
    private static function publishQQStatus($account_id, $access_token) {
        require_once 'QQ/QQConfig.php';
        $config = QQConfig::getConfig();
        $url = 'https://graph.qq.com/share/add_share?access_token='.urlencode($access_token)
                .'&oauth_consumer_key=' . urlencode($config->AppKey)
                .'&openid=' . urlencode($account_id)
                .'&title=' . urlencode('我注册了一万公里旅行网')
                .'&url=' . urldecode('http://10000km.cn')
                .'&summary=' . urlencode(self::REGISTER_SHARE_MSG)
                .'&site='. urldecode('一万公里旅行')
                .'&fromurl=' . urldecode('http://10000km.cn');
        file_get_contents($url);
    }
    
    private static function publishDoubanStatus($account_id, $access_token) {
        require_once 'Douban/DoubanConfig.php';
        $config = DoubanConfig::getConfig();
        
        //$client = new Douban_Tclientv2($config->AppKey, $config->SecretKey, $access_token);
        //$client->shuo_push(self::REGISTER_SHARE_MSG);
    }
    
    private static function publishRenrenStatus($account_id, $access_token) {
        require_once 'Renren/RenrenConfig.php';
        require_once 'Renren/RenrenRestApiService.php';
        
        $config = RenrenConfig::getConfig();
        $url = 'https://api.renren.com/restserver.do';
        $playload = 'v=1.0&access_token=' . urlencode($access_token)
                .'&format=json'
                .'&call_id=' . rand()
                .'&method=feed.publishFeed'
                .'&name=' . urlencode('我注册了一万公里旅行网')
                .'&description=' . urlencode(self::REGISTER_SHARE_MSG)
                .'&url=' . urlencode('http://10000km.cn');


        $rrObj = new RenrenRestApiService;
        $params = array(
            'v' => '1.0',
            'access_token' => $access_token,
            'format' => 'json',
            'call_id' => rand(),
            'name' => '我注册了一万公里旅行网',
            'description' => self::REGISTER_SHARE_MSG,
            'url' => 'http://10000km.cn',
        );
        
        $res = $rrObj->rr_post_curl('feed.publishFeed', $params);
        //print_r($res);
    }
}

?>
