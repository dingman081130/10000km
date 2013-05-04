<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.GroupAR');
Yii::import('application.models.AR.GroupUserAR');

/**
 * Description of Group
 *
 * @author yemin
 */
class Group {
    //put your code here
    
    const TTL = 3600;
    
    
    private static function groupCacheKey($id) {
        return "group_$id";
    }
    
    public static function delGroupCache($id) {
        RedisClient::getClient()->del(self::groupCacheKey($id));
    }
    
    /**
     * 获取一个group
     * 
     * @param integer $id  group id
     * @return array 成功时返回array，否则返回null
     */
    public static function getGroup($id) {
        $redis = RedisClient::getClient();
        $key = self::groupCacheKey($id);
        $group = $redis->get($key);
        if (!$group) {
            $group = Yii::app()->db->createCommand()
                    ->select('`group`.*, count(distinct group_user.user_id) as user_count, count(distinct topic.id) as topic_count')
                    ->from('group')
                    ->leftJoin('group_user', 'group_user.group_id = group.id and group_user.deleted = 0')
                    ->leftJoin('topic', 'topic.group_id = group.id and topic.deleted = 0')
                    ->where('group.id = :id and group.deleted = 0', array(':id' => $id))
                    ->group('group.id')
                    ->queryRow();
            
            if ($group) {
                $redis->setex($key, self::TTL, $group);
                return $group;
            } else {
                return null;
            }
        }
        
        return $group;
    }
    
    
    public static function getGroups($ids) {
        $data = array();
        foreach ($ids as $id) {
            $data[] = self::getGroup($id);
        }
        return $data;
    }
    
    /**
     * 
     * @param integer $cid category id
     * @param integer $offset
     * @param integer $size
     * @return array
     */
    public static function getGroupsByCategory($cid, $offset=0, $size=10) {
        return Yii::app()->db->createCommand()
                ->select('*')
                ->from('group')
                ->where('category_id = :cid and deleted = 0', array(':cid' => $cid))
                ->offset($offset)
                ->limit($size)
                ->queryAll();
    }
    
    
    /**
     * 获取一个城市的group
     * 
     * @param integer $city_id
     * @param integer $offset
     * @param integer $size
     * @return array
     */
    public static function getGroupsByCity($city_id, $offset=0, $size=10) {
        $ids = Yii::app()->db->createCommand()
                ->select('group.id')
                ->from('group')
                ->join('city', 'city.id = group.city_id')
                ->where('(city.id = :city_id or city.upid = :city_id) and deleted = 0', array(':city_id' => $city_id))
                ->offset($offset)
                ->limit($size)
                ->queryColumn();
        return self::getGroups($ids);
    }
    
    /**
     * 获取用户创建的小组
     * 
     * @param integer $uid
     * @return array
     */
    public static function getGroupsCreatedByUser($uid) {
        $ids = Yii::app()->db->createCommand()
                ->select('id')
                ->from('group')
                ->where('creator_id = :uid deleted = 0', array(':uid' => $uid))
                ->order('create_time desc')
                ->queryColumn();
        return self::getGroups($ids);
    }
    
    
    /**
     * 检索组信息
     * 按创建时间倒序排序
     * 
     * @param string $kw ＝ ‘’   //// 按关键字检索
     * @param int $cat ＝ 0      //// 按类型检索
     * @param int $city ＝ 0     //// 按城市检索
     * @param int $offset ＝ 0   //// 偏移量
     * $param int $limit ＝ 0    //// 每页长度
     */
    public static function search ($kw = '', $cat = 0, $city = 0, $offset = 0, $limit = 0) {
        $limit <= 0 && $limit = Yii::app()->params['groupPageSize'];
        $cmd = Yii::app()->db->createCommand();
        $cmd->select('id')->from('group')->where('deleted = 0')->order('create_time desc')->offset($offset)->limit($limit);
        //print_r($kw);exit;
        if ($kw) {
            $kw = mysql_real_escape_string($kw);
            $cmd->andWhere(
                    "name like '%{$kw}%' or description like '%{$kw}%' "
                    );
        }
        if ($cat) {
            $cmd->andWhere('category_id = :cat_id', array_merge($cmd->params, array(':cat_id' => $cat)));
        }
        if ($city) {
            $cmd->addWhere('city_id = :city_id', array_merge($cmd->params, array(':city_id' => $city)));
        }
        $data = $cmd->queryAll();
        $ret = array();
        foreach($data as $one){
            $ret[$one['id']] = self::getGroup($one['id']);
        }
        //print_r($ret);exit;
        return $ret;
    }
    
    /**
     * 检索组信息的数量 策略需和self::search()方法保持一致
     * 
     * @param string $kw ＝ ‘’   //// 按关键字检索
     * @param int $cat ＝ 0      //// 按类型检索
     * @param int $city ＝ 0     //// 按城市检索
     */
    public static function searchCnt ($kw = '', $cat = 0, $city = 0) {
        $cmd = Yii::app()->db->createCommand();
        $cmd->select('count(*) as cnt')->from('group')->where('deleted = 0');
        if ($kw) {
            $kw = mysql_real_escape_string($kw);
            $cmd->andWhere(
                    "name like '%{$kw}%' or description like '%{$kw}%' "
                    );
        }
        if ($cat) {
            $cmd->andWhere('category_id = :cat_id', array_merge($cmd->params, array(':cat_id' => $cat)));
        }
        if ($city) {
            $cmd->addWhere('city_id = :city_id', array_merge($cmd->params, array(':city_id' => $city)));
        }
        $data = $cmd->queryAll();
        return $data[0]['cnt'];
    }
    
    /**
     * 获取最热的组信息
     * 
     * @param int limit = 12 取12条记录
     */
    public static function getHotGroups( $limit = 12 ){
        
        $sql = 'select g.*, count(tc.id) as cnt ';
        $sql.= 'from `group` as g ';
        $sql.= 'left join topic as t ';
        $sql.= 'on t.deleted = 0 and t.group_id = g.id ';
        $sql.= 'left join topic_comment as tc ';
        $sql.= 'on tc.deleted = 0 and tc.topic_id = t.id ';
        $sql.= 'where g.deleted = 0 ';
        $sql.= 'group by g.id ';
        
        $cmd = Yii::app()->db->createCommand($sql);
        $data = $cmd->queryAll();
        //print_r($data);exit;
        $ret = array();
        
        for ( $i = 0; $i < $limit; $i ++ ) {
            $max_index = -1;$max_cnt = -1;
            foreach ($data as $index => $one) {
                if ($one['cnt'] > $max_cnt) {
                    $max_cnt   = $one['cnt'];
                    $max_index = $index;
                } else if ($one['cnt'] == $max_cnt) {
                    if ($one['create_time'] > $data[$max_index]['create_time']) {
                        $max_cnt   = $one['cnt'];
                        $max_index = $index;
                    }
                }
            }
            if (isset($data[$max_index])) {
                $ret[$max_index] = $data[$max_index];
                unset($data[$max_index]);
            }
        }
        return $ret;
    }
    
    
}

?>
