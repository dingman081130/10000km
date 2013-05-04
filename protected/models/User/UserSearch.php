<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.Couch.Couch');

/**
 * Description of UserSearch
 *
 * @author yemin
 */
class UserSearch {
    //put your code here

    /**
     * 
     * @param array $cond array('city_id'=>xx, 'sex'=>xx, 'start_age'=>xx, 'end_age'=>xx, 'photo'=>xx)
     * @param integer $offset
     * @param integer $size
     * @return array
     */
    public static function getCityLocals($cond, $offset = 0, $size = 10) {
        $city_id = isset($cond['city_id']) ? $cond['city_id'] : null;
        $sex = isset($cond['sex']) ? $cond['sex'] : null;
        $start_age = isset($cond['start_age']) ? $cond['start_age'] : null;
        $end_age = isset($cond['end_age']) ? $cond['end_age'] : null;
        $photo = isset($cond['photo']) ? $cond['photo'] : null;

        if (!$city_id)
            return null;

        $command = Yii::app()->db->createCommand()
                ->select('user_profile.id')
                ->from('user_profile')
                ->join('city', 'user_profile.live_city_id = city.id')
                ->leftJoin('album', 'album.user_id = user_profile.id')
                ->leftJoin('photo', 'photo.album_id = album.id');
        $where = '(city.id = :city_id or city.upid = :city_id)';
        $params = array(
            ':city_id' => $city_id,
        );

        if ($sex !== null && $sex !== '') {
            $where .= ' and user_profile.sex = :sex';
            $params[':sex'] = $sex;
        }
        if (!empty($start_age)) {
            $end_birthday = date('Y-m-d', strtotime("-{$start_age} years"));
            $where .= ' and user_profile.birthday <= :end_birthday';
            $params[':end_birthday'] = $end_birthday;
        }
        if (!empty($end_age)) {
            $end_age++;
            $start_birthday = date('Y-m-d', strtotime("-{$end_age} years"));
            $where .= ' and user_profile.birthday >= :start_birthday';
            $params[':start_birthday'] = $start_birthday;
        }

        $command->group('user_profile.id');
        if ($photo) {
            $command->having('count(photo.id)>0');
        }

        $command->where($where, $params)
                ->offset($offset)
                ->limit($size);
        $ids = $command->queryColumn();
        $users = array();
        foreach ($ids as $id) {
            $user = User::getUser($id);
            $couch = Couch::getUserCouch($id);
            if ($couch && $couch['available']) {
                $user['couch'] = $couch;
            }
            $users[] = $user;
        }
        return $users;
    }

    public static function getCityTravelers($cond, $offset = 0, $size = 10) {
        
    }

    public static function getCitySurfers($cond, $offset = 0, $size = 10) {
        $city_id = isset($cond['city_id']) ? $cond['city_id'] : null;
        $sex = isset($cond['sex']) ? $cond['sex'] : null;
        $start_age = isset($cond['start_age']) ? $cond['start_age'] : null;
        $end_age = isset($cond['end_age']) ? $cond['end_age'] : null;
        $photo = isset($cond['photo']) ? $cond['photo'] : null;

        if (!$city_id)
            return null;

        $command = Yii::app()->db->createCommand()
                ->select('max(s.id)')
                ->from('couch_search as s')
                ->join('user_profile', 'user_profile.id = s.user_id')
                ->join('city', 's.city_id = city.id')
                ->leftJoin('album', 'album.user_id = s.user_id')
                ->leftJoin('photo', 'photo.album_id = album.id');

        $where = 's.deleted = 0 and (city.id = :city_id or city.upid = :city_id)';
        $params = array(
            ':city_id' => $city_id,
        );

        if ($sex !== null && $sex !== '') {
            $where .= ' and user_profile.sex = :sex';
            $params[':sex'] = $sex;
        }
        if (!empty($start_age)) {
            $end_birthday = date('Y-m-d', strtotime("-$start_age years"));
            $where .= ' and user_profile.birthday <= :end_birthday';
            $params[':end_birthday'] = $end_birthday;
        }
        if (!empty($end_age)) {
            $end_age++;
            $start_birthday = date('Y-m-d', strtotime("-$end_age years"));
            $where .= ' and user_profile.birthday >= :start_birthday';
            $params[':start_birthday'] = $start_birthday;
        }

        $command->group('s.user_id');
        if ($photo) {
            $command->having('count(photo.id)>0');
        }

        $command->where($where, $params)
                ->offset($offset)
                ->limit($size);

        $ids = $command->queryColumn();
        $ret = array();
        foreach ($ids as $id) {
            $couch_search = Couch::getCouchSearch($id);
            if ($couch_search) {
                $uid = $couch_search['user_id'];
                $user = User::getUser($uid);
                $user['couch_search'] = $couch_search;
                $ret[] = $user;
            }
        }
        return $ret;
    }
    
    
    /**
     * 查找一个城市的沙发主
     * 
     * @param array $cond
     * @param integer $offset
     * @param integer $size
     * @return array
     */
    public static function getCityHosts($cond, $offset = 0, $size = 10) {
        $city_id = isset($cond['city_id']) ? $cond['city_id'] : null;
        $sex = isset($cond['sex']) ? $cond['sex'] : null;
        $start_age = isset($cond['start_age']) ? $cond['start_age'] : null;
        $end_age = isset($cond['end_age']) ? $cond['end_age'] : null;
        $photo = isset($cond['photo']) ? $cond['photo'] : null;

        if (!$city_id)
            return null;

        $command = Yii::app()->db->createCommand()
                ->select('c.user_id')
                ->from('couch as c')
                ->join('user_profile', 'user_profile.id = c.user_id')
                ->join('city', 'user_profile.live_city_id = city.id')
                ->leftJoin('album', 'album.user_id = user_profile.id')
                ->leftJoin('photo', 'photo.album_id = album.id');
        $where = 'c.available = 1 and (city.id = :city_id or city.upid = :city_id)';
        $params = array(
            ':city_id' => $city_id,
        );

        if ($sex !== null && $sex !== '') {
            $where .= ' and user_profile.sex = :sex';
            $params[':sex'] = $sex;
        }
        if (!empty($start_age)) {
            $end_birthday = date('Y-m-d', strtotime("-{$start_age} years"));
            $where .= ' and user_profile.birthday <= :end_birthday';
            $params[':end_birthday'] = $end_birthday;
        }
        if (!empty($end_age)) {
            $end_age++;
            $start_birthday = date('Y-m-d', strtotime("-{$end_age} years"));
            $where .= ' and user_profile.birthday >= :start_birthday';
            $params[':start_birthday'] = $start_birthday;
        }

        $command->group('c.user_id');
        if ($photo) {
            $command->having('count(photo.id)>0');
        }

        $command->where($where, $params)
                ->offset($offset)
                ->limit($size);
        $ids = $command->queryColumn();
        
        $ret = array();
        foreach ($ids as $id) {
            $couch = Couch::getUserCouch($id);
            if ($couch) {
                $user = User::getUser($id);
                $user['couch'] = $couch;
                $ret[] = $user;
            }
        }
        return $ret;
    }

    /**
     * 
     * @param array $cond
     * @param integer $offset
     * @param integer $size
     * @return array
     */
    public static function getAreaLocals($cond, $offset = 0, $size = 10) {
        $sw_lng = isset($cond['sw_lng']) ? $cond['sw_lng'] : null;
        $sw_lat = isset($cond['sw_lat']) ? $cond['sw_lat'] : null;
        $ne_lng = isset($cond['ne_lng']) ? $cond['ne_lng'] : null;
        $ne_lat = isset($cond['ne_lat']) ? $cond['ne_lat'] : null;
        $sex = isset($cond['sex']) ? $cond['sex'] : null;
        $start_age = isset($cond['start_age']) ? $cond['start_age'] : null;
        $end_age = isset($cond['end_age']) ? $cond['end_age'] : null;
        $photo = isset($cond['photo']) ? $cond['photo'] : null;

        if (!$sw_lat || !$sw_lng || !$ne_lat || !$ne_lng)
            return null;

        $command = Yii::app()->db->createCommand()
                ->select('u.id')
                ->from('user_profile as u')
                ->join('city', 'u.live_city_id = city.id')
                ->leftJoin('album', 'album.user_id = u.id')
                ->leftJoin('photo', 'photo.album_id = album.id');

        $where = 'city.longitude >= :sw_lng
                         and city.longitude <= :ne_lng
                         and city.latitude >= :sw_lat
                         and city.latitude <= :ne_lat';
        $params = array(
            ':sw_lng' => $sw_lng,
            ':sw_lat' => $sw_lat,
            ':ne_lng' => $ne_lng,
            ':ne_lat' => $ne_lat,
        );

        if ($sex !== null && $sex !== '') {
            $where .= ' and u.sex = :sex';
            $params[':sex'] = $sex;
        }
        if (!empty($start_age)) {
            $end_birthday = date('Y-m-d', strtotime("-$start_age years"));
            $where .= ' and user_profile.birthday <= :end_birthday';
            $params[':end_birthday'] = $end_birthday;
        }
        if (!empty($end_age)) {
            $end_age++;
            $start_birthday = date('Y-m-d', strtotime("-$end_age years"));
            $where .= ' and user_profile.birthday >= :start_birthday';
            $params[':start_birthday'] = $start_birthday;
        }

        $command->group('u.id');
        if (!empty($photo))
            $command->having = 'count(photo.id) > 0';

        $command->where($where, $params)
                ->offset($offset)
                ->limit($size);
        $ids = $command->queryColumn();
        $users = User::getUsers($ids);
        return $users;
    }

    public static function getAreaTravelers() {
        
    }

    public static function getAreaSurfers($cond, $offset = 0, $size = 10) {
        $sw_lng = isset($cond['sw_lng']) ? $cond['sw_lng'] : null;
        $sw_lat = isset($cond['sw_lat']) ? $cond['sw_lat'] : null;
        $ne_lng = isset($cond['ne_lng']) ? $cond['ne_lng'] : null;
        $ne_lat = isset($cond['ne_lat']) ? $cond['ne_lat'] : null;
        $sex = isset($cond['sex']) ? $cond['sex'] : null;
        $start_age = isset($cond['start_age']) ? $cond['start_age'] : null;
        $end_age = isset($cond['end_age']) ? $cond['end_age'] : null;
        $photo = isset($cond['photo']) ? $cond['photo'] : null;

        if (!$sw_lat || !$sw_lng || !$ne_lat || !$ne_lng)
            return null;

        $command = Yii::app()->db->createCommand()
                ->select('max(s.id)')
                ->from('couch_search as s')
                ->join('user_profile', 'user_profile.id = s.user_id')
                ->join('city', 'city.id = s.city_id')
                ->leftJoin('album', 'album.user_id = s.user_id')
                ->leftJoin('photo', 'photo.album_id = album.id');

        $where = 's.deleted = 0 and city.longitude >= :sw_lng
                         and city.longitude <= :ne_lng
                         and city.latitude >= :sw_lat
                         and city.latitude <= :ne_lat';
        $params = array(
            ':sw_lng' => $sw_lng,
            ':sw_lat' => $sw_lat,
            ':ne_lng' => $ne_lng,
            ':ne_lat' => $ne_lat,
        );

        if ($sex !== null && $sex !== '') {
            $where .= ' and user_profile.sex = :sex';
            $params[':sex'] = $sex;
        }
        if (!empty($start_age)) {
            $end_birthday = date('Y-m-d', strtotime("-$start_age years"));
            $where .= ' and user_profile.birthday <= :end_birthday';
            $params[':end_birthday'] = $end_birthday;
        }
        if (!empty($end_age)) {
            $end_age++;
            $start_birthday = date('Y-m-d', strtotime("-$end_age years"));
            $where .= ' and user_profile.birthday >= :start_birthday';
            $params[':start_birthday'] = $start_birthday;
        }

        $command->group('s.user_id');
        if ($photo) {
            $command->having('count(photo.id)>0');
        }

        $command->where($where, $params)
                ->offset($offset)
                ->limit($size);

        $ids = $command->queryColumn();
        $ret = array();
        foreach ($ids as $id) {
            $couch_search = Couch::getCouchSearch($id);
            if ($couch_search) {
                $uid = $couch_search['user_id'];
                $user = User::getUser($uid);
                $user['couch_search'] = $couch_search;
                $ret[] = $user;
            }
        }
        return $ret;
    }
    
    /**
     * 查找区域的沙发主
     * 
     * @param array $cond
     * @param integer $offset
     * @param integer $size
     * @return array
     */
    public static function getAreaHosts($cond, $offset = 0, $size = 10) {
        $sw_lng = isset($cond['sw_lng']) ? $cond['sw_lng'] : null;
        $sw_lat = isset($cond['sw_lat']) ? $cond['sw_lat'] : null;
        $ne_lng = isset($cond['ne_lng']) ? $cond['ne_lng'] : null;
        $ne_lat = isset($cond['ne_lat']) ? $cond['ne_lat'] : null;
        $sex = isset($cond['sex']) ? $cond['sex'] : null;
        $start_age = isset($cond['start_age']) ? $cond['start_age'] : null;
        $end_age = isset($cond['end_age']) ? $cond['end_age'] : null;
        $photo = isset($cond['photo']) ? $cond['photo'] : null;

        if (!$sw_lat || !$sw_lng || !$ne_lat || !$ne_lng)
            return null;

        $command = Yii::app()->db->createCommand()
                ->select('c.user_id')
                ->from('couch as c')
                ->join('user_profile', 'user_profile.id = c.user_id')
                ->join('city', 'city.id = user_profile.live_city_id')
                ->leftJoin('album', 'album.user_id = user_profile.id')
                ->leftJoin('photo', 'photo.album_id = album.id');

        $where = 'c.available = 1 and city.longitude >= :sw_lng
                         and city.longitude <= :ne_lng
                         and city.latitude >= :sw_lat
                         and city.latitude <= :ne_lat';
        $params = array(
            ':sw_lng' => $sw_lng,
            ':sw_lat' => $sw_lat,
            ':ne_lng' => $ne_lng,
            ':ne_lat' => $ne_lat,
        );

        if ($sex !== null && $sex !== '') {
            $where .= ' and user_profile.sex = :sex';
            $params[':sex'] = $sex;
        }
        if (!empty($start_age)) {
            $end_birthday = date('Y-m-d', strtotime("-$start_age years"));
            $where .= ' and user_profile.birthday <= :end_birthday';
            $params[':end_birthday'] = $end_birthday;
        }
        if (!empty($end_age)) {
            $end_age++;
            $start_birthday = date('Y-m-d', strtotime("-$end_age years"));
            $where .= ' and user_profile.birthday >= :start_birthday';
            $params[':start_birthday'] = $start_birthday;
        }

        $command->group('c.user_id');
        if ($photo) {
            $command->having('count(photo.id)>0');
        }

        $command->where($where, $params)
                ->offset($offset)
                ->limit($size);

        $ids = $command->queryColumn();
        $ret = array();
        foreach ($ids as $id) {
            $couch = Couch::getUserCouch($id);
            if ($couch) {
                $user = User::getUser($id);
                $user['couch'] = $couch;
                $ret[] = $user;
            }
        }
        return $ret;
    }

}

?>
