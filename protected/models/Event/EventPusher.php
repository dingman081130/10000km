<?php
/**
 * @file class EventPusher 管理事件的推送,主要针对EventUser表进行写操作
 * @package application.models.Event
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */

Yii::import('application.models.User.UserFollow');
Yii::import('application.models.AR.EventUserAR');
Yii::import('application.models.Event.Event');
Yii::import('application.models.Event.EventUser');
Yii::import('application.models.Event.EventCleaner');
Yii::import('application.models.Album.Photo');

class EventPusher{
    
    /**
     * 推送用户的一个事件给他所关注的人
     * 
     * @param EventAR $event 事件
     * @param bool $db = false 是否推送至数据库
     * @param bool $self = true 是否推送给自己
     * @param array $to = null 推送给指定的人，为空时推送给好友
     */
    public static function pushEvent($event, $db = FALSE, $self = TRUE, $to = NULL){
        //// 如果cache更新成功就不更新数据库（慢）
        if($db){
            return self::pushEventDB($event, $self, $to) && self::pushEventCache($event, $self, $to);
        }
        return self::pushEventCache($event, $self, $to);
    }
    
    /**
     * 推送用户的一个事件进入Redis
     * 
     * @param EventAR $event 事件
     * @param bool $self = true 是否推送给自己
     * @param array $to = null 推送给指定的人，为空时推送给好友
     * @return boolean
     */
    private static function pushEventCache($event, $self = TRUE, $to = NULL){
        if(!$to){
            $follows = UserFollow::getUserFansIds($event->user_id, $self);
        }else{
            $follows = $self ? array_unique(array_merge($to, array($event->user_id))) : $to;
        }
        foreach($follows as $fid){
            //@notice 如果push失败怎么处理
            self::addEventUser($fid, $event->id);
        }
        return TRUE;
    }
    
    /**
     * 推送用户的一个事件进入DB
     * 
     * @param EventAR $event 事件
     * @param bool $self = true 是否推送给自己
     * @param array $to = null 推送给指定的人，为空时推送给好友
     * @return boolean
     * 
     */
    private static function pushEventDB($event, $self = TRUE, $to = NULL){
        if(!$to){
            $follows = UserFollow::getUserFansIds($event->user_id, $self);
        }else{
            $follows = $self ? array_unique(array_merge($to, array($event->user_id))) : $to;
        }
        foreach($follows as $follow){     
            $ar = new EventUserAR;
            $ar->event_id = $event->id;
            $ar->user_id = $follow;
            $ar->create_time = $event->create_time;
            $ar->saveL();
        }
        //// 将事件标记为已推送状态
        return Event::setEventPushedByAR($event);
    }

    /**
     * 推送剩下的（没被推送过的事件到缓存和数据库,
     * 用于后台任务异步推送
     * @return boolean
     */
    public static function pushRestEvent(){
        $events = Event::getRestEventObj();
        foreach($events as $event){
            self::pushEventDB($event);
            self::pushEventCache($event);
        }
        return TRUE;
    }
    
    /**
     * 保存一条用户事件，如果这条用户事件不存在就创建
     * 如果存在则更新，要将这条用户事件按时间排列
     * @param int $uid 用户id
     * @param int $eid 事件id
     * @param bool
     */
    private static function addEventUser($uid, $eid){
        $key = EventUser::getKey($uid);
        RedisClient::getClient()->zRem($key, $eid); // 删除原来的用户事件
        RedisClient::getClient()->zAdd($key, time(), $eid);
        return true;
    }
    
    /**
     * 通过shell脚本去异步推送事件
     * 
     * @param int $user_id 用户id
     * @param int $event_id 事件id
     * @param datetime $create_time = null 事件的发生时间
     */
    private function pushEventDBAsync( $event ){
        // 触发一个异步任务去将用户的事件推送到各位follow的事件列表中
        $cmd = 'cd '.Yii::app()->basePath.'/commands/ && ';
        $cmd.= "sh pushEvent.sh $event->user_id $event->id $event->create_time";
        $cmd.= '> /dev/null &';
        //// 让shell去异步执行通知任务
        system($cmd);
    }
}