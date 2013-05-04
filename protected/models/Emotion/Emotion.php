<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.EmotionAR');

/**
 * Description of Emotion
 *
 * @author yemin
 */
class Emotion {
    //put your code here

    const EMOTION_MAP_KEY = 'emotion_map';
    const EMOTION_MAP_TTL = 86400; // 1 day

    /**
     * 返回所有表情
     * 
     * @return array
     */
    public static function getAllEmotions() {
        $redis = RedisClient::getClient();
        $ret = $redis->hVals(self::EMOTION_MAP_KEY);
        if (empty($ret)) {
            self::setEmotionCache();
            $ret = $redis->hVals(self::EMOTION_MAP_KEY);
        }
        return $ret;
    }

    /**
     * 
     * @param type $phrase
     * @return type
     */
    private static function getEmotionByPhrase($phrase) {
        $redis = RedisClient::getClient();
        $emotion = $redis->hGet(self::EMOTION_MAP_KEY, $phrase);
        if ($emotion === false) {
            self::setEmotionCache();
            $emotion = $redis->hGet(self::EMOTION_MAP_KEY, $phrase);
        }
        return $emotion === false ? null : $emotion;
    }

    
    /**
     * 从数据库中取出所有表情存入缓存
     */
    private static function setEmotionCache() {
        $emotions = Yii::app()->db->createCommand()
                ->select('*')
                ->from('emotion')
                ->where('deleted = 0')
                ->order('id')
                ->queryAll();
        $hashKeys = array();
        foreach ($emotions as $emotion) {
            $hashKeys[$emotion['phrase']] = $emotion;
        }
        RedisClient::getClient()->hMset(self::EMOTION_MAP_KEY, $hashKeys);
    }
    
    /**
     * 输入一个串，将串中的表情符号替换为<img>标签
     * @param string $str
     * @return string
     */
    public static function replaceEmotion($str){
        preg_match_all('/(\[[^\]]+\])/', $str, $matches);
        $phrases = array_unique($matches[1]);
        foreach ($phrases as $phrase) {
            $str = str_replace($phrase, self::emotionImage($phrase), $str);
        }
        return $str;
    }

    private static function emotionImage($phrase) {
        $emotion = Emotion::getEmotionByPhrase($phrase);
        if ($emotion) {
            $src = Yii::app()->params['staticBaseUrl'] . 'img/emotions/' . $emotion['filename'];
            $value = $emotion['value'];
            return "<img src=\"$src\" title=\"$value\" alt=\"$value\">";
        } else {
            return CHtml::encode($phrase);
        }
    }
}

?>
