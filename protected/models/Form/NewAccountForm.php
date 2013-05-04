<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.UserAR');
Yii::import('application.models.User.Account');
Yii::import('application.models.User.Invitation');

/**
 * Description of NewAccountForm
 *
 * @author yemin
 */
class NewAccountForm extends CFormModel {

    //put your code here

    public $name;
    public $login_email;
    public $password;
    public $share;
    //public $invitation;

    public function rules() {
        return array(
            //array('name, login_email, password,invitation', 'required'),
            array('name, login_email, password', 'required'),
            array('login_email', 'email'),
            array('password', 'length', 'min'=>6),
            
            array('name', 'nameValidate'),
            array('login_email', 'emailValidate'),
            array('share', 'safe'),
            //array('invitation', 'invitationValidate'),
        );
    }
    
    public function nameValidate($attribute, $params) {
        $ret = Account::usernameAvailable(null, $this->name);
        
        if ($ret['code'] != 0) {
            $this->addError('name', $ret['msg']);
        }
    }
    
    public function emailValidate($attribute, $params) {
        if (UserAR::model()->exists('login_email = :login_email', array(
            'login_email' => $this->login_email,
        ))) {
            $this->addError('login_email', '该邮箱已被注册<br>如果您已经使用该邮箱注册过一万公里，可以<a href="/account/bindAccount">绑定到该账号</a>');
        }
    }
    
    /*
    public function invitationValidate($attribute, $params) {
        return true;    // debug
        try{
            Invitation::isValid($this->invitation);
        }catch(CException $e){
            $this->addError('invitation', $e->getMessage());
        }
    }*/

}

?>
