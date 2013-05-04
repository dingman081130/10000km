<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.UserAR');
Yii::import('application.models.AR.UserProfileAR');
Yii::import('application.models.User.Account');

/**
 * Description of UserProfileForm
 *
 * @author yemin
 */
class UserProfileForm extends CFormModel {

    //put your code here
    // form fields
    public $name;
    public $sex;
    public $live_city_id;
    public $occupation;
    public $education;
    public $website;
    public $email;
    public $qq;
    public $msn;
    public $description;
    public $birthday;

    public function rules() {
        return array(
            array('name, sex,  birthday', 'required'),
            array('live_city_id', 'required', 'message' => '居住城市不能为空'),
            array('website', 'url'),
            array('qq', 'numerical', 'integerOnly' => true),
            array('email, msn', 'email'),
            array('description', 'length', 'max' => 1024),
            array('occupation, education, website', 'length', 'max' => 64),
            array('qq, email, msn', 'length', 'max' => 45),
            array('sex', 'in', 'range' => array(0, 1)),
            array('name', 'nameValidate'),
            array('birthday', 'birthdayValidate'),
            array('live_city_id', 'liveCityIdValidate'),
        );
    }

    public function nameValidate($attribute, $params) {
        $ret = Account::usernameAvailable(Yii::app()->user->id, $this->name);
        if ($ret['code'] != 0) {
            $this->addError($attribute, $ret['msg']);
        }
    }

    public function liveCityIdValidate($attribute, $params) {
        if (!CityAR::model()->exists('id', $this->live_city_id)) {
            $this->addError($attribute, '不存在该城市');
        }
    }

    public function birthdayValidate($attribute, $params) {
        
        $t = strtotime($this->birthday);
        if ($t === false) {
            $this->addError($attribute, '生日格式不正确');
            return;
        }

        $date_parts = getdate($t);
        if ($date_parts['year'] > 2012 || $date_parts['year'] < 1950) {
            $this->addError($attribute, '生日格式不正确');
            return;
        }
    }

    public function updateProfile() {
        if ($this->validate()) {
            $profile = UserProfileAR::model()->findByPk(Yii::app()->user->id);
            if ($profile) {

                UserAR::model()->updateByPk(Yii::app()->user->id, array('name' => $this->name));

                $profile->attributes = $this->attributes;
                $profile->save();

                User::delUserCache(Yii::app()->user->id);

                return true;
            }
        }
        return false;
    }

}

?>
