<?php

/**
 * @file class User
 * @package application.models.User
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-15
 * @version
 */
Yii::import('application.models.AR.UserAR');
Yii::import('application.models.AR.UserPickAR');
Yii::import('application.models.AR.UserProfileAR');
Yii::import('application.models.AR.SocialAccountAR');
Yii::import('application.models.Message.Heehaw');
Yii::import('application.models.Event.EventListener');
Yii::import('application.models.Event.Event');

class User {
    /**
     * @const 用户缓存key的前缀
     */

    const PRE = 'user_';

    /**
     * @const 用户缓存的失效时间
     */
    const TTL = 3600;

    /**
     * 通过用户id获取用户名
     *
     * @param array $id_arr
     * @return array e.g.
     * <code>
     * array(
     *      2 => '我不叫丁满'   // UserId => UserName
     * )
     * </code>
     */
    public static function getNameById($id_arr) {
        $ret = array();
        foreach ($id_arr as $id) {
            $user = self::getBasicById($id);
            if ($user)
                $ret[$id] = $user['name'];
            else
                $ret[$id] = null;
        }
        return $ret;
    }

    /**
     * 通过id获取用户名
     * 
     * @param int $id
     * @return string
     */
    public static function getNameByIdSingle($id) {
        $user = self::getBasicById($id);
        if ($user) {
            return $user['name'];
        }
        return NULL;
    }

    /**
     * 通过用户id获取用户基本信息
     *
     * @param int $id 用户id
     * @return array e.g.
     */
    public static function getBasicById($id) {
        $key = self::getUserRedisKey($id);
        $user_info = RedisClient::getClient()->get($key);
        if (!$user_info) {
            $user = UserAR::model()->findByPK($id);
            $profile = UserProfileAR::model()->findByPk($id);

            if ($user && $profile) {
                $user_attr = $user->attributes;
                $profile_attr = $profile->attributes;

                $user_info = array_merge($user_attr, $profile_attr);
                RedisClient::getClient()->setex($key, self::TTL, $user_info);
            }
        }
        if (!$user_info) {
            return array();
        }

        return $user_info;
    }

    /**
     * 获取用户的redis缓存idkey
     *
     * @param int $id 用户id
     * @return string redis缓存key
     */
    private static function getUserRedisKey($id) {
        return self::PRE . $id;
    }

    /**
     * 返回用户信息
     * 
     * @param integer $id
     * @return array 成功时返回array，否则返回null
     */
    public static function getUser($id) {
        $key = self::getUserRedisKey($id);
        if (!( $user_info = RedisClient::getClient()->get($key) )) {
            $user = UserAR::model()->findByPK($id);
            $profile = UserProfileAR::model()->findByPk($id);

            if ($user && $profile) {
                $user_attr = $user->attributes;
                unset($user_attr['hashpwd']);
                $profile_attr = $profile->attributes;

                $user_info = array_merge($user_attr, $profile_attr);
                RedisClient::getClient()->setex($key, self::TTL, $user_info);
            }
        }
        if (!$user_info) {
            return null;
        }

        return $user_info;
    }

    /**
     * 批量返回用户信息
     * 
     * @param array $ids 用户id数组
     * @return array 用户信息数组
     */
    public static function getUsers($ids) {
        $redis = RedisClient::getClient();
        $keys = array();
        foreach ($ids as $id) {
            $keys[] = self::getUserRedisKey($id);
        }
        $users = $redis->mget($keys);
        if ($users === false)
            return array();

        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i] === false) {
                $users[$i] = self::getUser($ids[$i]);
            }
        }
        return $users;
    }

    public static function delUserCache($id) {
        RedisClient::getClient()->del(self::getUserRedisKey($id));
    }

    /**
     * 更新用户的求被捡信息
     * 如果存在用户id则update
     * 如果不存在用户id则add
     * 
     * @param int $id 用户id
     * @param string $dst 用户想去的地方，字符串逗号分隔
     * @param datetime $start 开始时间
     * @param datetime $end 结束时间
     * @param string $desc = '' 说明
     * @param bool $heehaw = false 是否驴叫
     * @return bool
     * 
     */
    public static function updatePick($id, $dst, $start, $end, $desc = '', $heehaw = false) {
        $id = intval($id);
        ( $ar = UserPickAR::model()->findByPk($id) ) or ( $ar = new UserPickAR );

        $ar->id = $id;
        $ar->dst = $dst;
        $ar->start = $start;
        $ar->end = $end;
        $ar->desc = $desc;

        if (!$ar->saveL()) {
            return false;
        }
        $heehaw && Heehaw::pubHeehaw($id, $desc, $heehaw);    // 只发布不推送驴叫信息
        EventListener::getListener()->run(array(
            'user_id' => $id,
            'content' => CJSON::encode(array(
                'dst' => $dst,
                'end' => $end,
                'desc' => $desc,
                'start' => $start,
            )),
            'type' => Event::PICK
        ));
        return true;
    }

    /**
     * 更新session中的用户信息（用户登录后会将他的个人信息保存在session中，当用户修改资料后要更新这个session内容）
     */
    public static function updateMyCacheProfile() {
        self::delUserCache(Yii::app()->user->id);
        Yii::app()->user->attrs = self::getUser(Yii::app()->user->id);
    }
    
    
    public static function updateLastOnlineTime() {
        $uid = Yii::app()->user->id;
        if (!$uid) return;
        
        $key = self::getUserRedisKey($uid);
        $user = self::getUser($uid);
        if ($user) {
            $save_login_time = isset(Yii::app()->session['save_login_time']) ? Yii::app()->session['save_login_time'] : 0;
            $delta = time() - $save_login_time;
            if ($delta >= 600) { // 超过10分钟，将用户最后访问的时间存入数据库
                Yii::app()->session['save_login_time'] = time();
                UserAR::model()->updateByPk($uid, array('last_login_time' => date('Y-m-d H:i:s')));
            }
            $user['last_login_time'] = date('Y-m-d H:i:s');
            RedisClient::getClient()->setex($key, self::TTL, $user);
        }
    }
    
    /**
     * 获取系统用户id
     * @return int
     */
    public static function getSysId(){
        return Yii::app()->params['sysId'];
    }
    
}
