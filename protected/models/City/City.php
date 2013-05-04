<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.CityAR');
Yii::import('application.models.User.User');

/**
 * Description of City
 *
 * @author yemin
 */
class City {
    //put your code here

    const CITY_MAP_KEY = 'city_map';
    const CITY_MAP_TTL = 86400; // 1 day

    /**
     * 根据city_id查找对应的city
     * 
     * @param int $id  city id
     * @return array 
     */

    public static function getCity($id) {
        if (!$id) return null;
        
        $redis = RedisClient::getClient();
        $city = $redis->hGet(self::CITY_MAP_KEY, $id);
        if ($city === false) {
            self::setCityCache();
        }
        
        $city = $redis->hGet(self::CITY_MAP_KEY, $id);
        
        if ($city) {
            $up_city = null;
            if ($city['upid']) {
                $up_city = $redis->hGet(self::CITY_MAP_KEY, $city['upid']);
            }

            $city['up_city'] = $up_city;
        }
        return $city;
    }

    /**
     * 获取所有city
     * 
     * @return array all cities
     */
    public static function getAllCities() {
        $redis = RedisClient::getClient();
        $cities = $redis->hVals(self::CITY_MAP_KEY);
        if (empty($cities)) {
            self::setCityCache();
            $cities = $redis->hVals(self::CITY_MAP_KEY);
        }
        return $cities;
    }

    /**
     * 返回一个城市的下一级城市
     * 
     * @param integer $id
     * @return array 子城市
     */
    public static function getChildCities($id) {
        return Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('city')
                        ->where('upid = :upid', array(':upid' => $id))
                        ->order('id')
                        ->queryAll();
    }

    public static function cityAutocomplete($query) {
        $query = strtr($query, array(
            '%' => '\%',
            '_' => '\_',
                ));
        return Yii::app()->db->createCommand()
                        ->select('name')
                        ->from('city')
                        ->where('name like :query', array(':query' => "%$query%"))
                        ->queryColumn();
    }
    
    
    public static function getCityByPinyin($pinyin) {
        return Yii::app()->db->createCommand()
                ->select('*')
                ->from('city')
                ->where('pinyin = :pinyin', array(
                    ':pinyin' => $pinyin,
                ))
                ->queryRow();
    }
    
    

    /**
     * 从数据库中取出所有city存入缓存
     */
    private static function setCityCache() {
        $cities = CityAR::model()->findAll();
        $hashKeys = array();
        foreach ($cities as $city) {
            $hashKeys[$city['id']] = $city->attributes;
        }
        RedisClient::getClient()->hMset(self::CITY_MAP_KEY, $hashKeys);
    }

}

?>
