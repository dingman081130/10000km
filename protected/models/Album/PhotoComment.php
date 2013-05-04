<?php
/**
 * @file class PhotoComment 图片评论
 * 主要针对PhotoComment表提供一些get/set方法
 * 以及与缓存打交道
 * @package application.models.Album
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */

Yii::import('application.models.AR.PhotoCommentAR');
Yii::import('application.models.User.User');
Yii::import('application.models.Event.EventListener');
Yii::import('application.models.Message.SysMessage');

class PhotoComment{
    
    /**
     * 获取照片评论
     * 
     * @param int $pid 照片id
     * @return array
     */
    public static function getComment($pid, $offset = 0, $limit = 0){
        $cri = new  CDbCriteria(array(
            'condition' => 'deleted=0 and photo_id='.intval($pid),
            'order'     => 'id desc',
            'offset'    => intval($offset)));
        if($limit){
            $cri->limit = $limit;
        }
        $ars = PhotoCommentAR::model()->findAll($cri);
        $ret = array();
        foreach($ars as $ar){
            $ret[] = $ar->getAttributes();
        }
        return $ret;
    }
    
    /**
     * 获取某一条评论
     * @param int $cid
     * @return array
     */
    public static function getCommentById($cid){
        $ar = PhotoCommentAR::model()->find('id=? and deleted=0', array($cid));
        return $ar->getAttributes();
    }
    
    /**
     * 获取照片评论数量
     * 
     * @param int $pid
     * @return int
     */
    public static function getCommentCnt($pid){
        return PhotoCommentAR::model()->count('deleted=0 and photo_id='.intval($pid));
    }
    
    /**
     * 新增照片评论
     * 
     * @param int $pid 照片id
     * @param int $uid 回复用户id
     * @param string $comment 评论内容
     * @param int $ruid ＝ 0 被回复者的id
     * @return PhotoComment::getAttributes(), false when fail
     */
    public static function addComment($pid, $uid, $content, $ruid=0){
        self::isReplyerValid($content, $ruid) || $ruid = 0;
        
        $ar = new PhotoCommentAR;
        $ar->user_id = $uid;
        $ar->photo_id = $pid;
        $ar->content = $content;
        $ar->reply_user_id = $ruid;
        
        $ret = $ar->saveL();
        if(!$ret){
            return false;
        }
        Photo::delPhotoCache($pid); //因为缓存中有评论数量，所以要清缓存
        $cid = $ar->id;
        //EventListener::getListener()->runRePhoto($pid, $uid, $content, $ruid, $cid);
        SysMessage::saveRpicMsg(Yii::app()->user->id, $pid);
        SysMessage::saveRpiccMsg(Yii::app()->user->id, $ruid, $pid);
        return $ar->getAttributes();
    }
    
    /**
     * 判断回复的内容中回复者是否有效
     * 
     * @param string $content 回复的内容
     * @param int $ruid 被回复者得id
     * @return bool
     */
    private static function isReplyerValid($content, $ruid){
        if($ruid <= 0){
            return FALSE;
        }
        $rname = self::getReplyerName($content);
        $reply = User::getUser($ruid);
        if(!$reply){
            return FALSE;
        }
        $user = User::getUser($ruid);
        if(!$user){
            return FALSE;
        }
        return $rname === $user['name'];
        
    }
    
    /**
     * 从回复内容中提取出回复者的名字
     * 
     * @param string $content
     * @return string
     */
    private static function getReplyerName($content){
        $reg = '/^回复([^:]+):/';
        $content = trim($content);
        $matches = array();
        preg_match($reg, $content, $matches);
        if(isset($matches[1])){
            return $matches[1];
        }
        return NULL;
    }
    
    /**
     * 删除一张图片的评论信息
     * 
     * @param int $pid
     */
    public static function delComment($pid){
        $ars = PhotoCommentAR::model()->findAll('photo_id = ? and deleted = 0', array($pid));
        foreach($ars as $ar){
            $ar->deleted = 1;
            $ar->save();
        }
    }
}
