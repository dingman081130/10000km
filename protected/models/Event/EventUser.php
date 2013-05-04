<?php
/**
 * @file class EventUser 主要针对EventUser表进行读操作，包括对缓存的读
 * @package application.models.Event
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */

Yii::import('application.models.Event.Event');
Yii::import('application.models.AR.EventUserAR');

class EventUser{
    
    const EU_PRE = 'EU_';
    
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
     * 获取推给用户的事件列表
     * 
     * @param int $user 用户id
     * @param int $offset = 0 偏移量
     * @return array e.g.
     * <code>
     * array(
     *      EVENT_ID => array(
     *          'type' => 'heehaw',     //事件类型
     *          'user_id' => int,       //发布者id
     *          'content' => string,    //内容
     *          'create_time' => datetime,  //创建时间
     *          'id' => int,    //事件id,
     *          'is_push' => bool,  //该事件是否被推送（写入数据库event_user）
     *      ),
     * )
     * </code>
     */
    public function getEventByUser( $user, $offset = 0, $limit = 10 ){
        //print_r($limit);exit;
        $key = self::getKey($user);
        !self::isKeyExists($key) && $this->_mode = 'DB'; //// 如果key不存在则切换到db模式
        $mode = $this->_mode;
        $func = 'getCnt'.$mode;
        //print_r($func);exit;
        $cnt = $this->$func($key);
        //echo $cnt;die;  //debug
        $size = $limit;
        $stop = $cnt - 1 - $offset;
        if($stop < 0){
            return array();
        }
        ( $start  = $stop - $size + 1 ) < 0 && $start = 0;
        MuleApp::log('offset:'.$offset.' start:'.$start.' stop:'.$stop.' size:'.$size.' cnt:'.$cnt);  // debug
        
        $func = 'getEventIds'.$mode;
        $e_ids = $this->$func($key, $start, $stop);
        //print_r($e_ids);exit;   // debug
        $ret = array();
        $event = new Event;
        foreach($e_ids as $e_id){
            $tmp = $event->getEvent($e_id);
            //var_dump($tmp);exit;
            if(!$tmp){
                continue;
            }
            $ret[$tmp['id']] = $tmp;
        }
        return $ret;
    }
    
    /**
     * 获取用户事件的key
     * 
     * @param int $uid
     * @return  string
     */
    public static function getKey($uid){
       return self::EU_PRE.$uid; 
    }
    
    /**
     * 通过key获取用户id
     */
    private static function getIdByKey($key){
        $t = explode('_', $key);
        $uid = $t[1];
        return $uid;
    }
    
    /**
     * 判断一个key是否存在
     */
    private static function isKeyExists($key){
        return RedisClient::getClient()->exists($key);
    }


    /**
     * Cache模式下获取推送给用户的事件总数
     * 
     * @param string $key 用户的key
     * @return int
     */
    private function getCntCache($key){
        return $this->_client->zSize($key);
    }
    
    /**
     * DB模式下获取推送给用户的事件总数
     * $param string$ key 用户的key
     * @return int
     */
    private function getCntDB($key){
        $uid = self::getIdByKey($key);
        return EventUserAR::model()->count('is_delete = 0 and user_id = ?', array($uid));
    }

    
    /**
     * Cache模式下获取推送给某个用户的事件id
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array
     */
    private function getEventIdsCache($key, $start, $end){
        MuleApp::log(Utils::arrToString(func_get_args()));
        $data = $this->_client->zRange($key, $start, $end);
        rsort($data);
        //echo 'redis:';
        //print_r($data);exit;
        return $data;
    }
    
    /**
     * 获取所有推送给用户的事件id
     * 在缓存不存在的情况下会调用此方法，所以该事件会重新写缓存key
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array
     * 
     */
    private function getEventIdsDB($key, $start, $end){
        $data = self::reLoadKey($key);
        $offset = $start - 1;
        $length = $end - $offset;
        $length < 0 && $length = 0;
        $data = array_slice($data, $offset, $length);
        rsort($data);
        //echo 'mysql:';
        //print_r($data);exit;
        return $data;      
    }
    
    /**
     * 获取所有带EU_前缀的key值
     * @return type
     */
    public function getAllEUKeys(){
        $ret = $this->_client->getKeys(self::EU_PRE.'*');
        return $ret;
    }
    
    /**
     * 重装用户收到事件的缓存
     * @param string $key
     * @param array 返回重载的内容
     */
    private static function reLoadKey($key){
        $uid  = self::getIdByKey($key);
        $data = self::getUserEventDB($uid);
        $ret  = array();
        RedisClient::getClient()->delete($key);
        foreach($data as $one){
            RedisClient::getClient()->zAdd($key, strtotime($one['create_time']), $one['event_id']);
            $ret[$one['event_id']] = $one['event_id'];
        }
        return $ret;
    }
    
    /**
     * 从数据库中获取推给某个用户的所有新鲜事
     * 按时间升序排列
     * @param int $uid 用户id
     * @param array
     */
    private static function getUserEventDB($uid){
        return Yii::app()->db->createCommand()
                ->select('*')
                ->from('event_user')
                ->where('is_delete = 0 and user_id = :uid', array(':uid' => $uid))
                ->queryAll();
    }
}