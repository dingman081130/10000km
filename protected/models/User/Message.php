<?php
/**
 * @file class Heehaw 管理用户之间的私信信息
 * @package application.models.User
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-25 00:42:01
 * @version
 */

Yii::import('application.models.User.User');
Yii::import('application.models.AR.PrivateLetterAR');
Yii::import('application.models.Pagination');

class Message{
    
    const PRE_UMC = 'UNREAD_MSG_CNT';   // 未读消息缓存，按用户检索
    
    public static function getUnreadMsgCntKey(){
        return self::PRE_UMC;
    }
    
    /**
     * 获取用户的会话
     * 
     * @param int $user_id 用户id
     * @return array e.g.
     * <code>
     * array(
     *      SENDER_ID => array(               // 发送方用户的id
     *          'sender_name' => '丁满',     // 发送方用户的名字
     *          'unread'    => 3,           // 未读信息条数 
     *          'last_msg'   => 'bye world', // 最近的一条信息
     *          'last_time' => READABLE TIME FORMAT
     *      ),
     *      ...
     * )
     * </code>
     */
    public static function getSession($user_id) {
        
        //// 获取每个会话中的消息总数
        $session_cnt = self::getSessionMsgCnt($user_id);
        //// 获取每个会话中未读消息总数
        $session_unread_cnt = self::getSessionUnreadMsgCnt($user_id);
        //// 获取每个会话的最近时间
        $session_last_time = self::getSessionLastTime($user_id);
        //// 获取每个会话的最近消息
        $session_last_msg = self::getSessionLastMsg($session_last_time);
        
        foreach($session_last_msg as $sender => &$one){
            $one['send_time'] = $one['send_time'];
            $unread_cnt  = isset($session_unread_cnt[$sender]) ? $session_unread_cnt[$sender] : 0;
            $cnt = isset($session_cnt[$sender]) ? $session_cnt[$sender] : 0;
            $one = array_merge($one, array(
                'cnt' => $cnt,
                'unread' => $unread_cnt,
                'sender_user' => User::getUser($sender),
                ));
        }
        return $session_last_msg;
    }

    /**
     * 获取发送人最近一次发送的未读消息的时间
     * @param int $recipient_id
     * @param array $send_id_arr
     * @return array e.g.
     * <code>
     * array(
     *      SENDER_ID => TIMSTAMP,
     *      ...
     * )
     * </code>
     */
    private static function getRecentUnreadMsgTime($recipient, $send_id_arr) {
        $send_id_arr[] = 0;
        $send_id_str = join(',', $send_id_arr);
        $data =  Yii::app()->db->createCommand()
                ->select('sender, max(send_time) as recent')
                ->from('private_letter')
                ->where("recipient=:recipient and sender in ($send_id_str) and is_read=0", array(':recipient'=>$recipient))
                ->group('sender')
                ->queryAll();
        $ret = array();
        foreach ($data as $one){
            $ret[$one['sender']] = $one['recent'];
        }
        return $ret;
    }
    
    /**
     * 获取两个用户之间的私信信息
     * 更新接受人的未读私信数量缓存
     * 
     * @param int $recipient 收信人id
     * @param int $sender    发信人id
     * @param int $cur_page = 1     当前页
     * @param int $per_page = 0     每页大小，查询余下全部
     * @return array e.g.
     * <code>
     * array(
     *      'page' => array(
     *          'cur' => int,  // 当前页
     *          'per' => int,  // 每页大小
     *          'cnt' => int,     // 总记录条数
     *          'page_cnt' => int,  // 页总数
     *      ),
     *      'list' => array(
     *          array(),
     *          ...
     *      )
     * )
     * </code>
     */
    public static function getMsgByRecerSender($recipient,$sender,$cur_page=1,$per_page=0) {
        $limit = $per_page > 0 ? $per_page : Yii::app()->params['messagePageSize'];
        $offset = Pagination::getOffset($cur_page, $limit);
        $list = Yii::app()->db->createCommand()
                ->select('*')
                ->from('private_letter')
                ->where('(sender=:sender and recipient=:recipient) or (sender=:recipient and recipient=:sender)', 
                        array(':sender'=>$sender,':recipient'=>$recipient))
                ->order('send_time desc')
                ->offset($offset)
                ->limit($limit)
                ->queryAll();
        $id_arr = array();
        foreach($list as &$one){
            $one = self::getMsg($one);
            if($recipient == $one['recipient']){
                $id_arr[] = $one['id'];
            }    
        }
        //// 将即将要读取出来的消息设置为已读状态
        $id_str = join(',', $id_arr);
        !$id_str && $id_str = '0';
        Yii::app()->db->createCommand()->update( 'private_letter', array('is_read' => 1), "id in ($id_str)");
        self::delUnreadMsgCntRedis($recipient);
        
        $tmp = Yii::app()->db->createCommand()
                ->select('count(*) as cnt')
                ->from('private_letter')
                ->where('(sender=:sender and recipient=:recipient) or (sender=:recipient and recipient=:sender)', 
                        array(':sender'=>$sender,':recipient'=>$recipient))
                ->queryAll();
        $cnt = $tmp[0]['cnt'];
        $page_cnt = Pagination::getPageCnt( $cnt, $limit);
        $page = array(
            'cur' => $cur_page,
            'per' => $limit,
            'cnt' => $cnt,
            'page_cnt' => $page_cnt,
        );
        return array(
            'page' => $page,
            'list' => $list,
        );
    }
    
    /**
     * 封装一个消息结构
     * @param array $msg_arr PrivateLetterAR::getAttributes()后的数组
     * @return array 在$msg_arr 中增加两个属性 sender_name与recipient_name 并将send_time转化为易读格式
     */
    private static function getMsg($msg_arr){
        if(empty($msg_arr)){
            return array();
        }
        $msg_arr['sender_user']     = User::getUser($msg_arr['sender']);
        $msg_arr['recipient_user']  = User::getUser($msg_arr['recipient']);
        return $msg_arr;
    }
    
    /**
     * 保存一条信息
     * 
     * @param int $recipient 接受人id
     * @param int $sender 发送人id
     * @param string $msg 消息内容
     * @return array 同self::getMsg()方法
     * 
     */
    public static function saveMsg($recipient, $sender, $msg ){
        $ar = new PrivateLetterAR;
        $ar->sender = $sender;
        $ar->recipient = $recipient;
        $ar->content = trim($msg);
        $ar->send_time = date('Y-m-d H:i:s');
        if( !$ar->save() ){
            MuleApp::log('发布信息失败:'.Utils::arrToString($ar->getErrors()), 'error');   // 错误日志
            return array();
        }
        MuleApp::log('发布信息成功:'. Utils::arrToString($ar->getAttributes()), 'info');
        self::delUnreadMsgCntRedis($recipient);
        return self::getMsg($ar->getAttributes());
    }
    
    /**
     * 获取用户未读的私信数量
     * 
     * @param int $user_id
     * @return int
     */
    public static function getUnreadMsgCnt($user_id){
        $key = self::getUnreadMsgCntKey();
        $client = RedisClient::getClient();
        $cnt = $client->hGet($key, $user_id);
        if ($cnt === FALSE) {
            $cnt = PrivateLetterAR::model()->count(
                'is_read=0 and recipient=:recipient', array(':recipient'=>$user_id));
            $client->hSet($key, $user_id, $cnt);
        }
        return $cnt;
    }
    
    /**
     * 删除用户未读消息数量的缓存
     * @param type $user_id
     */
    private static function delUnreadMsgCntRedis($user_id) {
        $key = self::getUnreadMsgCntKey($user_id);
        RedisClient::getClient()->hDel($key, $user_id);
    }
    
    /**
     * 获取每个session的消息总数目
     * @param int $user 收件人id
     * @return array e.g.
     * <code>
     * array(SENDER_ID => MSG_CNT)
     * </code>
     */
    private static function getSessionMsgCnt($user){
        $contact_user_arr = self::getUserContactWith($user);
        $ret = array();
        foreach($contact_user_arr as $one){
            $ret[$one] = self::getMsgCnt($user, $one);
        }
        return $ret;
    }
    
    /**
     * 获取跟一个用户有联系的所有用户
     * 
     * @param int $user_id
     * @return array 用户id列表
     */
    private static function getUserContactWith( $user_id ){
        $recer = self::getRecer($user_id);
        $sender= self::getSender($user_id);
        $ret = array_merge($recer, $sender);
        $ret = array_unique($ret);
        return $ret;
    }
    
    /**
     * 获取所有接收用户
     * 
     * @param int $sender
     * @param array 接收用户的id列表
     */
    private static function getRecer( $sender ){
        $data = Yii::app()->db->createCommand()
                ->select('distinct( recipient ) as recipient')
                ->from('private_letter')
                ->where("sender=:sender")
                ->bindParam(':sender', $sender)
                ->queryAll();
        $ret = array();
        foreach( $data as $one ){
            $ret[$one['recipient']] = $one['recipient'];
        }
        return $ret;
    }
    
    /**
     * 获取所有发送用户
     * 
     * @param int $reicipient
     * @param array 发送用户的id列表
     */
    private static function getSender( $recipient ){
        $data = Yii::app()->db->createCommand()
                ->select('distinct( sender ) as sender')
                ->from('private_letter')
                ->where("recipient=:recipient")
                ->bindParam(':recipient', $recipient)
                ->queryAll();
        $ret = array();
        foreach( $data as $one ){
            $ret[$one['sender']] = $one['sender'];
        }
        return $ret;
    }
    
    /**
     * 获取两个用户的msg总数
     * 不考虑是否删除
     * 结果的受众是user1
     * 
     * @param int $user1
     * @param int $user2
     * 
     * @reutrn int
     */
    private static function getMsgCnt( $user1, $user2 ){
    
        $data = Yii::app()->db->createCommand()
                ->select( 'count(*) as cnt' )
                ->from( 'private_letter' )
                ->where( 'recipient=:recipient and sender=:sender' )
                ->bindParam(':recipient', $user1)
                ->bindParam(':sender', $user2)
                ->queryAll();
        $cnt1= $data[0]['cnt'];
        
        $data = Yii::app()->db->createCommand()
                ->select( 'count(*) as cnt' )
                ->from( 'private_letter' )
                ->where( 'recipient=:recipient and sender=:sender' )
                ->bindParam(':recipient', $user2)
                ->bindParam(':sender', $user1)
                ->queryAll();
        $cnt2 = $data[0]['cnt'];
        return $cnt1 + $cnt2;
    }
    
    /**
     * 获取每个会话中用户没有读取的私信数量
     * 
     * @param int $recipient 收件人id
     * @return array e.g.
     * <code>
     * array(SENDER_ID => UNREAD_CNT,)
     * </code>
     */
    private static function getSessionUnreadMsgCnt($recipient) {
        $data = Yii::app()->db->createCommand()
                ->select('sender, count(*) as cnt')
                ->from('private_letter')
                ->where('is_read=0 and recipient=:recipient',array(':recipient'=>$recipient))
                ->group('sender')
                ->queryAll();
        $ret = array();
        foreach ($data as $one){
            $ret[$one['sender']] = $one['cnt'];
        }
        return $ret;
    }
    
    /**
     * 获取每个会话的最近一次时间
     * @param int $recipient 接收人id
     * @return array e.g.
     * <code>
     * array(SENDER_ID => Y-m-d H:i:s, )
     * </code>
     */
    private static function getSessionLastTime($recipient){
        $data = Yii::app()->db->createCommand()
                ->select('sender, max(send_time) as time')
                ->from('private_letter')
                ->where('recipient=:recipient', array(':recipient'=>$recipient))
                ->group('sender')
                ->queryAll();
        $ret = array();
        foreach($data as $one){
            $ret[$one['sender']] = $one['time'];
        }
        return $ret;
    }
    
    /**
     * 获取每个会话的最近一则消息
     * @param array $send_time e.g.
     * <code>
     * array( SENDER_ID => TIME, ... )
     * </code>
     * @return array e.g.
     * <code>
     * array(
     *      SENDER_ID => array(
     *          'id' => int,
     *          'sender' => int,
     *          'recipient' => int,
     *          'send_time' => datetime,
     *          'content' => string,
     *          'is_read' => bool
     *      ),
     *      ...
     * )
     * </code>
     */
    private static function getSessionLastMsg($sender_time){
        if(empty($sender_time)){
            return array();
        }
        $criteria = new CDbCriteria;
        $criteria->alias = 't';
        $criteria->select = 't.*';
        foreach($sender_time as $sender => $time){
            $sender = intval($sender);
            $time = trim($time);
            $criteria->addCondition( "sender=$sender and send_time='$time'", 'OR' );
        }
        $data = PrivateLetterAR::model()->findAll($criteria);
        $ret = array();
        foreach($data as $one){
            $ret[$one->sender] = $one->getAttributes();
        }
        return $ret;
    }
    
    public static function setSessionsRead($user_id, $session_id_arr) {
        foreach ($session_id_arr as $session_id) {
            self::setSessionRead($user_id, $session_id);
        }
    }
    
    private static function setSessionRead($user_id, $session_id) {
        $user_id = intval($user_id);
        $session_id = intval($session_id);
        $sql = "update private_letter set is_read = 1 where recipient = {$user_id} and sender = {$session_id}";
        Yii::app()->db->createCommand($sql)->execute();
        self::delUnreadMsgCntRedis($user_id);
        return true;
        
    }
}
