<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.GroupUserAR');
Yii::import('application.models.User.User');

/**
 * Description of GroupUser
 *
 * @author yemin
 */
class GroupUser {
    //put your code here

    /**
     * 加入一个小组
     * 
     * @param integer $uid user id
     * @param integer $gid group id
     * @return boolean
     */
    public static function joinGroup($uid, $gid) {
        Group::delGroupCache($gid);
        $model  = GroupUserAR::model()->findByAttributes(array(
            'user_id' => $uid,
            'group_id' => $gid,
        ));
        if ($model) {
            $model->deleted = 0;
            return $model->save();
        } else {
            $model = new GroupUserAR();
            $model->user_id = $uid;
            $model->group_id = $gid;
            return $model->save();
        }
    }

    /**
     * 退出一个小组
     * 
     * @param integer $uid user id
     * @param integer $gid group id
     * @return boolean
     */
    public static function unjoinGroup($uid, $gid) {
        $group = Group::getGroup($gid);
        if (!$group) return false;
        if ($group['creator_id'] == $uid) return false;
        
        Group::delGroupCache($gid);
        return GroupUserAR::model()->updateAll(array(
                    'deleted' => 1,
                        ), 'user_id = :uid and group_id = :gid', array(
                    ':uid' => $uid,
                    ':gid' => $gid,
                )) == 1;
    }

    /**
     * 用户是否加入一个小组
     * 
     * @param integer $uid user id
     * @param integer $gid group id
     * @return boolean
     */
    public static function isUserJoinGroup($uid, $gid) {
        if (empty($uid))
            return false;

        return GroupUserAR::model()->exists('user_id = :user_id and group_id = :group_id and deleted = 0', array(
                    ':user_id' => $uid,
                    ':group_id' => $gid,
                ));
    }
    
    
    /**
     * 获取一个group的关注者
     * 
     * @param integer $gid
     * @param integer $offset
     * @param integer $size
     * @return array
     */
    public static function getGroupUsers($gid, $offset=0, $size=8) {
        $uids = Yii::app()->db->createCommand()
                ->select('distinct(user_id) as user_id')
                ->from('group_user')
                ->where('group_id = :gid and deleted = 0', array(
                    ':gid' => $gid,
                ))
                ->order('create_time desc')
                ->offset($offset)
                ->limit($size)
                ->queryColumn();
        return User::getUsers($uids);
    }
    
    
    /**
     * 获取一个小组的成员数
     * 
     * @param integer $gid
     * @return integer
     */
    public static function getGroupUserCount($gid) {
        return Yii::app()->db->createCommand()
                ->select('count(distinct user_id)')
                ->from('group_user')
                ->where('group_id = :gid and deleted = 0', array(':gid' => $gid))
                ->queryScalar();
        
    }
    
    /**
     * 获取用户加入的小组
     * 
     * @param integer $uid
     * @return array
     */
    public static function getUserGroups($uid) {
        $ids = Yii::app()->db->createCommand()
                ->select('group_id')
                ->from('group_user')
                ->where('user_id = :uid and deleted = 0', array(':uid' => $uid))
                ->order('create_time desc')
                ->queryColumn();
        return Group::getGroups($ids);
    }
    
    
    /**
     * 获取用户加入的小组id
     * 
     * @param integer $uid
     * @return array
     */
    public static function getUserGroupIds($uid) {
        return Yii::app()->db->createCommand()
                ->select('group_id')
                ->from('group_user')
                ->where('user_id = :uid and deleted = 0', array(':uid' => $uid))
                ->order('create_time desc')
                ->queryColumn();
    }

}

?>
