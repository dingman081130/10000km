<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.TopicCommentAR');


/**
 * Description of TopicComment
 *
 * @author yemin
 */
class TopicComment {
    //put your code here
    
    /**
     * 获取一个话题的所有评论
     * 按时间倒序排序
     * 
     * @param int $tip 话题id
     */
    public static function getComments($tid, $offset = 0, $limit = 0){
        
        $limit == 0 && $limit = Yii::app()->params['topicCommentPageSize'];
        $tid = intval($tid);
        $data = Yii::app()->db->createCommand()
                ->select('*')
                ->from('topic_comment')
                ->where('deleted = 0 and topic_id = :tid', array(':tid' => $tid))
                ->order('create_time desc')
                ->offset($offset)
                ->limit($limit)
                ->queryAll();
        return $data;
    }
    
    /**
     * 获取一个话题的评论数量
     * 
     * @param int $tip 话题id
     */
    public static function getCommentsCnt ($tid) {
        return TopicCommentAR::model()->count('deleted = 0 and topic_id = ?', array($tid));
    }
    
    /**
     * 通过评论id获取评论属性
     * @param int $id
     * @return array
     */
    public static function getComment($id){
        $ar = TopicCommentAR::model()->findByPk(intval($id), 'deleted = 0');
        if($ar){
            return $ar->getAttributes();
        }
        return array();
    }
    
    /**
     * 保存评论id
     * 
     * @param int $tid 话题id
     * @param int $upid 上一级评论id
     * @param int $uid 用户id
     * @param string $con 回复内容
     * @throws CException
     * @return array 评论属性
     */
    public static function saveComment ($id, $con, $upid, $uid) {
        $ar = new TopicCommentAR;
        $ar->topic_id = intval($id);
        $ar->author_id = intval($uid);
        $ar->content = trim($con);
        $ar->upid = intval($upid);
        if(!$ar->save()){
            throw new CException($ar->getFirstError());
        }
        Topic::delTopicCache($id);
        return $ar->getAttributes();
        
    }
}

?>
