<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ImageUrlHelper
 *
 * @author yemin
 */
class ImageUrlHelper {
    //put your code here
    
    const AVATAR_TINY = 'image/avatar/t/'; // 40x40
    const AVATAR_SMALL = 'image/avatar/s/'; // 60x60
    const AVATAR_MIDDLE = 'image/avatar/m/'; // 90x90
    const AVATAR_LARGE = 'image/avatar/l/'; // 180x180
    
    
    const TRIP_COVER_SMALL = 'image/trip_cover/s/'; // 120x90
    const TRIP_COVER_MIDDLE = 'image/trip_cover/m/'; // 220x165
    const TRIP_COVER_ORIG = 'image/trip_cover/o/'; // 原始图片
    
    const TRIP_IMAGE = 'image/trip/';
    
    
    const PHOTO_SMALL = 'image/photo/s/'; // 200x200
    const PHOTO_ORIG = 'image/photo/o/'; // 原始图片
    
    const GROUP_IMAGE = 'image/group/';
    
    const TOPIC_IMAGE = 'image/topic/';
    
    /**
     * 返回图片的绝对url
     * 
     * @param string $type
     * @param string $filename 文件名
     * @return string image url
     */
    public static function imgUrl($type, $filename) {
        return Yii::app()->params['staticBaseUrl'] . $type . $filename;
    }
    
    /**
     * 
     * @param string $type
     * @param string $filename 文件名
     * @return string
     */
    public static function imgPath($type, $filename) {
        return $type . $filename;
    }
}

?>
