<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.User.User');

/**
 * Description of UserProfile
 *
 * @author yemin
 */
class UserProfile {
    //put your code here
    
    
    /**
     * 更新用户的个性设置
     * 
     * @param array $params
     * @return boolean
     */
    public static function updatePersonality($params) {
        $uid = Yii::app()->user->id;
        $type = isset($params['type']) ? $params['type'] : null;
        $value = isset($params['value']) ? $params['value'] : null;
        $ret = false;
        
        if ($type == 'want_places') {
            $ret = UserProfileAR::model()->updateByPk($uid, array(
                'want_places' => $value,
            ));
        } else if ($type == 'personal_tags') {
            $ret = UserProfileAR::model()->updateByPk($uid, array(
                'personal_tags' => $value,
            ));
        }
        User::delUserCache($uid);
        return $ret == 1;
    }
}

?>
