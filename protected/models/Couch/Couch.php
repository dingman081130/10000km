<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Yii::import('application.models.AR.CouchAR');
Yii::import('application.models.AR.CouchSurfAR');
Yii::import('application.models.AR.CouchSearchAR');

/**
 * Description of Couch
 *
 * @author yemin
 */
class Couch {
    //put your code here

    const TTL = 3600;

    /**
     * 更新用户的沙发信息
     * 
     * @param array $couch_params 新的沙发信息,array('available'=>1, 'capacity'=>2, 'no_smoke'=>1, 'guest_sex'=>1)
     * @return boolean 是否更新成功
     */
    public static function updateCouchInfo($couch_params) {
        $uid = Yii::app()->user->id;

        $couch = CouchAR::model()->find('user_id = :user_id', array(
            ':user_id' => Yii::app()->user->id,
                ));

        // 用户没有发布过沙发
        if (!$couch) {
            $couch = new CouchAR();
            $couch->attributes = $couch_params;
            return $couch->save();
        }

        $couch->attributes = $couch_params;
        if ($couch->save()) {
            RedisClient::getClient()->del("couch_$uid");
            return true;
        }

        return false;
    }

    /**
     * 查找一个用户的沙发信息，
     * 
     * @param integer $uid 用户id
     * @return array 用户的沙发信息，没有找到则返回null
     */
    public static function getUserCouch($uid) {
        $redis = RedisClient::getClient();
        $key = "couch_$uid";
        $couch = $redis->get($key);
        if ($couch === false) {
            $couch = CouchAR::model()->find('user_id = :user_id', array(
                ':user_id' => $uid,
                    ));
            if ($couch) {
                $couch = $couch->attributes;
                $redis->setex($key, self::TTL, $couch);
                return $couch;
            }
            return null;
        }
        return $couch;
    }

    /**
     * 申请沙发
     * 
     * @param integer $uid 沙发主id
     * @param integer $myid 申请人的id
     * @param array $params 填充CouchSurfAR字段的数组
     * @return boolean 成功时返回true，否则返回false
     */
    public static function requestCouch($uid, $myid, $params) {
        
        $model = new CouchSurfAR();
        $model->attributes = $params;
        $model->host_id = $uid;
        $model->surf_id = $myid;
        $model->type = CouchSurfAR::TYPE_SURF_REQUEST_HOST;
        if (!$model->save() ){
            //print_r($model->getErrors());exit;
            return $model->getEErrors();
        }
        return TRUE;
    }

    /**
     * 邀请一个人来住沙发
     * 
     * @param integer $uid 被邀请的沙发客的id
     * @param integer $myid 邀请人的id
     * @param array $params 填充CouchSurfAR字段的数组
     * @return boolean 成功时返回ture，否则返回false
     */
    public static function inviteCouch($uid, $myid, $params) {
        $model = new CouchSurfAR();
        $model->attributes = $params;
        $model->host_id = $myid;
        $model->surf_id = $uid;
        $model->type = CouchSurfAR::TYPE_HOST_INVITE_SURF;
        $ret = $model->save();
        return $ret;
    }

    /**
     * 查询一个用户的沙发主记录
     * 
     * @param integer $uid 用户id
     * @param integer $type 类型
     * @param integer $status 状态
     * @return array
     */
    public static function getHostCouchLog($uid, $type=0, $status=0) {
        $command = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('couch_surf')
                        ->where('host_id = :uid', array(':uid' => $uid));
        if ($type) $command->andWhere('type = :type', array(':type' => $type));
        if ($status) $command->andWhere('status = :status', array(':status' => $status));
        
        return $command->order('create_time desc')
                        ->queryAll();
    }

    /**
     * 查询一个用户的沙发客记录
     * 
     * @param integer $uid 用户id
     * @param integer $type
     * @param integer $status
     * @return array
     */
    public static function getSurfCouchLog($uid, $type=0, $status=0) {
        $command = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('couch_surf')
                        ->where('surf_id = :uid', array(':uid' => $uid));
        if ($type) $command->andWhere('type = :type', array(':type' => $type));
        if ($status) $command->andWhere('status = :status', array(':status' => $status));
        
        return $command->order('create_time desc')
                        ->queryAll();
    }

    /**
     * 查询一条用户的沙发客记录
     * 
     * @param integer $uid
     * @param integer $id
     * @return array 成功时返回array
     */
    public static function getOneSurfCouch($uid, $id) {
        return Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('couch_surf')
                        ->where('id = :id and surf_id = :uid', array(
                            ':uid' => $uid,
                            ':id' => $id,
                        ))->queryRow();
    }

    /**
     * 查询一条用户的沙发主记录
     * 
     * @param integer $uid
     * @param integer $id
     * @return array
     */
    public static function getOneHostCouch($uid, $id) {
        return Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('couch_surf')
                        ->where('id = :id and host_id = :uid', array(
                            ':uid' => $uid,
                            ':id' => $id,
                        ))->queryRow();
    }

    /**
     * 拒绝一个沙发请求或邀请
     * 
     * @param integer $id 
     * @param integer $uid 当前用户的id
     * @param string $reason 拒绝的原因
     * @return boolean
     */
    public static function reject($id, $uid, $reason) {
        if (empty($id) || empty($uid))
            return false;

        $model = CouchSurfAR::model()->findByPk($id);
        if (!$model)
            return false;

        if ($model['type'] == CouchSurfAR::TYPE_SURF_REQUEST_HOST && $uid == $model['host_id'] // 沙发主拒绝沙发客的请求
                || $model['type'] == CouchSurfAR::TYPE_HOST_INVITE_SURF && $uid == $model['surf_id']) { // 沙发客拒绝沙发主的邀请
            if ($model['status'] == CouchSurfAR::STATUS_PEDDING) {
                $model['status'] = CouchSurfAR::STATUS_REJECTED;
                $model['reason'] = $reason;
                return $model->save();
            }
        }

        return false;
    }

    /**
     * 接受一个请求或邀请
     * 
     * @param integer $id
     * @param integer $uid 当前用户的id
     * @return boolean
     */
    public static function accept($id, $uid) {
        if (empty($id) || empty($uid))
            return false;

        $model = CouchSurfAR::model()->findByPk($id);
        if (!$model)
            return false;

        if ($model['type'] == CouchSurfAR::TYPE_SURF_REQUEST_HOST && $uid == $model['host_id'] // 沙发主接受沙发客的申请
                || $model['type'] == CouchSurfAR::TYPE_HOST_INVITE_SURF && $uid == $model['surf_id']) { // 沙发客接受沙发主的邀请
            if ($model['status'] == CouchSurfAR::STATUS_PEDDING) {
                $model['status'] = CouchSurfAR::STATUS_ACCEPED;
                return $model->save();
            }
        }

        return false;
    }

    /**
     * 取消一个申请或邀请
     * 
     * @param integer $id 
     * @param integer $uid 当前用户的id
     * @return boolean
     */
    public static function cancel($id, $uid) {
        if (empty($id) || empty($uid))
            return false;

        $model = CouchSurfAR::model()->findByPk($id);
        if (!$model)
            return false;

        if ($model['type'] == CouchSurfAR::TYPE_SURF_REQUEST_HOST && $uid == $model['surf_id'] // 沙发客取消自己向沙发主提交的沙发申请
                || $model['type'] == CouchSurfAR::TYPE_HOST_INVITE_SURF && $uid == $model['host_id']) { // 沙发主取消自己发出的邀请
            if ($model['status'] == CouchSurfAR::STATUS_PEDDING) {
                $model['status'] = CouchSurfAR::STATUS_CANCELED;
                return $model->save();
            }
        }

        return false;
    }

    /**
     * 存储沙发信息，如果存储失败，抛出异常信息
     * @param array $params
     * @return bool
     */
    public static function saveCouchSearch($params) {
        $c = new CouchSearchAR;
        $c->attributes = $params;
        $ret = $c->save();
        if(!$ret){
            throw new CException($c->getFirstError());
        }
        return TRUE;
    }

    public static function delCouchSearch($uid, $id) {
        if (CouchSearchAR::model()->updateAll(array('deleted' => 1), 'id = :id and user_id = :uid', array(
                    ':uid' => $uid,
                    ':id' => $id,
                ))) {
            return true;
        }
        return false;
    }

    public static function getUserCouchSearch($uid) {
        return Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('couch_search')
                        ->where('user_id = :uid and deleted = 0', array(
                            ':uid' => $uid,
                        ))
                        ->order('create_time desc')
                        ->queryAll();
    }

    public static function getCouchSearch($id) {
        $obj = CouchSearchAR::model()->findByPk($id);
        if ($obj) {
            return $obj->attributes;
        }
        return null;
    }
    
    /**
     * 发布沙发的到达与离开日期是否合法
     * @param datetime date Y-m-d eg. 2013-01-20
     * @throw CException
     * code : 0 代表是达到日期出了问题
     * code : 1 代表是离开日期出了问题
     */
    public static function isDateValid($arrive, $leave){
        $at = strtotime($arrive);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $arrive) || $at == false) {
            throw new CException('到达日期格式不合法', 0);
        }
        if ($at < strtotime(date('Y-m-d'))) {
            throw new CException("到达日期不应晚于当前时间", 0);
        }
        $lt = strtotime($leave);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $leave) || $lt == false) {
            throw new CException('离开日期格式不合法', 1);
        }
        if($lt < $at){
            throw new CException('离开日期不应早于到达日期', 1);
        }
    }
    
    
    /**
     * 获取一个用户未处理的沙发申请数量
     * 
     * @param integer $uid 用户id
     * @return integer
     */
    public static function getUndealRequestCount($uid) {
        return Yii::app()->db->createCommand()
                ->select('count(id)')
                ->from('couch_surf')
                ->where('host_id = :uid and type = '. CouchSurfAR::TYPE_SURF_REQUEST_HOST . ' and status = ' . CouchSurfAR::STATUS_PEDDING, array(
                    ':uid' => $uid,
                ))
                ->queryScalar();
    }
    
    /**
     * 获取一个用户未处理的沙发邀请数量
     * 
     * @param integer $uid 用户id
     * @return integer
     */
    public static function getUndealInviteCount($uid) {
        return Yii::app()->db->createCommand()
                ->select('count(id)')
                ->from('couch_surf')
                ->where('surf_id = :uid and type = '. CouchSurfAR::TYPE_HOST_INVITE_SURF. ' and status = ' . CouchSurfAR::STATUS_PEDDING, array(
                    ':uid' => $uid,
                ))
                ->queryScalar();
    }
    
    /**
     * 判断沙发客和沙发主之间是否存在pending的沙发
     */
    public static function isPendingCouchExists($host_id, $surf_id) {
        return CouchSurfAR::model()->exists('host_id=? and surf_id=? and status=?', array($host_id, $surf_id, CouchSurfAR::STATUS_PEDDING));
    }

}

?>
