<?php
/**
 * @file class Event 管理事件
 * 主要针对Event表提供一些get/set方法
 * 以及与缓存打交道
 * @package application.models.Event
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */

Yii::import('application.models.AR.EventAR');
Yii::import('application.models.Event.Event');
Yii::import('application.models.Event.EventUserCleaner');

class EventCleaner{
    
    /**
     * 删除某相册原先的事件
     * @param int $aid 相册id
     * @return int 返回所删除事件的eid
     */
    public static function delAlbumEvent($aid){
        $eid = Event::getEventByAlbum($aid);
        self::delEvent($eid);
    }
    
    /**
     * 删除某个事件，从数据库与缓存中删除该事件以及该事件相关的推送信息
     * @param int $eid
     */
    private static function delEvent($eid){
        self::deleteEventDB($eid);
        self::deleteEventRedis($eid);
        EventUserCleaner::delEventFromUser($eid);
        
    }
    
    private static function deleteEventDB($eid){
        $sql = 'update event set is_delete = 1 where id = ?';
        $cmd = Yii::app()->db->createCommand($sql);
        $cmd->bindParam(1, $eid);
        $cmd->execute();
    }
    
    private static function deleteEventRedis($eid){
        $e_key = Event::getKey($eid);
        RedisClient::getClient()->delete($e_key);
    }
    
    
    
    
}