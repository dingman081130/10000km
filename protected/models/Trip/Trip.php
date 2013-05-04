<?php

/**
 * @file class Trip
 * @package application.models.Trip
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-14
 * @version
 */
Yii::import('application.models.AR.TripAR');
Yii::import('application.models.AR.TripDstAR');
Yii::import('application.models.User.User');
Yii::import('application.models.AR.TripCommentAR');
Yii::import('application.models.AR.UserTripJoinAR');
Yii::import('application.models.Event.Event');
Yii::import('application.models.Event.EventListener');

class Trip {

    const TTL = 3600;

    /**
     * 根据旅程id列表获取旅程的基本信息
     *
     * @param array $id_arr
     * @return array
     */
    public static function getTripBasicInfoByIdArr($id_arr) {

        $criteria = new CDbCriteria;
        $criteria->alias = 't';
        $criteria->select = 't.*';
        $criteria->addInCondition('t.id', $id_arr, 'OR');
        $criteria->order = 't.start_date asc';

        $trip_obj_arr = TripAR::model()->findAll($criteria);
        $trip_dst_arr = self::getTripDestByIdArr($id_arr);
        $trip_frm_arr = self::getTripFromByIdArr($id_arr);
        $trip_way_arr = self::getTripWayByIdArr($id_arr);
        $trip_arr = array();
        $user_id_arr = array();
        //// 数据组合
        foreach ($trip_obj_arr as $trip_obj) {
            $trip_id = $trip_obj->id;
            $trip_arr[$trip_id] = $trip_obj->getAttributes();
            $user_id_arr[$trip_obj->creator_id] = $trip_obj->creator_id;
            $trip_arr[$trip_id]['dst'] = isset($trip_dst_arr[$trip_id]) ? $trip_dst_arr[$trip_id] : $trip_arr[$trip_id]['dst'] = array();
            $trip_arr[$trip_id]['frm'] = isset($trip_frm_arr[$trip_id]) ? $trip_frm_arr[$trip_id] : $trip_arr[$trip_id]['frm'] = array();
            $trip_arr[$trip_id]['way'] = isset($trip_way_arr[$trip_id]) ? $trip_way_arr[$trip_id] : $trip_arr[$trip_id]['way'] = array();
        }

        $user_arr = User::getNameById($user_id_arr);
        foreach ($trip_arr as &$one) {
            $one['creator_name'] = isset($user_arr[$one['creator_id']]) ? $user_arr[$one['creator_id']] : '';
        }
//print_r($trip_arr);exit; // debug
        return $trip_arr;
    }

    /**
     * 根据旅程id列表获取旅程的目的地
     * 根据旅程id进行分组
     *
     * @param array $id_arr
     * @return array e.g.
     * <code>
     * array(
     *      7 => array( '拉萨','新疆'),
     * )
     * </code>
     *
     */
    private static function getTripDestByIdArr($id_arr) {

        $criteria = new CDbCriteria;
        $criteria->alias = 't';
        $criteria->select = 't.*';
        $criteria->addInCondition('t.trip_id', $id_arr, 'OR');

        $dest_obj = TripDstAR::model()->findAll($criteria);
        $trip_dst = array();
        foreach ($dest_obj as $one) {
            $trip_dst[$one->trip_id][] = $one->dst_name;
        }
        return $trip_dst;
    }

    /**
     * 根据旅程id列表获取旅程的出发地
     *
     * @param array $id_arr
     * @return array e.g.
     * <code>
     * array(
     *      7 => array( 'cid' => 110000, 'cname' => '北京市' )
     * )
     * </code>
     */
    private static function getTripFromByIdArr($id_arr) {
        $id_arr[] = 0;
        // 尼玛，我真的不想裸写sql
        $sql = 'select t.id as tid, c.id as cid, c.name as cname ';
        $sql.= 'from trip t left join city c ';
        $sql.= 'on t.from_city = c.id ';
        $sql.= 'where t.id in ( ' . join(',', $id_arr) . ' ) ';

        $trip_city_arr = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($trip_city_arr as $one) {
            $ret[$one['tid']] = array('cid' => $one['cid'], 'cname' => $one['cname']);
        }
        return $ret;
    }

    /**
     * 根据旅程id列表获取旅程的方式
     *
     * @param array $id_arr
     * @return array e.g.
     * <code>
     * array(
     *      7 => array( // TripId
     *          6 => '丛林冒险',    // WayId => WayName
     *          5 => '登山远足',
     *      ),
     * )
     * </code>
     */
    private static function getTripWayByIdArr($id_arr) {
        $id_arr[] = 0;
        // 尼玛，又得裸写sql啊
        $sql = 'select tw.trip_id as tid, w.id as wid, w.name as wname ';
        $sql.= 'from trip_way tw left join way w ';
        $sql.= 'on tw.way_id = w.id ';
        $sql.= 'where tw.trip_id in (' . join(',', $id_arr) . ')';

        $trip_way_arr = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($trip_way_arr as $one) {
            $ret[$one['tid']][$one['wid']] = $one['wname'];
        }
        return $ret;
    }

    public static function getTrip($trip_id) {
        $key = "trip_$trip_id";
        $redis = RedisClient::getClient();
        $trip = $redis->get($key);
        if ($trip === false) {
            $trip = TripAR::model()->findByPk($trip_id);
            if ($trip) {
                $trip = $trip->attributes;
                $redis->setex($key, self::TTL, $trip);
            }
        }
        return $trip ? $trip : null;
    }

    /**
     * 根据trip_id获取该trip的所有评论
     * 
     * @param integer $trip_id
     * @return array array($comment1, $comment2, .....)
     */
    public static function getTripComments($trip_id) {
        $trip_id = intval($trip_id);
        $key = "trip_{$trip_id}_comments";
        $redis = RedisClient::getClient();
        $comments = $redis->lRange($key, 0, -1);
        if (empty($comments)) {
            $comments = Yii::app()->db->createCommand()
                    ->select('*')
                    ->from('trip_comment')
                    ->where('trip_id = :trip_id', array(':trip_id' => $trip_id))
                    ->order('create_time desc')
                    ->queryAll();
            foreach ($comments as $comment) {
                $redis->rPush($key, $comment);
            }
            $redis->expire($key, self::TTL);
        }
        return $comments;
    }
    
    public static function getTripCommentsCount($trip_id) {
        $key = "trip_{$trip_id}_comments";
        $redis = RedisClient::getClient();
        if ($redis->exists($key))
            return $redis->lLen($key);
        
        return Yii::app()->db->createCommand()
                ->select('count(id)')
                ->from('trip_comment')
                ->where('trip_id = :trip_id', array(':trip_id' => $trip_id))
                ->queryScalar();
    }

    public static function getTripWays($trip_id) {
        $trip_id = intval($trip_id);
        $key = "trip_{$trip_id}_ways";
        $redis = RedisClient::getClient();
        $ways = $redis->get($key);
        if ($ways === false) {
            Yii::import('application.models.Way.Way');

            $ways_id = Yii::app()->db->createCommand()
                    ->select('way_id')
                    ->from('trip_way')
                    ->where('trip_id = :trip_id', array(':trip_id' => $trip_id))
                    ->queryColumn();
            $ways = Way::getWayName($ways_id);
            $redis->setex($key, self::TTL, $ways);
        }
        return empty($ways) ? array() : $ways;
    }

    public static function getTripDsts($trip_id) {
        $trip_id = intval($trip_id);
        $key = "trip_{$trip_id}_dsts";
        $redis = RedisClient::getClient();
        $dsts = $redis->get($key);
        if ($dsts === false) {
            Yii::import('application.models.TripDstAR');

            $dsts = Yii::app()->db->createCommand()
                    ->select('dst_name')
                    ->from('trip_dst')
                    ->where('trip_id = :trip_id', array(':trip_id' => $trip_id))
                    ->queryColumn();
            $redis->setex($key, self::TTL, $dsts);
        }
        return empty($dsts) ? array() : $dsts;
    }

    /**
     * 查找trip
     * 
     * @param array $dsts
     * @param string $start_date
     * @param string $end_date
     * @param array $trip_ways
     * @param integer $offset
     * @param integer $size
     * @return array 返回array('trips' => $trips, 'total' => $total)
     */
    public static function searchTrips($dsts = null, $start_date = null, $end_date = null, $trip_ways = null, $offset = 0, $size = 10) {
        
        $schema = Yii::app()->db->schema;
        $dst_condition = $schema->commandBuilder->createInCondition('trip_dst', 'dst_name', $dsts);
        $trip_way_condition = $schema->commandBuilder->createInCondition('trip_way', 'way_id', $trip_ways);
        
        $command = Yii::app()->db->createCommand()
                ->select("t.id, count(case when $dst_condition then 1 else null end)+count(case when $trip_way_condition then 1 else null end) as relevance")
                ->from('trip as t')
                ->leftJoin('trip_dst', 't.id = trip_dst.trip_id')
                ->leftJoin('trip_way', 't.id = trip_way.trip_id')
                ->group('t.id');
        
        if (!empty($start_date)) {
            $command->andWhere('t.start_date = 0 OR ABS(TIMESTAMPDIFF(DAY, t.start_date, :start_date)) <= 5', array(
                ':start_date' => $start_date
            ));
        }
        if (!empty($end_date)) {
            $command->andWhere('t.start_date = 0 OR ABS(TIMESTAMPDIFF(DAY, t.end_date, :end_date)) <= 5', array(
                ':end_date' => $end_date,
            ));
        }
        
        $ids = $command->order('relevance desc, t.create_time desc')
                ->offset($offset)
                ->limit($size)
                ->queryColumn();
        
        $trips = array();
        foreach ($ids as $id) {
            $trips[] = self::getTrip ($id);
        }

        $command = Yii::app()->db->createCommand()
                ->select("count(t.id)")
                ->from('trip as t');
        
        if (!empty($start_date)) {
            $command->andWhere('t.start_date = 0 OR ABS(TIMESTAMPDIFF(DAY, t.start_date, :start_date)) <= 5', array(
                ':start_date' => $start_date
            ));
        }
        if (!empty($end_date)) {
            $command->andWhere('t.start_date = 0 OR ABS(TIMESTAMPDIFF(DAY, t.end_date, :end_date)) <= 5', array(
                ':end_date' => $end_date,
            ));
        }

        $total = $command->queryScalar();

        return array('trips' => $trips, 'total' => $total);
    }
    
    
    /**
     * 用户关注一个旅行
     * 
     * @param integer $uid 用户id
     * @param integer $tid 旅行id
     * @return boolean 成功时返回true，否则返回false
     */
    public static function followTrip($uid, $tid) {
        if (!$uid || !$tid)
            return false;
        
        $ret = UserTripFollowAR::model()->updateAll(array('deleted' => 0), 'trip_id = :tid and user_id = :uid', array(
            ':tid' => $tid,
            ':uid' => $uid,
        ));
        
        if (!$ret) {
            $follow = new UserTripFollowAR();
            $follow->trip_id = $tid;
            $follow->user_id = $uid;
            $ret = $follow->save();
        }
        if ($ret) {
            RedisClient::getClient()->del("trip_{$tid}_followers");
        }
        return $ret;
    }
    
    
    /**
     * 取消关注一个旅行
     * 
     * @param integer $uid 用户id
     * @param integer $tid trip id
     * @return boolean 成功时返回true，否则返回false
     */
    public static function unfollowTrip($uid, $tid) {
        if (!$uid || !$tid)
            return false;
        
        $ret = UserTripFollowAR::model()->updateAll(array('deleted' => 1), 'trip_id = :tid and user_id = :uid', array(
            ':tid' => $tid,
            ':uid' => $uid,
        ));
        if ($ret) {
            RedisClient::getClient()->del("trip_{$tid}_followers");
        }
        
        return $ret;
    }

    /**
     * 查询用户是否follow了一个trip
     * 
     * @param integer $uid 用户id
     * @param integer $tid trip id
     * @return boolean
     */
    public static function isUserFollowTrip($uid, $tid) {
        if (!$uid || !$tid)
            return false;

        $redis = RedisClient::getClient();
        $key = "trip_{$tid}_followers";

        if (!$redis->exists($key)) {
            $uids = self::getTripFollowers($tid);
            return in_array($uid, $uids);
        }
        
        return $redis->sIsMember($key, $uid);
    }

    /**
     * 返回关注了某trip的所有用户的id
     * 
     * @param integer $tid trip id
     * @return array 包含所有follow了该trip的用户id
     */
    public static function getTripFollowers($tid) {
        $redis = RedisClient::getClient();
        $key = "trip_{$tid}_followers";

        if (!$redis->exists($key)) {
            $uids = Yii::app()->db->createCommand()
                    ->select('user_id')
                    ->from('user_trip_follow')
                    ->where('trip_id = :trip_id and deleted = 0', array(':trip_id' => $tid))
                    ->queryColumn();
            foreach ($uids as $one)
                $redis->sAdd($key, $one);
            $redis->expire($key, self::TTL);
            return $uids;
        }

        return $redis->sMembers($key);
    }
    
    /**
     * 获取关注了某个trip的用户数
     * 
     * @param integer $tid
     * @return integer
     */
    public static function getTripFollowersCount($tid) {
        $redis = RedisClient::getClient();
        $key = "trip_{$tid}_followers";
        
        if ($redis->exists($key))
            return $redis->sCard($key);
        
        return Yii::app()->db->createCommand()
                ->select('count(id)')
                ->from('user_trip_follow')
                ->where('trip_id = :trip_id and deleted = 0', array(':trip_id' => $tid))
                ->queryScalar();
    }

    /**
     * 用户是否参与了某trip
     * 
     * @param integer $uid 用户id
     * @param integer $tid trip id
     * @return boolean
     */
    public static function isUserJoinTrip($uid, $tid) {
        if (!$uid || !$tid)
            return false;

        $redis = RedisClient::getClient();
        $key = "trip_{$tid}_participants";

        if (!$redis->exists($key)) {
            $uids = self::getTripParticipants($tid);
            
            return in_array($uid, $uids);
        }

        return $redis->sIsMember($key, $uid);
    }

    /**
     * 获取一个trip的所有参与者
     * 
     * @param integer $tid trip id
     * @return boolean
     */
    public static function getTripParticipants($tid) {
        $redis = RedisClient::getClient();
        $key = "trip_{$tid}_participants";

        if (!$redis->exists($key)) {
            $uids = Yii::app()->db->createCommand()
                    ->select('user_id')
                    ->from('user_trip_join')
                    ->where('trip_id = :trip_id and deleted = 0', array(':trip_id' => $tid))
                    ->queryColumn();
            
            foreach ($uids as $one)
                $redis->sAdd($key, $one);
            $redis->expire($key, self::TTL);
            return $uids;
        }
        return $redis->sMembers($key);
    }
    
    /**
     * 获取参与某个trip的用户数
     * 
     * @param integer $tid
     * @return integer
     */
    public static function getTripParticipantsCount($tid) {
        $redis = RedisClient::getClient();
        $key = "trip_{$tid}_participants";
        
        if ($redis->exists($key))
            return $redis->sCard ($key);
        
        return Yii::app()->db->createCommand()
                ->select('count(id)')
                ->from('user_trip_join')
                ->where('trip_id = :trip_id', array(':trip_id' => $tid))
                ->queryScalar();
    }
    
    /**
     * 参与一个trip
     * 
     * @param integer $uid
     * @param integer $tid
     * @return boolean
     */
    public static function joinTrip($uid, $tid) {
        if (!$uid || !$tid)
            return false;
        $ret = UserTripJoinAR::model()->updateAll(array('deleted' => 0), 'trip_id = :tid and user_id = :uid', array(
            ':tid' => $tid,
            ':uid' => $uid,
        ));
        if (!$ret) {
            $join = new UserTripJoinAR();
            $join->user_id = $uid;
            $join->trip_id = $tid;
            $ret = $join->save();
        }
        if ($ret) {
            RedisClient::getClient()->del("trip_{$tid}_participants");
        }
        return $ret;
    }
    
    /**
     * 取消参与一个trip
     * 
     * @param integer $uid
     * @param integer $tid
     * @return boolean
     */
    public static function unjoinTrip($uid, $tid) {
        $ret = UserTripJoinAR::model()->updateAll(array('deleted' => 1), 'trip_id = :tid and user_id = :uid', array(
            ':tid' => $tid,
            ':uid' => $uid,
        ));
        if ($ret) {
            RedisClient::getClient()->del("trip_{$tid}_participants");
        }
        return $ret;
    }
    
    /**
     * add a trip
     * 
     * @param array $attributes
     * @param string $cover
     * 
     * @return TripAR
     * @throws CException
     */
    public static function addTrip( $attributes, $cover ){
        $ar = new TripAR();
        $ar->attributes = $attributes;
        $ar->cover = $cover;
        if( !$ar->saveL() ){
            throw new CException(Utils::arrToString($ar->getErrors()));
        }
        EventListener::getListener()->run(array(
            'user_id' => $ar->creator_id,
            'content' => CJSON::encode(array('trip_id'=>$ar->id)),
            'type'    => Event::PTRIP,
        ));
        return $ar;
    }
    
    
    /**
     * 获取用户创建的trip
     * 
     * @param integer $uid
     * @return array
     */
    public static function getUserCreatedTrips($uid) {
        $ids = Yii::app()->db->createCommand()
                ->select('id')
                ->from('trip')
                ->where('creator_id = :uid')
                ->order('create_time desc')
                ->bindValue(':uid', $uid)
                ->queryColumn();
        $ret = array();
        foreach ($ids as $id) {
            $ret[] = self::getTrip($id);
        }
        return $ret;
    }
    
    /**
     * 获取用户参与的trips
     * 
     * @param integer $uid
     * @return array
     */
    public static function getUserJoinedTrips($uid) {
        $ids = Yii::app()->db->createCommand()
                ->select('trip_id')
                ->from('user_trip_join')
                ->where('user_id = :uid and deleted = 0')
                ->order('time desc')
                ->bindValue(':uid', $uid)
                ->queryColumn();
        $ret = array();
        foreach ($ids as $id) {
            $ret[] = self::getTrip($id);
        }
        return $ret;
    }
    
}
