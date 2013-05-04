<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.User.User');
Yii::import('application.models.AR.UserProfileAR');

/**
 * Description of Search
 *
 * @author yemin
 */
class Search {
    //put your code here
    
    public static function searchCityUser($city_id, $type='local') {
        $ids = Yii::app()->db->createCommand()
                ->select('id')
                ->from('user_profile')
                ->where('live_city_id = :city_id', array(':city_id' => $city_id))
                ->queryColumn();
        $users = User::getUsers($ids);
        return $users;
    }
    
    public static function searchProvinceUser($province_id, $type='local') {
        $ids = Yii::app()->db->createCommand()
                ->select('user_profile.id')
                ->from('user_profile')
                ->join('city', 'user_profile.live_city_id = city.id')
                ->where('city.upid = :pid', array(':pid' => $province_id))
                ->queryColumn();
        $users = User::getUsers($ids);
        return $users;
    }
}

?>
