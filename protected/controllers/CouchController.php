<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.Couch.Couch');
Yii::import('application.models.User.User');

/**
 * Description of CouchController
 *
 * @author yemin
 */
class CouchController extends Controller {

    //put your code here


    public function filters() {
        return array(
            'accessControl',
            'postOnly + reject, accept',
        );
    }

    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('accept', 'cancel', 'hostDetailModal', 'reject', 'surfDetailModal'),
                'users' => array('?'),
            ),
        );
    }


    public function actionReject() {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        $reason = isset($_POST['reason']) ? $_POST['reason'] : '';
        if (Couch::reject($id, Yii::app()->user->id, $reason)) {
            $this->returnJson(array(
                'code' => 0,
                'msg' => '',
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => '操作失败',
            ));
        }
    }

    public function actionAccept() {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        if (Couch::accept($id, Yii::app()->user->id)) {
            $this->returnJson(array(
                'code' => 0,
                'msg' => '',
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => '操作失败',
            ));
        }
    }

    public function actionCancel() {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        if (Couch::cancel($id, Yii::app()->user->id)) {
            $this->returnJson(array(
                'code' => 0,
                'msg' => '',
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => '操作失败',
            ));
        }
    }
    
    
    public function actionInvite() {
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : null;
        $user = User::getUser($uid);
        $ret = Couch::inviteCouch($uid, Yii::app()->user->id, $_POST);
        if ($ret) {
            $this->returnJson(array(
                'code' => 0,
                'msg' => '已经向'. $user['name'] .'发送沙发邀请，请等待沙发客回应',
            ));
        } else {
            $this->returnJson(array(
                'code' => 0,
                'msg' => '发送邀请失败',
            ));
        }
    }
    
    public function actionIsDateValid($arrive, $leave){
        try{
            Couch::isDateValid(trim($arrive), trim($leave));
            $this->returnJson(array('code' => 0));
        }catch(CException $e){
            $this->returnJson(array(
                'code' =>-1,
                'msg' => $e->getMessage(),
                'data'=> $e->getCode()
            ));
        }
    }
    
    public function actionInviteModal($id) {
        $s = Couch::getCouchSearch($id);
        if ($s) {
            $user = User::getUser($s['user_id']);
            $html = $this->renderPartial('inviteModal', array(
                'couch_search' => $s,
                'user' => $user,
                'host' => User::getUser(Yii::app()->user->id),
            ), true);
            $this->returnJson(array(
                'code' => 0,
                'html' => $html,
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => 'failed',
            ));
        }
    }


}

?>
