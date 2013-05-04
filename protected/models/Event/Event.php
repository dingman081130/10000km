<?php
/**
 * @file class Event 管理新鲜事
 * 主要针对Event表提供一些get/set方法
 * 以及与缓存打交道
 * @package application.models.Event
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */

Yii::import('application.models.AR.EventAR');

class Event{
    
    const E_PRE = 'E_';
    const EA_PRE = 'EA_';
    
    const TTL = 864000;
    const EA_TTL = 3600;
    
    const HEEHAW = 'heehaw';    // 发布驴叫的类型定义
    const PTRIP = 'ptrip';      // 发布新的旅程
    const PPIC = 'ppic';        // 发布了一张图片
    const PGRP = 'pgrp';        // 发布小组
    const PTOP = 'ptop';        // 发布话题
    // 废弃一下两种新鲜事类型
    //const RPIC = 'rpic';        // 回复了图片      // 要移到消息中去，见SysMessage中的rpic，不应该出现在新鲜事列表中
    //const RPICC = 'rpicc';      // 回复了图片评论   // 要移到消息中去，见SysMessage中的rpicc，不应该出现在新鲜事列表中
    
    private $_client; // RedisClient的一个实例
    private $_mode = 'Cache';   // 有两种工作模式，一种在cache下工作，一种在DB下工作
    
    /**
     * 构造函数
     */
    public function __construct() {
        //// 没两秒重新连接redis，重试三次
        for($i = 0; $i <= 3; $i++){
            if($this->_client = RedisClient::getClient()){
                break;
            }
            sleep(2);
        }
        //// 如果连接不上redis则将工作模式改为DB模式
        if(!$this->_client){
            $this->_mode = 'DB';
        }
    }

    /**
     * 获取事件信息
     * 
     * @param int $id 事件id
     * @return array
     */
    public function getEvent($id){
        $key = self::getKey($id);
        !RedisClient::getClient()->exists($key) && $this->_mode = 'DB';
        $func = 'getEvent'.$this->_mode;
        $event = $this->$func($key);
        !$event && $event = array();
        return $event;
    }
    
    /**
     * 通过key值从缓存中读取事件信息
     * 
     * @param string $key
     * @return array
     */
    private function getEventCache($key){
        $event = $this->_client->get($key);
        return $event;
    }
    
    /**
     * 通过key值从数据库中读取事件信息
     * 如果要从数据库中取则必定是缓存不存在或者过期
     * 所以该方法会更新缓存
     * @param string $key
     * @return array
     */
    private function getEventDB($key){
        $arr = explode('_', $key);
        $id = $arr[1];
        $event = self::getEventById($id);
        if($event){
            self::setEventCache($event);
        }
        return $event;
    }
    
    /**
     * 通过id从数据库中获取事件信息
     * @param int $id
     * @param array
     */
    private static function getEventById($id){
        $ar = EventAR::model()->findByPk($id, 'is_delete = 0');
        if(!$ar){
            return array();
        }
        return $ar->getAttributes();
    }
    
    /**
     * 获取所有没有推送的Event对象
     * 
     * return array
     */
    public static function getRestEventObj(){
        
        return EventAR::model()->findAll('is_push=0 || is_push=NULL');
    }
    
    /**
     * 将一条事件标记为已推送
     * 
     * @param int $id
     * @return boolean
     */
    public static function setEventPushedById($id){
        $ar = EventAR::model()->findByPk($id);
        return self::setEventPushedByAR($ar);
    }

    /**
     * 将一条事件标记为已推送状态
     * 
     * @param array $ars
     * @return boolean
     */
    public static function setEventPushedByAR($ar){
        $ar->is_push = 1;
        return $ar->saveL();
    }
    
    /**
     * 新增一个事件，写库写缓存
     * 
     * @param array $attributes
     * @return EventAR
     */
    public static function addEvent($attributes){
        $ar = new EventAR();
        $ar->setAttributes($attributes, false);
        if($ar->saveL()){
            self::setEventCache($ar->getAttributes());
        }
        return $ar;
    }
    
    /**
     * 写事件缓存
     * @param array $event 事件属性
     */
    private static function setEventCache($event){
        $key = self::getKey($event['id']);
        $rc = RedisClient::getClient();
        if($rc){
            $rc->setex($key, self::TTL, $event);
        }
    }
    
    /**
     * 新增相册传图事件，将相册与事件的关联关系写入缓存
     * 
     * @param array $album
     * @return EventAR
     */
    public static function addAlbumEvent($album){
        //// 组装事件数据
        $aid = $album['album']['id'];
        $photo = array();
        foreach($album['photo'] as $id => $one){
            $photo[$id] = $id;
        }
        $pcnt = count($photo);
        $attr['user_id']     = $album['album']['user_id'];
        $attr['create_time'] = $album['album']['update_time'];
        $attr['content'] = CJSON::encode(array(
            'album_id' => $aid,
            'photo' => $photo,
            'pcnt' => $pcnt,
        ));
        $attr['type'] = Event::PPIC;
        $attr['is_push'] = 1;
        
        //// 保存事件
        $event = self::addEvent($attr);
        //// 保存相册与事件的关联关系
        $ea_key = self::getEAKey($aid);
        RedisClient::getClient()->setex($ea_key, self::EA_TTL, $event->id);
        return $event;
    }
    
    /**
     * 通过相册id获取相册最近更新的事件的id
     * @param int $aid 相册id
     * @return int 事件id
     */
    public static function getEventByAlbum($aid){
        $ea_key = self::getEAKey($aid);
        return RedisClient::getClient()->get($ea_key);
        
    }
    
    private static function getEAKey($aid){
        return self::EA_PRE.$aid;
    }

    /**
     * 通过事件的id获取事件的key值
     * 
     * @param int $id 事件的id
     * @return int 事件的key值
     */
    public static function getKey($id){
        return self::E_PRE.$id;
    }
    
    
    /**
     * 获取某个用户产生的新鲜事，过滤掉用户回复图片和用户回复别人针对图片的回复
     * 
     * @param int $uid 用户id
     * @param int $offset = 0 位移
     * @param int $limit = 10 限长
     * @return array
     */
    public static function getUserEvent($uid, $offset=0, $size=5) {
        return Yii::app()->db->createCommand()
                ->select('*')
                ->from('event')
                ->where('user_id = :uid and is_delete = 0 and type not in ("rpicc","rpic")', array(
                    ':uid' => $uid,
                ))
                ->order('create_time desc')
                ->offset($offset)
                ->limit($size)
                ->queryAll();
    }
}