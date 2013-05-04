<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.Group.Group');
Yii::import('application.models.AR.TopicAR');

/**
 * Description of Topic
 *
 * @author yemin
 */
class Topic {
    //put your code here
    
    const TTL = 3600;
    
    
    private static function topicCacheKey($id) {
        return "topic_$id";
    }
    
    public static function delTopicCache($id) {
        RedisClient::getClient()->del(self::topicCacheKey($id));
    }
    
    /**
     * 获取一个topic
     * 
     * @param integer $id topic id
     * @return array 成功时返回array，否则null
     */
    public static function getTopic($id) {
        $redis = RedisClient::getClient();
        $key = self::topicCacheKey($id);
        $topic = $redis->get($key);
        if (!$topic) {
            $topic = Yii::app()->db->createCommand()
                    ->select('topic.*, max(topic_comment.create_time) as last_reply_time, count(topic_comment.id) as reply_count')
                    ->from('topic')
                    ->leftJoin('topic_comment', 'topic.id = topic_comment.topic_id')
                    ->where('topic.id = :id', array(':id' => $id))
                    ->group('topic.id')
                    ->queryRow();
            if ($topic) {
                $redis->setex($key, self::TTL, $topic);
                return $topic;
            }
            return null;
        }

        return $topic;
    }
    
    
    public static function getTopics($ids) {
        $data = array();
        foreach ($ids as $id) {
            $data[] = self::getTopic($id);
        }
        return $data;
    }
    
    
    /**
     * 设置一个话题
     * @param int $id 话题id
     * @param string $title 话题标题
     * @param string $content 话题内容
     * @throws CException 失败则抛出异常
     */
    public static function setTopic ($id, $title = '', $content = ''){
        $ar = TopicAR::model()->findByPK($id);
        $need_save = false;
        if (!$ar) {
            throw new CException ('未找到话题');
        }
        if ($title && $title != $ar->title) {
            $ar->title = $title;
            $need_save = true;
        }
        if ($content && $content != $ar->content) {
            $ar->content = $content;
            $need_save = true;
        }
        if ( $need_save && !$ar->save()) {
            throw new CException ($ar->getFirstError());
        }
        self::delTopicCache($id);
    }
    
    
    public static function getTopicsByGroup($gid, $offset=0, $size=10) {
        $ids = Yii::app()->db->createCommand()
                ->select('topic.id')
                ->from('topic')
                ->where('group_id = :gid', array(':gid' => $gid))
                ->order('create_time desc')
                ->offset($offset)
                ->limit($size)
                ->queryColumn();
        return self::getTopics($ids);
    }
    
    public static function getTopicsCountByGroup($gid) {
        return Yii::app()->db->createCommand()
                ->select('count(id)')
                ->from('topic')
                ->where('group_id = :gid and deleted = 0', array(':gid' => $gid))
                ->queryScalar();
    }
    
    
    public static function getTopicsByGroups($gids, $offset=0, $size=10) {
        $builder = Yii::app()->db->schema->commandBuilder;
        $where = $builder->createInCondition('topic', 'group_id', $gids);
        
        return Yii::app()->db->createCommand()
                ->select('topic.*, max(topic_comment.create_time) as last_reply_time, count(topic_comment.id) as reply_count')
                ->from('topic')
                ->leftJoin('topic_comment', 'topic.id = topic_comment.topic_id')
                ->group('topic.id')
                ->where($where)
                ->order('last_reply_time desc')
                ->offset($offset)
                ->limit($size)
                ->queryAll();
                
    }
    
    
    public static function getTopicsCreatedByUser($uid, $offset=0, $size=10) {
        $ids = Yii::app()->db->createCommand()
                ->select('topic.id')
                ->from('topic')
                ->where('author_id = :uid', array(':uid' => $uid))
                ->order('topic.create_time desc')
                ->offset($offset)
                ->limit($size)
                ->queryColumn();
        return self::getTopics($ids);
    }
    
    public static function getTopicsRepliedByUser($uid, $offset=0, $size=10) {
        return Yii::app()->db->createCommand()
                ->select('topic.*, max(topic_comment.create_time) as reply_time')
                ->from('topic')
                ->join('topic_comment', 'topic.id = topic_comment.topic_id')
                ->where('topic_comment.author_id = :uid', array(':uid' => $uid))
                ->group('topic.id')
                ->order('reply_time desc')
                ->offset($offset)
                ->limit($size)
                ->queryAll();
    }
    
    
    public static function getTopicsByCity($city_id, $offset=0, $size=10) {
        $ids = Yii::app()->db->createCommand()
                ->select('topic.id')
                ->from('topic')
                ->join('group', 'topic.group_id = group.id')
                ->join('city', 'group.city_id = city.id')
                ->where('city.id = :city_id or city.upid = :city_id')
                ->order('topic.create_time desc')
                ->offset($offset)
                ->limit($size)
                ->bindValue(':city_id', $city_id)
                ->queryColumn();
        
        return self::getTopics($ids);
    }
    
    
}

?>
