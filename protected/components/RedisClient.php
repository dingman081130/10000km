<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RedisClient
 *
 * @author yemin
 */
class RedisClient {
    //put your code here
    
    const HOST = 'localhost';
    const PORT = 6379;
    
    private static $_client;
    
    /**
     * Get redis client
     *
     * @return Redis
     */
    public static function getClient() {
        if (!self::$_client) {
            $client = new Redis();
            if ($client->connect(self::HOST, self::PORT)) {
                $client->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);
            } else {
                MuleApp::log($client->getLastError());
                return null;
            }
            self::$_client = $client;
        }
        
        return self::$_client;
    }
    
    private function __construct() {}
    
    private function __clone() {}
}

?>
