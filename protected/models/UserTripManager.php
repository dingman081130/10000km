<?php
/**
 * @file class UserTripManager 管理用户和旅程的关系
 * @package application.models
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-13
 * @version
 */

Yii::import('application.models.Trip.Trip');
Yii::import('application.models.AR.UserTripConAR');

class UserTripManager{

    /**
     * 获取一个用户所关心的旅程信息
     *
     * @param int $user_id 用户id
     * @param int $offset=0 偏移量
     * @param int $limit=0 位长，默认为0时，取从偏移量开始的所有记录
     * @return array
     */
    public function getConTripByUser($user_id, $offset=0, $limit=0){
        $criteria = new CDbCriteria;
        $criteria->condition = 'user_id=:user_id and is_delete=0';
        $criteria->params = array( ':user_id' => $user_id );
        $criteria->offset = $offset;
        if( $limit > 0 ){
            $criteria->limit = $limit;
        }
        $obj_arr = UserTripConAR::model()->findAll($criteria);
        $trip_arr = array();
        foreach($obj_arr as $obj){
            $trip_arr[] = $obj->trip_id;
        }
        return Trip::getTripBasicInfoByIdArr($trip_arr);
    }

    /**
     * 获取一个用户所关心的旅程的数量
     *
     * @param int $user_id 用户id
     * @return int
     */
    public function getConTripCntByUser($user_id){
        return UserTripConAR::model()->count('user_id=:user_id and is_delete=0', array(':user_id'=>$user_id));
    }

    /**
     * 获取一个用户所关心的新鲜旅程的数量
     * 新鲜的定义为该旅程有最近follow，follow包括其他用户针对此旅程进行了基本信息的更新，评论，发布照片或是游记
     *
     * @param int $user_id 用户id
     * @return int
     */
    public function getConFreTripCntByUser(){
        // add your logic here
        return 0;
    }
}
