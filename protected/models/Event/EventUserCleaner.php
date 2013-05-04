<?php
/**
 * @file class EventUser 主要针对EventUser表进行读操作，包括对缓存的读
 * @package application.models.Event
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */

Yii::import('application.models.Event.EventUser');

class EventUserCleaner{
    
    public static function delEventFromUser($eid){
        self::delEventUserDB($eid);
        self::deleteEventUserRedis($eid);
    }
    
    private static function delEventUserDB($eid){
        $sql = 'update event_user set is_delete = 1 where event_id = ?';
        $cmd = Yii::app()->db->createCommand($sql);
        $cmd->bindParam(1, $eid);
        $cmd->execute();
    }
    
    private static function deleteEventUserRedis($eid){
        $eu = new EventUser;
        $eu_keys = $eu->getAllEUKeys();
        foreach($eu_keys as $eu_key){
            RedisClient::getClient()->zRem($eu_key, $eid);
        }
    }
}