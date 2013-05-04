<?php
/**
 * @file class Heehaw 管理系统发送的消息
 * @package application.models.Message
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2013-01-22 00:42:01
 * @version
 */
Yii::import('application.models.User.Message');
Yii::import('application.models.Album.*');
Yii::import('application.models.Trip.Trip');
Yii::import('application.models.User.User');
Yii::import('application.models.AR.TripCommentAR');
Yii::import('application.models.Group.Group');
Yii::import('application.models.Group.Topic');
Yii::import('application.models.Group.GroupUser');

class SysMessage extends Message{
    
    const NEW_TOPIC = 'NEW_TOPIC';
    const USER_JOIN_TRIP = 'USER_JOIN_TRIP';
    
    /**
     * 格式化存储消息
     * @param array $type_args eg.
     * <code>
     * array(
     *      TYPE => array(
     *          'replace' => array(':reply_id' => 13, ...),
     *          'recipients' => array(),    // user 列表
     *      ),
     *      ...
     * )
     * </code>
     */
    private static function saveMsgFormat( $type_args ) {
        $msg_formats = self::getMsgFormats();
        foreach ($type_args as $type => $args ) {
            $msg_format = $msg_formats[$type];
            foreach($args['replace'] as $hold_place => $value) {
                $msg_format = str_replace($hold_place, CHtml::encode($value), $msg_format);
            }
            foreach($args['recipients'] as $recipient) {
                self::saveMsg($recipient['id'], User::getSysId(), $msg_format);
            }   
        }
        return true;
    }
    
    private static function getMsgFormats() {
        return array(
            'RPIC'       => '<a href="/user/:reply_id">:reply_name</a>回复了你的图片<a href="/photo/:album_id#:photo_id">:photo_title</a>',    //// 回复照片的消息格式
            'RPIC_SYS'   => '<a href="/user/:reply_id">:reply_name</a>回复了<a href="/user/:creator_id">:creator_name</a>的图片<a href="/photo/:album_id#:photo_id">:photo_title</a>',    //// 回复照片的消息格式
            'RPICC'      => '<a href="/user/:reply_id">:reply_name</a>在图片<a href="/photo/:album_id#:photo_id">:photo_title</a> 中回复了你',    /// 回复照片评论
            'RPICC_SYS'  => '<a href="/user/:reply_id">:reply_name</a>在图片<a href="/photo/:album_id#:photo_id">:photo_title</a> 中回复了<a href="/user/:replied_user_id">:replied_user_name</a>',
            'RTOP'       => '<a href="/user/:reply_id">:reply_name</a>回复了你的话题<a href="/topic/:topic_id">:topic_title</a>',
            'RTOP_SYS'   => '<a href="/user/:reply_id">:reply_name</a>回复了<a href="/user/:creator_id">:creator_name</a>的话题<a href="/topic/:topic_id">:topic_title</a>',
            'RTOPC'      => '<a href="/user/:reply_id">:reply_name</a>在话题<a href="/topic/:topic_id">:topic_title</a> 中回复了你',
            'RTOPC_SYS'  => '<a href="/user/:reply_id">:reply_name</a>在话题<a href="/topic/:topic_id">:topic_title</a> 中回复了<a href="/user/:replied_user_id">:replied_user_name</a>',
            'CTOP'       => '<a href="/user/:creator_id">:creator_name</a>在小组<a href="/group/:group_id">:group_name</a>中创建了话题<a href="/topic/:topic_id">:topic_title</a>',
            'FOL'        => '<a href="/user/:follow_id">:follow_name</a>关注了你～',
            'FOL_SYS'    => '<a href="/user/:follow_id">:follow_name</a>关注了<a href="/user/:user_id">:user_name</a>～',
            'JTRIP'      => '<a href="/user/:join_id">:join_name</a>参加了旅行<a href="/trip/:trip_id">:trip_title</a>',
            'FTRIP'      => '<a href="/user/:follow_id">:follow_name</a>关注了你发起的旅行<a href="/trip/:trip_id">:trip_title</a>',
            'FTRIP_SYS'  => '<a href="/user/:follow_id">:follow_name</a>关注了<a href="/user/:creator_id">:creator_name</a>发起的旅行<a href="/trip/:trip_id">:trip_title</a>',
            'RTRIP'      => '<a href="/user/:user_id">:user_name</a>评论了你发起的旅行<a href="/trip/:trip_id">:trip_title</a>',
            'RTRIP_SYS'  => '<a href="/user/:user_id">:user_name</a>评论了<a href="/user/:creator_id">:creator_name</a>发起的旅行<a href="/trip/:trip_id">:trip_title</a>',
            'RTRIPC'     => '<a href="/user/:user_id">:user_name</a>在旅行<a href="/trip/:trip_id">:trip_title</a>中回复了你',
            'RTRIPC_SYS' => '<a href="/user/:user_id">:user_name</a>在旅行<a href="/trip/:trip_id">:trip_title</a>中回复了<a href="/user/:replied_user_id">:replied_user_name</a>',
            'JGRP'       => '<a href="/user/:user_id">:user_name</a>参加了你创建的小组<a href="/group/:group_id">:group_name</a>',
            'JGRP_SYS'   => '<a href="/user/:user_id">:user_name</a>参加了<a href="/user/:creator_id">:creator_name</a>创建的小组<a href="/group/:group_id">:group_name</a>',
            'SIGNUP'     => '欢迎注册一万公里~',
            'SIGNUP_SYS' => '<a href="/user/:user_id">:user_name</a>注册了一万公里',
        );
    }
    
    /**
     * 推送用户注册信息给一万公里
     * 
     * @param int $user_id
     */
    public static function saveSingup($user_id) {
        $user = User::getUser($user_id);
        if (!$user) {
            return false;
        }
        $replace = array(':user_id' => $user['id'], ':user_name' => $user['name']);
        return self::saveMsgFormat(array(
            'SIGNUP' => array(
                'replace' => $replace,
                'recipients' => array($user)
            ),
            'SIGNUP_SYS' => array(
                'replace' => $replace,
                'recipients' => array(array('id' => User::getSysId())))));
    }
    
    /**
     * 推送用户参加小组的动作给小组的创建者
     * @param int $join_id 参加者的id
     * @param int $group_id 组id
     * @return boolean
     */
    public static function saveJGrpMsg($join_id, $group_id){
        
        if (!($join = User::getUser($join_id)))
            return false;
        if (!($group = Group::getGroup($group_id)))
            return false;
        if (!($creator = User::getUser($group['creator_id'])))
            return false;
        $replace = array(
            ':user_id' => $join['id'],
            ':user_name' => $join['name'],
            ':group_id' => $group['id'],
            ':group_name' => $group['name'],
            ':creator_id' => $creator['id'],
            ':creator_name' => $creator['name'],
        );
        self::saveMsgFormat(array(
            'JGRP' => array('replace'=>$replace, 'recipients' => array($creator)),
            'JGRP_SYS' => array('replace'=>$replace, 'recipients' => array(array('id' => User::getSysId())))));
    }
    
    /**
     * 推送用户评论旅程评论的信息给旅行的发起者
     * 
     * @param int $user_id 评论者id
     * @param int $comment_id 被评论的评论id
     * @return boolean
     */
    public static function saveRTripCMsg($user_id, $comment_id) {
        
        if (!($user = User::getUser($user_id)))
            return false;
        if (!($comment = TripCommentAR::model()->findByPK($comment_id)))
            return false;
        $trip_id = $comment['trip_id'];
        if (!($trip = Trip::getTrip($trip_id)))
            return false;
        //// 被评论者
        if (!($cuser = User::getUser($comment['user_id']))) {
            return false;
        }
        $replace = array(
            ':user_id'=>$user['id'],
            ':user_name'=>$user['name'],
            ':trip_id' => $trip['id'],
            ':trip_title' => $trip['title'],
            ':replied_user_id' => $cuser['id'],
            ':replied_user_name' => $cuser['name']
        );
        self::saveMsgFormat(array(
           'RTRIPC' => array('replace' => $replace,'recipients' => array($cuser)),
           'RTRIPC_SYS' => array('replace' => $replace, 'recipients' => array(array('id' => User::getSysId())))));
        return true;
    }
    
    /**
     * 推送用户评论旅程的信息给旅行的发起者
     * 
     * @param int $user_id 评论者id
     * @param int $trip_id 旅行id
     * @return boolean
     */
    public static function saveRTripMsg($user_id, $trip_id) {
        
        if (!($user = User::getUser($user_id)))
            return false;
        if (!($trip = Trip::getTrip($trip_id)))
            return false;
        if (!($creator = User::getUser($trip['creator_id'])))
            return false;
        $replace = array(
            ':user_id' => $user['id'],
            ':user_name' => $user['name'],
            ':creator_id' => $creator['id'],
            ':creator_name' => $creator['name'],
            ':trip_id' => $trip['id'],
            ':trip_title' => $trip['title']
        );
        return self::saveMsgFormat(array(
            'RTRIP' => array('replace' => $replace,'recipients' => array($creator)),
            'RTRIP_SYS' => array('replace' => $replace, 'recipients' => array(array('id' => User::getSysId())))));
    }
    
    /**
     * 推送用户创建的话题消息给小组成员
     * 异步推送
     * 
     * @param int $topic_id 话题id
     * @param string $mode = 'save' 当mode=save时只保存到redis中，当为push时，将redis中的缓存取出，然后push给用户
     * @return boolean
     */
    public static function saveCTopMsg($topic_id, $mode = 'save') {
        if($mode == 'save')
            return RedisClient::getClient()->lPush(self::NEW_TOPIC, $topic_id);
        $client = RedisClient::getClient();
        if(!$client)
            return FALSE;
        $topics = array();
        while($ret = $client->rPop(self::NEW_TOPIC))
            !in_array($ret, $topics) && $topics[] = $topics[] = $ret;
        foreach($topics as $topic) {
            if(!($topic = Topic::getTopic($topic)))
                continue;
            if(!($group = Group::getGroup($topic['group_id'])))
                continue;
            if(!($creator = User::getUser($topic['author_id'])))
                continue;
            $tmp_recipients = GroupUser::getGroupUsers($group['id'], 0, 100000);
            $recipients = array();
            foreach($tmp_recipients as $one)
                if($one['id'] != $creator['id'])
                    $recipients[$one['id']] = $one;
            
            $recipients[User::getSysId()] = array('id' => User::getSysId());
            $replace = array(
                ':creator_id' => $creator['id'],
                ':creator_name' => $creator['name'],
                ':group_id' => $group['id'],
                ':group_name' => $group['name'],
                ':topic_id' => $topic['id'],
                ':topic_title' => $topic['title']
            );
            self::saveMsgFormat(array(
                'CTOP' => array('replace' => $replace,'recipients' => $recipients),
            ));
        }
        return TRUE;
        
    }
    
    /**
     * 推送用户参加trip的动作给trip的发起者
     * 
     * @param int $join_id 关注者的id
     * @param int $trip_id 旅程id
     * @param string $mode = 'save' 当mode=save时只保存到redis中，当为push时，将redis中的缓存取出，然后push给用户
     * @return boolean
     */
    public static function saveJTripMsg($join_id, $trip_id, $mode = 'save') {
        if($mode == 'save')
            return RedisClient::getClient()->lPush(self::USER_JOIN_TRIP, $join_id.'_'.$trip_id);
        $client = RedisClient::getClient();
        if(!$client)
            return FALSE;
        $join_trip = array();
        while($ret = $client->rPop(self::USER_JOIN_TRIP))
            !in_array($ret, $join_trip) && $join_trip[] = $ret;
        
        foreach($join_trip as $one) {
            list($join_id, $trip_id) = explode('_', $one);
            if(!($trip = Trip::getTrip($trip_id)))
                continue;
            if(!($join = User::getUser($join_id)))
                continue;
            
            $recipients = array_merge( Trip::getTripParticipants($trip_id), Trip::getTripFollowers($trip_id ), array(User::getSysId()));
            
            $recipients = array_unique($recipients);
            foreach($recipients as $key => &$one)
                if($one == $join['id']) 
                    unset($recipients[$key]);
                else
                    $one = array('id' => $one);
            
            $replace = array(
                ':join_id' => $join['id'], 
                ':join_name' => $join['name'], 
                ':trip_id' => $trip['id'], 
                ':trip_title' => $trip['title']);
            self::saveMsgFormat(array(
                'JTRIP' => array('replace' => $replace, 'recipients' => $recipients)
            ));
        }
        return TRUE;
    }
    
    /**
     * 推送用户关注trip的动作给trip的发起者
     * 
     * @param int $follow_id 关注者的id
     * @param int $trip_id 旅程id
     * @return boolean
     */
    public static function saveFTripMsg($follow_id, $trip_id) {
        
        if (!($follow = User::getUser($follow_id)))
            return false;
        if (!($trip = Trip::getTrip($trip_id)))
            return false;
        if (!($creator = User::getUser($trip['creator_id'])))
            return false;
        if (!($user = User::getUser($trip['creator_id'])))
            return false;
        $replace = array(
            ':follow_id' => $follow['id'],
            ':follow_name' => $follow['name'],
            ':creator_id' => $user['id'],
            ':creator_name' => $user['name'],
            ':trip_id' => $trip['id'],
            ':trip_title' => $trip['title'],
        );
        return self::saveMsgFormat(array(
            'FTRIP' => array('replace' => $replace, 'recipients' => array($user)),
            'FTRIP_SYS' => array('replace' => $replace, 'recipients' => array(array('id' => User::getSysId())))));
    }
    
    /**
     * 推送关注者的关注东西给被关注者
     * 
     * @param int $user_id 被follow的用户id
     * @param int $follow_id follow的用户id
     */
    public static function saveFollowMsg($user_id, $follow_id) {
        if (!($user = User::getUser($user_id)))
            return false;
        if (!($follow = User::getUser($follow_id)))
            return false;
        $replace = array(
            ':follow_id' => $follow['id'],
            ':follow_name' => $follow['name'],
            ':user_id' => $user['id'],
            ':user_name' => $user['name'],
        );
        return self::saveMsgFormat(array(
            'FOL' => array('replace' => $replace, 'recipients' => array($user)),
            'FOL_SYS' => array('replace' => $replace, 'recipients' => array(array('id' => User::getSysId())))));
    }
    
    /**
     * 推送回复话题回复消息
     * 
     * @param int $replyer_id 回复人id
     * @param int $comment_id 评论id
     * @return boolean
     */
    public static function saveRtopcMsg($replyer_id, $comment_id ){
        
        if (!($comment = TopicComment::getComment($comment_id)))
            return false;
        //// 如果回复者回复的是自己的回复则不推送消息
        if ($replyer_id == $comment['author_id'])
            return true;
        if (!($topic = Topic::getTopic($comment['topic_id'])))
            return false;
        if (!($replyer = User::getUser($replyer_id)))
            return false;
        if (!($user = User::getUser($comment['author_id'])))
            return false;
        $replace = array(
            ':reply_id' => $replyer['id'],
            ':reply_name' => $replyer['name'],
            ':creator_id' => $user['id'],
            ':creator_name' => $user['name'],
            ':topic_id' => $topic['id'],
            ':topic_title' => $topic['title'],
        );
        return self::saveMsgFormat( array(
            'RTOP' => array('replace' => $replace, 'recipients' => array($user)),
            'RTOP_SYS' => array('replace' => $replace, 'recipients' => array(array('id' => User::getSysId())))));
    }
    
    /**
     * 推送回复话题消息
     * 实时推送，只推送给话题的创建者
     * 
     * @param int $replyer_id 回复人id
     * @param int $topic_id 话题id
     */
    public static function saveRtopMsg($replyer_id, $topic_id){
        if (!($topic = Topic::getTopic($topic_id))) 
            return false;
        if (!($replyer = User::getUser($replyer_id)))
            return false;
        //// 如果回复者就是话题的作者则不推送消息
        if ($replyer_id == $topic['author_id'])
            return true;
        if (!($user = User::getUser($topic['author_id'])))
            return false;
        $replace = array(
            ':reply_id' => $replyer['id'],
            ':reply_name' => $replyer['name'],
            ':creator_id' => $user['id'],
            ':creator_name' => $user['name'],
            ':topic_id' => $topic['id'],
            ':topic_title' => $topic['title'],
        );
        return self::saveMsgFormat( array(
            'RTOP' => array('replace' => $replace, 'recipients' => array($user)),
            'RTOP_SYS' => array('replace' => $replace, 'recipients' => array(array('id' => User::getSysId())))));
    }
    
    /**
     * 推送回复图片回复消息
     * 
     * @param int $replyer_id 回复人id
     * @param int $replied_user_id 被回复者id
     * @param int $pic_id 图片id
     * @return boolean
     */
    public static function saveRpiccMsg($replyer_id, $replied_user_id, $pic_id ){
        //// 如果是自己回复自己则不推送消息
        if ($replied_user_id == $replyer_id)
            return false;
        if (!($photo = Photo::getPhoto($pic_id)))
            return false;
        if (!($album = Album::getAlbum($photo['album_id'])))
            return false;
        if (!($replyer = User::getUser($replyer_id)))
            return false;
        if (!($replied_user = User::getUser($replied_user_id)))
            return false;
        
        $replace = array(
            ':reply_id' => $replyer['id'],
            ':reply_name' => $replyer['name'],
            ':album_id' => $album['id'],
            ':photo_id' => $photo['id'],
            ':photo_title' => $photo['title'],
            ':replied_user_id' => $replied_user['id'],
            ':replied_user_name' => $replied_user['name']
        );
        return self::saveMsgFormat( array(
            'RPICC' => array('replace' => $replace, 'recipients' => array($replied_user)),
            'RPICC_SYS' => array('replace' => $replace, 'recipients' => array(array('id' => User::getSysId())))));
    }
    
    /**
     * 推送回复图片消息
     * @param int $replyer_id 回复者id
     * @param int $pic_id 图片id
     * @return boolean
     */
    public static function saveRpicMsg($replyer_id, $pic_id){
        if (!($photo = Photo::getPhoto($pic_id)))
            return false;
        if (!($album = Album::getAlbum($photo['album_id'])))
            return false;
        if (!($replyer = User::getUser($replyer_id)))
            return false;
        //// 如果回复者就是话题的作者则不推送消息
        if ($replyer_id == $album['user_id'])
            return true;
        if (!($user = User::getUser($album['user_id'])))
            return false;
        
        $replace = array(
            ':reply_id' => $replyer['id'],
            ':reply_name' => $replyer['name'],
            ':album_id' => $album['id'],
            ':photo_id' => $photo['id'],
            ':photo_title' => $photo['title'],
            ':creator_id' => $user['id'],
            ':creator_name' => $user['name']
        );
        return self::saveMsgFormat( array(
            'RPIC' => array('replace' => $replace, 'recipients' => array($user)),
            'RPIC_SYS' => array('replace' => $replace, 'recipients' => array(array('id' => User::getSysId())))));
    }
}

?>
