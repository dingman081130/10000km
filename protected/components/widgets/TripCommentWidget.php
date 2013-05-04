<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.Emotion.Emotion');
Yii::import('application.models.User.User');

/**
 * Description of TripCommentWidget
 *
 * @author yemin
 */
class TripCommentWidget extends CWidget {

    //put your code here

    public $comment;
    public $threaded_comments;
    
    private $user;


    protected function renderContent() {
        $content = CHtml::encode($this->comment['content']);
        $content = Emotion::replaceEmotion($content);
        $this->render('tripCommentWidget', array(
            'comment' => $this->comment,
            'comment_content' => $content,
            'threaded_comments' => $this->threaded_comments,
            'user' => $this->user,
        ));
    }

    public function run() {
        $this->user = User::getBasicById($this->comment['user_id']);
        $this->renderContent();
    }
}

?>
