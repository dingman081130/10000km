<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.UserAR');

/**
 * Description of ChangePasswordForm
 *
 * @author yemin
 */
class ChangePasswordForm extends CFormModel{
    //put your code here
    
    public $password;
    public $new_password;
    
    public function rules() {
        return array(
            array('password, new_password', 'required'),
            array('password', 'passwordValidate'),
            array('new_password', 'length', 'min' => 6),
        );
    }
    
    public function passwordValidate($attribute, $params) {
        $user = UserAR::model()->findByPk(Yii::app()->user->id);
        if ($user && $user->validatePassword($this->password)) {
            return true;
        }
        $this->addError($attribute, '密码错误');
        return false;
    }
    
    
    public function updatePassword() {
        if ($this->validate()) {
            $user = UserAR::model()->findByPk(Yii::app()->user->id);
            if ($user) {
                $user->password = $this->new_password;
                return $user->save();
            }
        }
        return false;
    }
    
}

?>
