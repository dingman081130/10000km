<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.WayAR');

/**
 * Description of Way
 *
 * @author yemin
 */
class Way {
    //put your code here

    const WAY_MAP_KEY = 'way_map';
    const WAY_MAP_TTL = 86400; // 1 day

    /**
     * 返回旅行方式的显示名称
     * 
     * @param mixed $id  
     * @return mixed $id为数组时，返回数组，array('id1' => 'value1', 'id2' => 'value2')；否则返回单个名称
     */

    public static function getWayName($id) {
        $redis = RedisClient::getClient();
        if (!$redis->exists(self::WAY_MAP_KEY)) {
            self::setWayCache();
        }

        if (is_array($id)) {
            return $redis->hMGet(self::WAY_MAP_KEY, $id);
        } else {
            $name = $redis->hGet(self::WAY_MAP_KEY, $id);
            return ($name === false) ? null : $name;
        }
    }

    public static function getAllWays() {
        $redis = RedisClient::getClient();
        if (!$redis->exists(self::WAY_MAP_KEY)) {
            self::setWayCache();
        }
        $map = $redis->hGetAll(self::WAY_MAP_KEY);
        $ret = array();
        foreach ($map as $k=>$v) {
            $ret[] = array(
                'id' => $k,
                'name' => $v,
                );
        }
        sort($ret);
        return $ret;
        //$command = Yii::app()->db->createCommand('select id, name from way order by id;');
        //return $command->queryAll();
    }

    private static function setWayCache() {
        $ways = WayAR::model()->findAll();
        $hashKeys = array();
        foreach ($ways as $way) {
            $hashKeys[$way['id']] = $way['name'];
        }
        RedisClient::getClient()->hMset(self::WAY_MAP_KEY, $hashKeys);
    }

}

?>
