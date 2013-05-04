<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.User.UserFollow');
Yii::import('application.models.Couch.Couch');

/**
 * Description of UserPageHeader
 *
 * @author yemin
 */
class UserPageHeaderWidget extends CWidget {

    //put your code here
    public $user;
    private $ifollowed;
    private $couch;

    protected function renderContent() {
        $this->render('userPageHeaderWidget', array(
            'user' => $this->user,
            'ifollowed' => $this->ifollowed,
            'couch' => $this->couch,
        ));
    }

    public function run() {
        $this->ifollowed = !empty(Yii::app()->user->id) && UserFollow::isFollowUser(Yii::app()->user->id, $this->user['id']);
        $this->couch = Couch::getUserCouch($this->user['id']);
        $this->renderContent();
    }

}

?>
