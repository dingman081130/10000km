<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.User.User');

/**
 * Description of UserCommentWidget
 *
 * @author yemin
 */
class UserCommentWidget extends CWidget{
    //put your code here
    
    public $comment;
    private $author;
    
    protected function renderContent() {
        $this->render('userCommentWidget', array(
            'author' => $this->author,
            'comment' => $this->comment,
        ));
    }

    public function run() {
        $this->author = User::getUser($this->comment['author_id']);
        $this->renderContent();
    }
}

?>
