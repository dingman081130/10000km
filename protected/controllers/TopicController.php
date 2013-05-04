<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.TopicAR');
Yii::import('application.models.Group.Group');
Yii::import('application.models.Group.Topic');
Yii::import('application.models.Group.TopicComment');
Yii::import('application.models.Form.TopicImageForm');
Yii::import('application.models.User.Message');
Yii::import('application.models.Message.SysMessage');

/**
 * Description of TopicController
 *
 * @author yemin
 */
class TopicController extends Controller{
    //put your code here
    protected $defaultPageInfo = array(
        'create' => array(
            'title' => '发表话题',
            ),
        'edit' => array(
            'title' => '编辑话题',
            ),
    );
    
    public function filters() {
        return array(
            'accessControl',
            'postOnly + comment, uploadImage',
        );
    }

    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('comment', 'edit', 'create', 'uploadImage'),
                'users' => array('?'),
            ),
        );
    }
    
    public function actionView($id) {
        $topic = Topic::getTopic($id);
        if (!$topic)
            throw new CHttpException(404);
        $this->pageTitle = $topic['title'];
        $this->pageDescription = Helpers::substr(strip_tags($topic['content']), 80);
        $group = Group::getGroup($topic['group_id']);
        $this->render('view', array(
            'topic' => $topic,
            'group' => $group,
        ));
    }
    
    /**
     * 
     * @param integer $id group id
     * @return type
     * @throws CHttpException
     */
    public function actionCreate($id) {
        $group = Group::getGroup($id);
        if (!$group)
            throw new CHttpException(404);
        
        $model = new TopicAR();
        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST;
            if ($model->save()) {
                Topic::delTopicCache($model->id);
                Group::delGroupCache($id);
                //// 推送消息
                SysMessage::saveCTopMsg($model->id);
                //// 推送新鲜事
                EventListener::getListener()->run(array(
                    'user_id' => Yii::app()->user->id,
                    'content' => CJSON::encode(array('topic_id' => $model->id)),
                    'type'    => Event::PTOP
                ));
                return $this->redirect('/topic/'.$model['id']);
            }
        }
        
        $this->render('create', array(
            'form' => $model,
            'group' => $group,
        ));
    }
    
    
    public function actionEdit($id=null) {
        $topic = TopicAR::model()->findByAttributes(array(
            'id' => $id,
            'author_id' => Yii::app()->user->id,
        ));
        if (!$topic) {
            throw new CHttpException(404);
        }
        if (Yii::app()->request->isPostRequest) {
            $topic->attributes = $_POST;
            if ($topic->save())
                $this->redirect ('/topic/'.$topic['id']);
        }
        $this->render('edit', array(
            'form' => $topic,
        ));
    }
    
    
    public function actionUploadImage() {
        $form = new TopicImageForm();
        $form->image = CUploadedFile::getInstanceByName('file');
        $file_name = $form->saveImage();
        if ($file_name) {
            $this->returnJson(array(
                'filelink' => ImageUrlHelper::imgUrl(ImageUrlHelper::TOPIC_IMAGE, $file_name),
            ));
        }
    }
    
    /**
     * 提交评论
     */
    public function actionComment() {
        $tid = $_REQUEST['tid'];
        $cid = $_REQUEST['cid'];
        $con = $_REQUEST['content'];
        //var_dump(Yii::app()->user->name);exit;
        try{
            $comment = TopicComment::saveComment($tid, $con, $cid, Yii::app()->user->id);
            /* begin 通知 */
            SysMessage::saveRtopMsg(Yii::app()->user->id, $tid);
            SysMessage::saveRtopcMsg(Yii::app()->user->id, $cid);
            /* end 通知 */
            $code = $this->renderPartial('commentItem', array('comment' => $comment), TRUE);
            $this->returnJson(array('code' => 0, 'msg' => 'ok', 'data' => array('code' => $code)));
        }catch(CException $e){
            $this->returnJson(array('code' => -1, 'msg' => $e->getMessage(), 'data' => array()));
        }
    }
    
    public function actionCommentHtml(){
        $tid    = $_GET['tid'];
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $comments = TopicComment::getComments($tid, $offset, Yii::app()->params['topicCommentPageSize']);
        
        $html = '';
        foreach($comments as $comment){
            $html .= $this->renderPartial('commentItem', array('comment' => $comment), TRUE);
        }
        echo $html;
    }
}

?>
