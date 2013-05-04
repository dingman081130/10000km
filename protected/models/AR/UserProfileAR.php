<?php

/**
 * This is the model class for table "user_profile".
 *
 * The followings are the available columns in table 'user_profile':
 * @property integer $id
 * @property integer $sex
 * @property string $birthday
 * @property integer $live_city_id
 * @property string $occupation
 * @property string $education
 * @property string $description
 * @property string $website
 * @property string $email
 * @property string $qq
 * @property string $want_places
 * @property string $personal_tags
 *
 * The followings are the available model relations:
 * @property City $liveCity
 */
class UserProfileAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return UserProfileAR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'user_profile';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id', 'required'),
            array('id, sex, live_city_id', 'numerical', 'integerOnly' => true),
            array('occupation, education, website', 'length', 'max' => 64),
            array('description', 'length', 'max' => 1024),
            array('email, qq', 'length', 'max' => 45),
            array('want_places, personal_tags', 'length', 'max' => 128),
            array('birthday', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, sex, birthday, live_city_id, occupation, education, description, website, email, qq, want_places, personal_tags', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'liveCity' => array(self::BELONGS_TO, 'City', 'live_city_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'sex' => 'Sex',
            'birthday' => 'Birthday',
            'live_city_id' => 'Live City',
            'occupation' => 'Occupation',
            'education' => 'Education',
            'description' => 'Description',
            'website' => 'Website',
            'email' => 'Email',
            'qq' => 'Qq',
            'want_places' => '想去的地方',
            'personal_tags' => '个性标签',
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
        $criteria->compare('sex', $this->sex);
        $criteria->compare('birthday', $this->birthday, true);
        $criteria->compare('live_city_id', $this->live_city_id);
        $criteria->compare('occupation', $this->occupation, true);
        $criteria->compare('education', $this->education, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('website', $this->website, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('qq', $this->qq, true);
        $criteria->compare('want_places', $this->want_places, true);
        $criteria->compare('personal_tags', $this->personal_tags, true);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

}