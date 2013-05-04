<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccountNavWidget
 *
 * @author yemin
 */


Yii::import('application.models.User.Message');

class AccountNavWidget extends CWidget {
    //put your code here
    
    protected function renderContent() {
        $unread_msg_cnt = Message::getUnreadMsgCnt(Yii::app()->user->id);
        $this->render('accountNavWidget', array('unread_msg_cnt' => $unread_msg_cnt));
    }
    
    public function run() {
        $this->renderContent();
    }
}

?>
