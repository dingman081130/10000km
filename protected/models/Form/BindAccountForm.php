<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Yii::import('application.models.AR.UserAR');
/**
 * Description of BindAccountForm
 *
 * @author yemin
 */
class BindAccountForm extends CFormModel{
    //put your code here
    
    public $login_email;
    public $password;
    private $user_id;
    
    public function rules() {
        return array(
            array('login_email, password', 'required'),
            array('login_email', 'email'),
            array('password', 'passwordValidate'),
        );
    }
    
    
    public function passwordValidate($attribute, $params) {
        $user = UserAR::model()->find('login_email = :login_email', array(
            'login_email' => $this->login_email,
        ));
        if ($user && $user->validatePassword($this->password)) {
            $this->user_id = $user->id;
        } else {
            $this->addError('password', '邮箱或密码错误！');
        }
    }
    
    public function getUserId() {
        return $this->user_id;
    }
}

?>
