<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.PhotoAR');
Yii::import('application.models.Album.Album');
Yii::import('application.models.Album.PhotoComment');

/**
 * Description of Photo
 *
 * @author yemin
 */
class Photo {
    //put your code here
    
    const P_PRE = 'P_';
    const TTL = 3600;
    
    /**
     * 返回照片属性
     * 
     * @param int $album_id 相册id
     * @param string $filename 图片文件名
     * @param string $title 图片说明
     * @return array
     */
    public static function savePhoto($album_id, $filename, $title) {
        $photo = new PhotoAR;
        $photo->album_id = $album_id;
        $photo->img = $filename;
        $photo->title = $title;
        $photo->save();
        Album::delAlbumCache($album_id);
        return $photo->getAttributes();
    }
    
    /**
     * 获取大于等于某个时间点的图片
     * @param datetime $time
     * @return array
     */
    public static function getPhotoByTimeGE( $time ){
        $pars = PhotoAR::model()->findAll('create_time >= ? and deleted = 0', array($time));
        $ret = array();
        foreach($pars as $par){
            $album_id = $par->album_id;
            $photo_id = $par->id;
            $ret[$album_id]['photo'][$photo_id] = $par->getAttributes();
            isset($ret[$album_id]['album']) || $ret[$album_id]['album'] = Album::getAlbum($album_id);
        }
        return $ret;
    }
    
    /**
     * 获取图片信息
     * 
     * @param int $pid
     * @return array
     */
    public static function getPhoto($pid){
        $p = self::getPhotoRedis($pid);
        //// 如果缓存中能拿到没有删除的图片信息则直接返回
        if($p && !$p['deleted']){
            return $p;
        }
        $p = self::getPhotoDB($pid);
        if(!$p){
            self::delPhotoCache($pid);
        }else{
            $p['surl'] = ImageUrlHelper::imgUrl(ImageUrlHelper::PHOTO_SMALL, $p['img']);
            $p['ourl'] = ImageUrlHelper::imgUrl(ImageUrlHelper::PHOTO_ORIG, $p['img']);
            $p['comment_count'] = PhotoComment::getCommentCnt($pid);
            self::setPhotoRedis($pid, $p);
        }
        return $p;
    }
    
    /**
     * 从redis获取图片信息
     * @param int $pid
     * @return array
     */
    private static function getPhotoRedis($pid){
        $key = self::getKey($pid);
        $ret = RedisClient::getClient()->get($key);
        return $ret;
    }
    
    private static function getPhotoDB($pid){
        $ar = PhotoAR::model()->findByPk($pid, 'deleted = 0');
        if(!$ar){
            return NULL;
        }
        return $ar->getAttributes();
    }

    private static function setPhotoRedis($pid, $attributes){
        $key = self::getKey($pid);
        RedisClient::getClient()->setex($key, self::TTL, $attributes);
    }

    private static function getKey($pid){
        return self::P_PRE.$pid;
    }
    
    /**
     * 找到图片的所有者
     * @param int $pid
     * @return int
     */
    public static function getOwner($pid){
        $p = self::getPhoto($pid);
        if(!$p){
            return FALSE;
        }
        $aid = $p['album_id'];
        $a = Album::getAlbum($aid);
        if(!$a){
            return FALSE;
        }
        return $a['user_id'];
    }
    
    /**
     * 获取图片所在相册的下一张照片
     * @param int $p
     * @param bool $cyc 是否循环找寻
     * @return array
     */
    public static function getNextPhoto($p, $cyc = FALSE){
        if(!is_array($p)){
            $p = self::getPhoto($p);
        }
        $cri = new CDbCriteria;
        $cri->condition = 'id<:pid and album_id=:aid and deleted=0';
        $cri->order = 'id desc';
        $cri->limit = 1;
        $cri->params = array(':pid'=>$p['id'], ':aid'=>$p['album_id']);
        
        $ar = PhotoAR::model()->find($cri);
        if($ar){
            return Photo::getPhoto($ar->id);
        }
        if($cyc){
            return self::getFirstPhoto($p['album_id']);
        }
        return array();
    }
    
    /**
     * 获取图片所在相册的上一张照片
     * @param int $p
     * @param bool $cyc 是否循环找寻
     * @return array
     */
    public static function getPrevPhoto($p, $cyc = FALSE){
        if(!is_array($p)){
            $p = self::getPhoto($p);
        }
        $cri = new CDbCriteria;
        $cri->condition = 'id>:pid and album_id=:aid and deleted=0';
        $cri->order = 'id asc';
        $cri->limit = 1;
        $cri->params = array(':pid'=>$p['id'], ':aid'=>$p['album_id']);
        
        $ar = PhotoAR::model()->find($cri);
        if($ar){
            return Photo::getPhoto($ar->id);
        }
        if($cyc){
            return self::getLastPhoto($p['album_id']);
        }
        return array();
    }
    
    /**
     * 获取相册的第一张图片
     * @param int $aid
     * @return array
     */
    private static function getFirstPhoto($aid){
        $cri = new CDbCriteria;
        $cri->condition = 'album_id=:aid and deleted=0';
        $cri->order = 'id desc';
        $cri->limit = 1;
        $cri->params = array(':aid'=>$aid);
        
        $ar = PhotoAR::model()->find($cri);
        if($ar){
            return Photo::getPhoto($ar->id);
        }
        return array();
    }
    
    /**
     * 获取相册的最后一张图片
     * @param int $aid
     * @return array
     */
    private static function getLastPhoto($aid){
        $cri = new CDbCriteria;
        $cri->condition = 'album_id=:aid and deleted=0';
        $cri->order = 'id asc';
        $cri->limit = 1;
        $cri->params = array(':aid'=>$aid);
        
        $ar = PhotoAR::model()->find($cri);
        if($ar){
            return Photo::getPhoto($ar->id);
        }
        return array();
    }
    
    /**
     * 删除一张图片
     * @param int $pid 图片id
     * @return array 返回该图所在相册的下一张图片 没有则返回空数组
     */
    public static function delPhoto($pid){
        $p  = self::getPhoto($pid);
        $np = self::getNextPhoto($pid, TRUE); //// 循环获取下一张图片
        
        self::delPhotoCache($pid);
        self::delPhotoDB($pid);
        PhotoComment::delComment($pid);
        
        $aid = $p['album_id'];
        //// 如果图片所再的相册id不存在
        if(!$aid){
            return array();
        }
        $a = Album::getAlbum($aid);
        Album::delAlbumCache($aid);  //// 删除相册的缓存
        //// 如果所删除的不是封面
        if($a['cover'] != $pid){
            return $np['id'] == $pid ? array() : $np;
        }
        //// 重新设置封面
        $np['id'] == $pid ? Album::unsetCover($aid) : Album::setCover($np);
        //// 如果不存在下一张图片
        if(!$np || $np['id'] == $pid){
            return array();
        }
        
        return $np;
    }
    
    /**
     * 删除一张图片的Redis缓存
     * @param int $pid
     */
    public static function delPhotoCache($pid) {
        RedisClient::getClient()->del(self::getKey($pid));
    }
    
    /**
     * 删除一张图片
     * @param int $pid
     */
    private static function delPhotoDB($pid){
        $ar = PhotoAR::model()->findByPK($pid);
        if($ar){
            $ar->deleted = 1;
            $ar->save();
        }
    }
    
    /**
     * 设置图片的title
     * 
     * @param int $uid
     * @param int $pid
     * @param string $title
     * @return bool
     */
    public static function setTitle($uid, $pid, $title){
        if (!$uid || $uid != self::getOwner($pid)) {
            return false;
        }
        
        $title = trim($title);
        $ar = self::getAR($pid);
        if(!$ar){
            return FALSE;
        }
        if(trim($ar->title) == $title){
            return TRUE;
        }
        $ar->title = trim($title);
        if(!$ar->save()){
            return FALSE;
        }
        self::delPhotoCache($pid);
        return TRUE;
    }
    
    /**
     * 从数据库中获取照片ar
     * 
     * @param int $pid
     * @return PhotoAR
     */
    private static function getAR($pid){
        return PhotoAR::model()->findByPk($pid, 'deleted = 0');
    }
}
?>
