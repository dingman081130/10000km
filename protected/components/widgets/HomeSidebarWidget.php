<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.User.Message');
Yii::import('application.models.Couch.Couch');

/**
 * Description of homeSidebarWidget
 *
 * @author yemin
 */
class HomeSidebarWidget extends CWidget{
    //put your code here
    
    public $tab;
    
    private $couch_request_count;
    private $couch_invite_count;
    
    protected function renderContent() {
        $this->render('homeSidebarWidget', array(
            'tab' => $this->tab,
            'unread_cnt' => Message::getUnreadMsgCnt(Yii::app()->user->id),
            'couch_request_count' => $this->couch_request_count,
            'couch_invite_count' => $this->couch_invite_count,
        ));
    }
    
    public function run() {
        $this->couch_request_count = Couch::getUndealRequestCount(Yii::app()->user->id);
        $this->couch_invite_count = Couch::getUndealInviteCount(Yii::app()->user->id);
        $this->renderContent();
    }
}

?>
