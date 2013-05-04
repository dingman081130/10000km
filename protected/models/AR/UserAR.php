<?php

Yii::import('application.models.AR.UserProfileAR');

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $name
 * @property string $login_email
 * @property string $avatar
 * @property string $hashpwd
 * @property integer $points
 * @property string $register_time
 * @property string $last_login_time
 * @property string $last_login_ip
 * @property integer $disabled
 * @property integer $email_verified
 *
 * The followings are the available model relations:
 * @property SocialAccount[] $socialAccounts
 * @property Trip[] $trips
 */
class UserAR extends CActiveRecord {

    public $password;
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, login_email, password', 'required'),
            array('points, disabled, email_verified', 'numerical', 'integerOnly' => true),
            array('name, login_email', 'length', 'max' => 45),
            
            array('password', 'length', 'min'=>6),
            array('login_email', 'email'),
            array('register_time', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, login_email, avatar, hashpwd, points, register_time, last_login_time, disabled, email_verified', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'socialAccounts' => array(self::HAS_MANY, 'SocialAccount', 'user_id'),
            'trips' => array(self::HAS_MANY, 'Trip', 'creator_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'login_email' => 'Email',
            'avatar' => 'Avatar',
            'hashpwd' => 'Hashpwd',
            'points' => 'Points',
            'register_time' => 'Register Time',
            'last_login_time' => 'Last Login Time',
            'last_login_IP' => 'Last Login IP',
            'disabled' => 'Disabled',
            'email_verified' => 'Email Verified',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('login_email', $this->email, true);
        $criteria->compare('avatar', $this->avatar, true);
        $criteria->compare('hashpwd', $this->hashpwd, true);
        $criteria->compare('points', $this->points);
        $criteria->compare('register_time', $this->register_time, true);
        $criteria->compare('last_login_time', $this->last_login_time, true);
        $criteria->compare('disabled', $this->disabled);
        $criteria->compare('email_verified', $this->email_verified);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
   

    protected function beforeSave() {
        if (!empty($this->password)) {
            $salt = uniqid();
            $this->hashpwd = $salt . ':' . md5($salt . $this->password);
        }
        return parent::beforeSave();
    }
    
    protected function afterSave() {
        if ($this->isNewRecord) {
            UserProfileAR::model()->deleteByPk($this->id);
            $profile = new UserProfileAR();
            $profile->id = $this->id;
            $profile->save();
        }
        return parent::afterSave();
    }


    public function validatePassword($password) {
        list($salt, $hash) = explode(':', $this->hashpwd);
        return md5($salt . $password) === $hash;
    }

}
