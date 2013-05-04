<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserSuggest
 *
 * @author yemin
 */
class UserSuggest {
    //put your code here

    /**
     * 与用户相关的推荐
     * 
     * @param integer $uid
     * @return array
     */
    public static function suggestUser($uid, $offset = 0, $size = 5) {
        Yii::import('application.models.Place.Place');
        $user = User::getUser($uid);
        if (!$user)
            return array();
        $cur_city_id = Place::getClientCity();

        $command = Yii::app()->db->createCommand()
                ->select('u.id,  (case when up.sex != :sex then 10 else 0 end)
                                +(case when up.live_city_id = :live_city_id then 10 else 0 end)
                                +(case when :cur_city_id != :live_city_id and up.live_city_id = :cur_city_id then 10 else 0 end)
                                -abs(year(up.birthday) - year(:birthday))
                                as rank')
                ->from('user as u')
                ->join('user_profile as up', 'u.id = up.id')
                ->where('u.id != :myid')
                ->order('rank desc')
                ->offset($offset)
                ->limit($size);

        $command->bindValues(array(
            ':sex' => $user['sex'],
            ':live_city_id' => $user['live_city_id'],
            ':cur_city_id' => $cur_city_id,
            ':birthday' => $user['birthday'],
            ':myid' => $user['id'],
        ));
        $ids = $command->queryColumn();
        $users = User::getUsers($ids);
        return $users;
    }
    
    
    /**
     * 当用户在搜索trip时，推荐用户给他
     * 
     * @param integer $uid 当前用户的id
     * @param array $dsts 用户搜索的目的地
     * @return array 返回推荐用户
     */
    public static function suggestTripUser($uid, $dsts, $offset=0, $size=8) {
        $user = User::getUser($uid);
        $my_sex = 1;
        if ($user) {
            $my_sex = intval($user['sex']);
        }
        
        
        $criteria = new CDbCriteria();
        $criteria->addInCondition('name', $dsts);
        $cities = CityAR::model()->findAll($criteria);
        
        $city_ids = array();
        foreach ($cities as $city) {
            $city_ids[] = intval($city['id']);
        }
        
        // fvck code!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        if (empty($city_ids)) $city_ids[] = -1; // to avoid sql error!!!!
        $in_city_str = implode(',', $city_ids);
        
        $uids = Yii::app()->db->createCommand()
                ->select("id, (case when live_city_id in ($in_city_str) then 20 else 0 end)
                             +(case when up.sex != $my_sex then 10 else 0 end)
                             as relevance")
                ->from('user_profile as up')
                ->where('id != :myid')
                ->order('relevance desc')
                ->offset($offset)
                ->limit($size)
                ->bindValue(':myid', intval($uid))
                ->queryColumn();
        
        return User::getUsers($uids);
    }
    
    
    public static function suggestLocalUser($uid, $city_id, $offset=0, $size=8) {
        $ids = Yii::app()->db->createCommand()
                ->select('id')
                ->from('user_profile as up')
                ->where('up.live_city_id = :city_id')
                ->offset($offset)
                ->limit($size)
                ->order('rand()')
                ->bindValue(':city_id', $city_id)
                ->queryColumn();
        
        return User::getUsers($ids);
    }

}

?>
