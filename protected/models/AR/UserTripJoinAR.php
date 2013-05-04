<?php

/**
 * This is the model class for table "user_trip_join".
 *
 * The followings are the available columns in table 'user_trip_join':
 * @property integer $id
 * @property integer $user_id
 * @property integer $trip_id
 * @property string $time
 * @property integer $approved
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property Trip $trip
 * @property User $user
 */
class UserTripJoinAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return UserTripJoin the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'user_trip_join';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('trip_id', 'required'),
            array('trip_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, trip_id, time, approved, deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'trip' => array(self::BELONGS_TO, 'Trip', 'trip_id'),
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'trip_id' => 'Trip',
            'time' => 'Time',
            'approved' => 'Approved',
            'deleted' => 'Deleted',
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
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('trip_id', $this->trip_id);
        $criteria->compare('time', $this->time, true);
        $criteria->compare('approved', $this->approved);
        $criteria->compare('deleted', $this->deleted);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    protected function beforeValidate() {
        if ($this->model()->exists('user_id = :user_id and trip_id = :trip_id', array(
            ':user_id' => $this->user_id,
            ':trip_id' => $this->trip_id,
        ))) {
            $this->addError('trip_id', '用户已经参加了该旅行');
            return false;
        }
        return parent::beforeValidate();
    }


    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->deleted = 0;
        }
        $this->time = date('Y-m-d H:i:s');
        return parent::beforeSave();
    }

}