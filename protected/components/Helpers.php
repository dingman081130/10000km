<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Helpers
 *
 * @author yemin
 */
class Helpers {
    //put your code here

    /**
     * 
     * @param string $phrase 字符串的表情，如'[草泥马]'
     * @return string 返回值不能再html转义。如果存在该表情，则返回显示该表情的img标签，否则返回表情的字符串值
     */
    public static function emotionImage($phrase) {
        Yii::import('application.models.Emotion.Emotion');
        $emotion = Emotion::getEmotionByPhrase($phrase);
        if ($emotion) {
            $src = CHtml::encode(Yii::app()->params['staticBaseUrl'] . 'img/emotions/' . $emotion['filename']);
            $value = CHtml::encode($emotion['value']);
            return "<img src=\"$src\" title=\"$value\" alt=\"$value\">";
        } else {
            return CHtml::encode($phrase);
        }
    }

    /**
     * 返回友好的时间字符串
     * 
     * @param string $time 用date('Y-m-d H:i:s')生成的时间
     * @param boolean $accurate 对于很久以前的时间，是否返回精确值
     * @return string
     */
    public static function friendlyTime($time, $accurate=true) {
        
        $t = strtotime($time);
        $now = time();
        $diff = $now - $t;

        $t_parts = getdate($t);
        $now_parts = getdate($now);

        if($diff >= 0 && $diff <= 60){
            return '刚刚';
        }elseif ($diff < 3600 && $diff > 0) {
            $minutes = intval($diff / 60);
            if ($minutes == 0)
                $minutes = 1;
            return "{$minutes}分钟前";
        } else if ($t_parts['year'] == $now_parts['year'] && $t_parts['yday'] == $now_parts['yday']) {
            return date("今天 G:i", $t);
        } else if ($t_parts['year'] == $now_parts['year']) {
            if ($accurate)
                return date('n月j日 G:i', $t);
            else 
                return date('n月j日', $t);
        } else {
            if ($accurate)
                return date('Y年n月j日 G:i', $t);
            else
                return date('Y年n月j日', $t);
        }
    }
    
    /**
     * 返回一个时间与现在的时间差
     * 
     * @param string $time
     * @return string
     */
    public static function timeDelta($time) {
        $t = strtotime($time);
        $now = time();
        $diff = $now - $t;
        if ($diff < 60) return '1分钟前';
        if ($diff < 60*60) return intval($diff / 60).'分钟前';
        if ($diff < 60*60*24) return intval($diff / 3600).'小时前';
        return intval($diff / 3600 / 24).'天前';
    }

    public static function friendlyDate($time) {
        $t = strtotime($time);
        $now = time();

        $t_parts = getdate($t);
        $now_parts = getdate($now);
        
        if ($t_parts['year'] == $now_parts['year'] && $t_parts['yday'] == $now_parts['yday']) {
            return '今天';
        }
        if ($t_parts['year'] == $now_parts['year']) {
            return date('n月j日', $t);
        }
        return date('Y年n月j日', $t);
    }

    /**
     * 生成csrf input标签
     * 
     * @return string 隐藏的csrf input标签代码
     */
    public static function csrfInput() {
        $name = Yii::app()->request->csrfTokenName;
        $value = Yii::app()->request->csrfToken;
        return "<input id=\"csrf-input\" type=\"hidden\" name=\"$name\" value=\"$value\">";
    }

    /**
     * 根据生日计算年龄
     * 
     * @param string $birthday 生日，如'1990-12-21'
     * @return integer 年龄
     */
    public static function ageFromBirthday($birthday) {
        $date = new DateTime($birthday);
        $now = new DateTime();
        $interval = $now->diff($date);
        return $interval->y;
    }

    /**
     * 
     * @param integer $city_id 城市id
     * @param boolean $upcity 返回的字符串中是否要包含上一级城市，默认为true
     * @return string 成功时返回string，否则返回null
     */
    public static function cityName($city_id, $upcity=true) {
        Yii::import('application.models.City.City');
        $ret = null;
        $city = City::getCity($city_id);
        if ($city) {
            if ($upcity && $city['up_city']) {
                $ret = $city['up_city']['name']. ' ' . $city['name'];
            } else {
                $ret = $city['name'];
            }
        }
        return $ret;
    }

    public static function substr($str, $len, $dot=true) {
        if (mb_strlen($str, 'utf-8') <= $len ) {
            return $str;
        } else {
            $ret = mb_substr($str, 0, $len, 'utf-8');
            if ($dot)
                $ret .= '...';
            return $ret;
        }
    }

}

?>
