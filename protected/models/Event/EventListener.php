<?php
/**
 * @file class EventListener 以单例模式提供服务
 * 主要用于推送新鲜事，新鲜事类型参加Event类
 * @package application.models.Event
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */

Yii::import('application.models.Event.*');
Yii::import('application.models.User.UserFollow');
Yii::import('application.models.AR.EventUserAR');
Yii::import('application.models.Event.EventPusher');
Yii::import('application.models.Album.Photo');

class EventListener{
    
    /**
     * @var EventListener
     */
    private static $listener = NULL;
    
    /**
     * 创建事件监听器工厂
     * 
     * @return EventListenr
     */
    public static function getListener(){
        if(self::$listener){
            return self::$listener;
        }
        return new self;
    }
    
    private function __construct(){}
    
    private function __clone(){}

    /**
     * 运行监听器
     * @param array $args
     * <code>
     * array(
     *      'user_id' => int,
     *      'content' => mixed,
     *      'create_time' => datetime,
     *      'type' => string
     * )
     * </code>
     * @return Event
     */
    public function run($args){
        $ar = Event::addEvent( $args );
        EventPusher::pushEvent($ar);
        return $ar->getAttributes();
    }
    
    /**
     * 运行回复图片的事件监听器
     * @notice 回复图片不应该做到新鲜事中，应该迁移到消息中去
     * @param int $pid 图片id
     * @param int $uid 回复者id
     * @param string $content 回复内容
     * @param int $ruid 被回复者id
     * @param int $cid 评论id
     * @param type $args
     *
    public function runRePhoto($pid, $uid, $content, $ruid, $cid){
        
        //var_dump(func_get_args());exit; // debug
        $oid = Photo::getOwner($pid);
        
        //// 将消息推送给照片的所有者
        if($oid && $oid != $uid){
            $args = array(
                'user_id' => $uid,
                'content' => CJSON::encode(array(
                    'pid' => $pid,  // 图片id
                    'cid' => $cid,  // 评论id
                    'content' => $content,  // 评论内容
                 )),
                'create_time' => date('Y-m-d H:i:s'),
                'type' => Event::RPIC,  // 回复了图片
            );
            //print_r($args);echo "\n";
            $ar = Event::addEvent($args);
            //print_r($ar->getAttributes());exit;
            EventPusher::pushEvent($ar, TRUE, FALSE, array($oid));
        }
        if($ruid && $ruid != $uid){
            $args = array(
                'user_id' => $uid,
                'content' => CJSON::encode(array(
                    'pid' => $pid,
                    'cid' => $cid,
                    'content' => $content,
                )),
                'create_time' => date('Y-m-d H:i:s'),
                'type' => Event::RPICC, // 回复了图片评论
            );
            $ar = Event::addEvent($args);
            EventPusher::pushEvent($ar, TRUE, FALSE, array($ruid));
        }
    }*/
    
}