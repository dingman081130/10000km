<?php
/**
 * @file class Fresh 新鲜事
 * @package application.models.Fresh
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-14
 * @version
 */

//Yii::import('application.models.UserTripManager');
//Yii::import('application.models.Pagination');
Yii::import('application.models.Event.EventUser');

class Fresh{

    /**
     * 获取用户新鲜事的数量
     *
     * @param int $user_id 用户id
     * @return int
     *
    private function getFreshCnt($user_id){
        // add your logic here
        return self::getTripFreshCnt($user_id) + self::getOthFreshCnt($user_id);
    }*/

    /**
     * 获取用户所关心的旅程新鲜事的数量
     *
     * @param int $user_id 用户id
     * @return int
     *
    private function getTripFreshCnt($user_id){
        // add your logic here
        return UserTripManager::getConTripCntByUser($user_id);
    }*/

    /**
     * 获取用户所关心的其他新鲜事的数量
     *
     * @param int $user_id 用户id
     * @return int
     *
    private function getOthFreshCnt($user_id){
        // add your logic here
        return 0;
    }*/
    

    /**
     * 获取用户新鲜事列表
     *
     * @param int $user_id 用户id
     * @param int $offset 偏移量
     * @param int $limit 位长
     * @return array
     *
    public static function getFreshList($user_id, $offset=0, $limit=15){
        $trip_cnt = UserTripManager::getConTripCntByUser($user_id);
        $trip_arr = UserTripManager::getConTripByUser($user_id, $offset, $limit);
        if( Yii::app()->params['mode'] == 'dev' ){
            $trip_arr = array_merge( $trip_arr, $trip_arr );
        }
        $ret = array(
            /*
            'page' => array(
                'cnt'=>$trip_cnt,
                'per'=>$limit,
                'cur'=>Pagination::getCurNum($offset,$limit),
                'page_cnt'=>Pagination::getPageCnt($trip_cnt,$limit)),
            'list' => $trip_arr
        );
        return $ret;
    }*/ 
    
    /**
     * 获取推给用户的新鲜事列表
     * 
     * @param int $user
     * @param int $offset
     * 
     * @return array e.g. 同EventUser::getEventByUser()
     */
    public static function getFreshList($user, $offset = 0){
        $limit = isset(Yii::app()->params['freshPageSize']) ? Yii::app()->params['freshPageSize'] : 5;
        $ins = new EventUser;
        $data = $ins->getEventByUser($user, $offset, $limit);
        foreach($data as &$one){
            $one['content'] = CJSON::decode($one['content']);
        }
        return $data;
    }
    
    /**
     * 获取用户推出的新鲜事列表
     * 
     * @param int $user
     * @param int $offset
     * @return 同self::getFreshList
     */
    public static function getFreshListOut($user, $offset = 0){
        $limit = isset(Yii::app()->params['freshPageSize']) ? Yii::app()->params['freshPageSize'] : 5;
        $ret = Event::getUserEvent($user, $offset, $limit);
        foreach($ret as &$one){
            $one['content'] = CJSON::decode($one['content']);
        }
        return $ret;
    }
}
