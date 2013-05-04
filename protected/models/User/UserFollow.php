<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Yii::import('application.models.AR.UserFollowAR');

/**
 * Description of UserFollow
 *
 * @author yemin
 */
class UserFollow {
    //put your code here

    /**
     * 返回用户关注的用户id
     * 
     * @param integer $uid
     * @return array array(uid1, uid2, uid3, ......)
     */
    public static function getUserFollowIds($uid) {
        $redis = RedisClient::getClient();
        $key = self::followKey($uid);
        if ($redis->exists($key)) {
            return $redis->sMembers($key);
        }

        $ids = Yii::app()->db->createCommand()
                ->select('user2_id')
                ->from('user_follow')
                ->where('user1_id = :uid and deleted = 0', array(':uid' => $uid))
                ->order('time desc')
                ->queryColumn();

        $multi = $redis->multi();
        foreach ($ids as $id) {
            $multi->sAdd($key, $id);
        }
        $multi->exec();
        return $ids;
    }

    /**
     * 返回用户关注的所有用户
     * 
     * @param integer $uid
     * @return array array(user1, user2, user3,......)
     */
    public static function getUserFollow($uid) {
        $ids = self::getUserFollowIds($uid);
        $users = User::getUsers($ids);
        return $users;
    }

    /**
     * 返回用户的粉丝id
     * 
     * @param integer $uid
     * @param bool $withself = fals
     * @return array array(uid1, uid2, uid3, ......)
     */
    public static function getUserFansIds($uid, $withself = false) {
        $redis = RedisClient::getClient();
        $key = self::fansKey($uid);
        if ($redis->exists($key)) {
            $ids = $redis->sMembers($key);
            $withself && $ids[] = $uid;
            return array_unique($ids);
        }

        $ids = Yii::app()->db->createCommand()
                ->select('user1_id')
                ->from('user_follow')
                ->where('user2_id = :uid and deleted = 0', array(':uid' => $uid))
                ->order('time desc')
                ->queryColumn();

        $multi = $redis->multi();
        foreach ($ids as $id) {
            $multi->sAdd($key, $id);
        }
        $multi->exec();
        if($withself){
            $ids[] = $uid;
        }
        return $ids;
    }

    /**
     * 获取用户所有的粉丝
     * 
     * @param integer $uid
     * @return arrray array(user1, user2, user3,......)
     */
    public static function getUserFans($uid) {
        $ids = self::getUserFansIds($uid);
        $users = User::getUsers($ids);
        return $users;
    }
    
    
    /**
     * 获取用户的粉丝数
     * 
     * @param integer $uid
     * @return integer
     */
    public static function getUserFansCount($uid) {
        $redis = RedisClient::getClient();
        $key = self::fansKey($uid);
        if ($redis->exists($key)) {
            return $redis->sCard($key);
        }
        return count(self::getUserFans($uid));
    }
    
    /**
     * 获取用户关注的人数
     * 
     * @param integer $uid
     * @return integer
     */
    public static function getUserFollowCount($uid) {
        $redis = RedisClient::getClient();
        $key = self::followKey($uid);
        if ($redis->exists($key)) {
            return $redis->sCard($key);
        }
        return count(self::getUserFollow($uid));
    }

    /**
     * 一个用户关注另一个用户
     * 
     * @param integer $myid 攻的id
     * @param integer $uid 受的id
     * @return boolean 关注成功时返回true，否则返回false
     */
    public static function followUser($myid, $uid) {
        if (!$myid || !$uid)
            return false;

        self::delUserFollowCache($myid);
        self::delUserFansCache($uid);

        $obj = UserFollowAR::model()->find('user1_id = :user1_id and user2_id = :user2_id', array(
            ':user1_id' => $myid,
            ':user2_id' => $uid,
                ));
        if ($obj) {
            $obj->deleted = 0;
            return $obj->save();
        } else {
            $obj = new UserFollowAR;
            $obj->user1_id = $myid;
            $obj->user2_id = $uid;
            return $obj->save();
        }
    }

    /**
     * 取消关注某用户
     * 
     * @param integer $myid
     * @param integer $uid
     */
    public static function unfollowUser($myid, $uid) {
        if (!$myid || !$uid)
            return false;

        self::delUserFollowCache($myid);
        self::delUserFansCache($uid);

        return UserFollowAR::model()->updateAll(array(
                    'deleted' => 1,
                        ), 'user1_id = :user1_id and user2_id = :user2_id', array(
                    ':user1_id' => $myid,
                    ':user2_id' => $uid,
                ));
    }

    /**
     * 检查当前用户1是否关注用户2
     * 
     * @param integer $uid
     * @return boolean
     */
    public static function isFollowUser($user1_id, $user2_id) {
        $redis = RedisClient::getClient();
        $key = self::followKey($user1_id);
        if ($redis->exists($key)) {
            return $redis->sIsMember($key, $user2_id);
        }
        return UserFollowAR::model()->exists('user1_id = :id1 and user2_id = :id2 and deleted = 0', array(
                    ':id1' => $user1_id,
                    ':id2' => $user2_id,
                ));
    }

    private static function followKey($id) {
        return "user_{$id}_follow";
    }

    private static function fansKey($id) {
        return "user_{$id}_fans";
    }

    public static function delUserFollowCache($id) {
        RedisClient::getClient()->del(self::followKey($id));
    }

    public static function delUserFansCache($id) {
        RedisClient::getClient()->del(self::fansKey($id));
    }

}

?>
