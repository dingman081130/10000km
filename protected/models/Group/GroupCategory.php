<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.GroupCategoryAR');

/**
 * Description of GroupCategory
 *
 * @author yemin
 */
class GroupCategory {
    //put your code here
    
    
    const GROUP_CATEGORY_CACHE_KEY = 'group_category_map';
    
    /**
     * 获取一个group category name
     * 
     * @param integer $id category id
     * @return string 成功时返回string，否则返回null
     */
    public static function getCategoryName($id) {
        $redis = RedisClient::getClient();
        if ($redis->exists(self::GROUP_CATEGORY_CACHE_KEY)) {
            $name = $redis->hGet(self::GROUP_CATEGORY_CACHE_KEY, $id);
            return $name ? $name : null;
        } else {
            $map = self::getAllCategories();
            if (array_key_exists($id, $map)) {
                return $map[$id];
            } else {
                return null;
            }
        }
    }
    
    /**
     * 获取所有group category
     * 
     * @return array  array('1'=>'group1 name', '2'=>'group2 name')
     */
    public static function getAllCategories() {
        $redis = RedisClient::getClient();
        if ($redis->exists(self::GROUP_CATEGORY_CACHE_KEY)) {
            return $redis->hGetAll(self::GROUP_CATEGORY_CACHE_KEY);
        } else {
            $arr = Yii::app()->db->createCommand()
                    ->select('*')
                    ->from('group_category')
                    ->queryAll();
            $map = array();
            foreach ($arr as $one) {
                $map[$one['id']] = $one['name'];
            }
            $redis->hMset(self::GROUP_CATEGORY_CACHE_KEY, $map);
            return $map;
        }
    }
}

?>
