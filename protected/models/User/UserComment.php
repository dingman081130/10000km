<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.UserCommentAR');

/**
 * Description of UserComment
 *
 * @author yemin
 */
class UserComment {
    //put your code here
    
    
    /**
     * 获取用户所有的评论
     * 
     * @param integer $uid
     * @return array
     */
    public static function getUserAllComment($uid) {
        $redis = RedisClient::getClient();
        $key = self::cacheKey($uid);
        if ($redis->exists($key)) {
            $comments = $redis->lRange($key, 0, -1);
        } else {
            $comments = Yii::app()->db->createCommand()
                    ->select('*')
                    ->from('user_comment')
                    ->where('user_id = :uid', array(':uid' => $uid))
                    ->order('create_time desc')
                    ->queryAll();
            $multi = $redis->multi();
            foreach ($comments as $comment) {
                $multi->rPush($key, $comment);
            }
            $multi->exec();
            $redis->setTimeout($key, 3600);
        }
        return $comments;
    }

    /**
     * 获取一个用户的评论
     * 
     * @param integer $uid
     * @param integer $opinion
     * @param string $from
     * @return array
     */
    public static function getUserComment($uid, $opinion = 0, $from = null, $offset = 0, $size = 10) {
        $comments = self::getUserAllComment($uid);
        $comments_filtered = array();
        foreach ($comments as $comment) {
            if ($opinion == 0 || $comment['opinion'] == $opinion) {
                if ($from == null) {
                    $comments_filtered[] = $comment;
                    continue;
                }
                if ($from == 'host' && $comment['host_days'] > 0) {
                    $comments_filtered[] = $comment;
                    continue;
                }
                if ($from == 'travel' && $comment['travel_days'] > 0) {
                    $comments_filtered[] = $comment;
                    continue;
                }
                if ($from == 'surf' && $comment['surf_days'] > 0) {
                    $comments_filtered[] = $comment;
                    continue;
                }
            }
        }
        
        return array_slice($comments_filtered, $offset, $size);
        
        /*
        $criteria = new CDbCriteria();

        $params = array();
        $criteria->addCondition('user_id = :uid');
        $params[':uid'] = $uid;
        
        if ($from == 'surf')
            $criteria->addCondition('host_days > 0');
        else if ($from == 'host')
            $criteria->addCondition('surf_days > 0');
        else if ($from == 'travel')
            $criteria->addCondition('travel_days > 0');

        if ($opinion != 0) {
            $criteria->addCondition('opinion = :opinion');
            $params[':opinion'] = $opinion;
        }
        $criteria->order = 'create_time desc';
        $criteria->offset = $offset;
        $criteria->limit = $size;
        $criteria->params = $params;

        $models = UserCommentAR::model()->findAll($criteria);
        
        $comments = array();
        foreach ($models as $model) {
            $comments[] = $model->attributes;
        }
        return $comments;
         * 
         */
    }

    /**
     * 保存一条用户评论
     * 
     * @param array $params
     * @return array 成功时返回array，否则返回null
     */
    public static function saveComment($params) {
        $model = new UserCommentAR;
        $model->attributes = $params;
        $ret = $model->save();
        if ($ret) {
            self::delCommentCache($model->user_id);
            return $model->attributes;
        }
        return null;
    }
    
    
    
    public static function getCommentCount($uid) {
        $positive_count = 0;
        $neutral_count = 0;
        $negative_count = 0;
        $travel_count = 0;
        $host_count = 0;
        $surf_count = 0;
        
        $comments = self::getUserAllComment($uid);
        foreach ($comments as $comment) {
            if ($comment['opinion'] == UserCommentAR::OPINION_POSITIVE)
                $positive_count++;
            else if ($comment['opinion'] == UserCommentAR::OPINION_NEUTRAL)
                $neutral_count++;
            else if ($comment['opinion'] == UserCommentAR::OPINION_NEGATIVE)
                $negative_count++;
            
            if ($comment['surf_days'] > 0) $surf_count++;
            if ($comment['host_days'] > 0) $host_count++;
            if ($comment['travel_days'] > 0) $travel_count++;
        }
        return array(
            'comment_count' => count($comments),
            'positive_count' => $positive_count,
            'neutral_count' => $neutral_count,
            'negative_count' => $negative_count,
            'travel_count' => $travel_count,
            'host_count' => $host_count,
            'surf_count' => $surf_count,
        );
        /*
        return Yii::app()->db->createCommand()
                ->select('count(*) as comment_count,
                          sum(case when opinion=1 then 1 else 0 end) as positive_count,
                          sum(case when opinion=2 then 1 else 0 end) as neutral_count,
                          sum(case when opinion=3 then 1 else 0 end) as negative_count,
                          sum(case when travel_days > 0 then 1 else 0 end) as travel_count,
                          sum(case when host_days > 0 then 1 else 0 end) as host_count,
                          sum(case when surf_days > 0 then 1 else 0 end) as surf_count')
                ->from('user_comment')
                ->where('user_id = :uid', array(':uid' => $uid))
                ->queryRow();
         * *
         */
    }
    
    
    
    private static function cacheKey($id) {
        return "user_{$id}_comment";
    }
    
    public static function delCommentCache($id) {
        RedisClient::getClient()->del(self::cacheKey($id));
    }

}

?>
