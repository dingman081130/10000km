<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.AlbumAR');
Yii::import('application.models.AR.PhotoAR');
Yii::import('application.models.Album.Photo');

/**
 * Description of Album
 *
 * @author yemin
 */
class Album {
    //put your code here

    const TTL = 3600;

    /**
     * 获取用户的照片总数
     * 
     * @param integer $uid
     * @return integer
     */
    public static function getUserPhotoCount($uid) {
        $albums = self::getUserAlbums($uid);
        $cnt = 0;
        foreach ($albums as $album) {
            $cnt += $album['photo_count'];
        }
        return $cnt;
    }

    /**
     * 返回一个用户的所有相册
     * 
     * @param integer $uid 用户id
     * @return array 用户的所有数组，array(album1, album2,.....)
     */
    public static function getUserAlbums($uid) {
        $album_ids = Yii::app()->db->createCommand()
                ->select('id')
                ->from('album')
                ->where('user_id = :uid and deleted = 0', array(':uid' => $uid))
                ->order('create_time desc')
                ->queryColumn();
        $ret = array();
        foreach ($album_ids as $aid) {
            $ret[] = self::getAlbum($aid);
        }
        return $ret;
    }

    /**
     * 获取一个相册的基本信息
     * 
     * @param integer $id 相册id
     * @return array 找到相册则返回相册基本信息，否则返回null
     */
    public static function getAlbum($id) {
        $redis = RedisClient::getClient();
        $key = self::getAlbumCacheKey($id);

        $album = $redis->get($key);
        if ($album === false) {
            $album_obj = AlbumAR::model()->find('id = :id and deleted = 0', array(
                ':id' => $id,
                    ));
            if ($album_obj) {
                $album = $album_obj->attributes;
                $photo_count = PhotoAR::model()->count('album_id = :album_id and deleted = 0', array(
                    ':album_id' => $id,
                        ));
                $update_time = Yii::app()->db->createCommand()
                        ->select('max(create_time)')
                        ->from('photo')
                        ->where('album_id = :album_id', array(':album_id' => $id))
                        ->queryScalar();
                if (!$update_time) {
                    $update_time = $album_obj->create_time;
                }
                $album['update_time'] = $update_time;
                $album['photo_count'] = $photo_count;
                if ($album['cover'] && ($photo = Photo::getPhoto($album['cover']))) {
                    $album['cover_surl'] = $photo['surl'];
                    $album['cover_ourl'] = $photo['ourl'];
                } else {
                    $album['cover_surl'] = ImageUrlHelper::imgUrl(ImageUrlHelper::PHOTO_SMALL, 'no_photo.png');;
                    $album['cover_ourl'] = ImageUrlHelper::imgUrl(ImageUrlHelper::PHOTO_SMALL, 'no_photo.png');;
                }
                $redis->setex($key, self::TTL, $album);
                return $album;
            }
            return null;
        }
        return $album;
    }

    /**
     * 获取一个相册的所有照片
     * 
     * @param integer $id 相册id
     * @return array 照片array，示例：array(photo1, photo2,......)
     */
    public static function getAlbumPhotos($id) {
        $key = self::getAlbumPhotoCacheKey($id);
        $redis = RedisClient::getClient();
        if ($redis->exists($key)) {
            $photo_ids = $redis->lRange($key, 0, -1);
        } else {
            $photo_ids = Yii::app()->db->createCommand()
                    ->select('photo.id')
                    ->from('photo')
                    ->where('photo.album_id = :id and photo.deleted != 1', array(':id' => $id))
                    ->order('photo.create_time desc')
                    ->queryColumn();
            $multi = $redis->multi();
            foreach ($photo_ids as $photo_id) {
                $multi->rPush($key, $photo_id);
            }
            $multi->exec();
            $redis->setTimeout($key, self::TTL);
        }


        $photos = array();
        foreach ($photo_ids as $photo_id) {
            $photos[] = Photo::getPhoto($photo_id);
        }
        return $photos;
    }

    /**
     * 相册照片的缓存key
     * 
     * @param integer $id
     * @return string
     */
    public static function getAlbumPhotoCacheKey($id) {
        return "album_{$id}_photos";
    }

    /**
     * 删除一个相册的缓存
     * 
     * @param integer $id
     */
    public static function delAlbumCache($id) {
        RedisClient::getClient()->del(
                self::getAlbumCacheKey($id), self::getAlbumPhotoCacheKey($id));
    }

    /**
     * 相册缓存的key
     * 
     * @param integer $id
     * @return string
     */
    public static function getAlbumCacheKey($id) {
        return "album_$id";
    }

    /**
     * 判断一个相册是否属于某用户
     * 
     * @param integer $album_id 相册id
     * @param integer $uid 用户id
     * @return boolean
     */
    public static function albumBelongToUser($album_id, $uid) {
        return AlbumAR::model()->exists('id = :id and user_id = :user_id', array(
                    ':id' => $album_id,
                    ':user_id' => $uid,
                ));
    }

    /**
     * 设置相册封面
     * 
     * @param mixed $p 图片 可以传id也可以传数组
     * @return array 封面图片信息
     */
    public static function setCover($p) {
        if (!is_array($p)) {
            $p = Photo::getPhoto($p);
        }
        if (!$p) {
            return FALSE;
        }
        $aid = $p['album_id'];
        $ar = AlbumAR::model()->findbyPK($aid);
        if (!$aid) {
            return FALSE;
        }
        $ar->cover = $p['id'];
        if (!$ar->save()) {
            return FALSE;
        }
        self::delAlbumCache($aid);
        return $p;
    }
    
    /**
     * 设置相册封面为空
     */
    public static function unsetCover($aid){
        $ar = AlbumAR::model()->findbyPK($aid);
        if (!$aid) {
            return FALSE;
        }
        $ar->cover = 0;
        if (!$ar->save()) {
            return FALSE;
        }
        self::delAlbumCache($aid);
    }
    
    /**
     * 相册是否有封面
     * 
     * @param int $aid
     * @return bool
     */
    public static function hasCover($aid){
        $ar = AlbumAR::model()->findByPK($aid);
        if(!$ar){
            return FALSE;
        }
        if(!$ar->cover){
            return FALSE;
        }
        return TRUE;
    }

}

?>
