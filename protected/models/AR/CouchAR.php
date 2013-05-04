<?php

Yii::import('application.models.AR.AlbumAR');

/**
 * This is the model class for table "couch".
 *
 * The followings are the available columns in table 'couch':
 * @property integer $id
 * @property integer $user_id
 * @property integer $available
 * @property integer $capacity
 * @property integer $no_smoke
 * @property integer $guest_sex
 * @property string $update_time
 * @property integer $album_id
 * @property string description
 *
 * The followings are the available model relations:
 * @property User $user
 */
class CouchAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Couch the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'couch';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('available', 'required'),
            array('available', 'in', 'range' => array(0, 1)),
            array('capacity', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 6),
            array('no_smoke', 'in', 'range' => array(0, 1)),
            array('guest_sex', 'in', 'range' => array(-1, 0, 1)),
            array('description', 'length', 'max' => 512),
            array('album_id', 'albumIdValidate'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, available, capacity, no_smoke, guest_sex, update_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
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
            'available' => 'Available',
            'capacity' => 'Capacity',
            'no_smoke' => 'No Smoke',
            'guest_sex' => 'Guest Sex',
            'update_time' => 'Update Time',
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
        $criteria->compare('available', $this->available);
        $criteria->compare('capacity', $this->capacity);
        $criteria->compare('no_smoke', $this->no_smoke);
        $criteria->compare('guest_sex', $this->guest_sex);
        $criteria->compare('update_time', $this->update_time, true);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    
    public function albumIdValidate($attribute, $params) {
        if (!empty($this->$attribute) && !AlbumAR::model()->exists('id = :aid and user_id = :uid', array(
            ':uid' => $this->user_id,
            ':aid' => $this->$attribute,
        ))) {
            $this->addError($attribute, '不存在该相册');
            return false;
        }
    }
    
    
    protected function beforeSave() {
        $this->user_id = Yii::app()->user->id;
        $this->update_time = date('Y-m-d H:i:s');
        return parent::beforeSave();
    }
    
    
    protected function afterSave() {
        RedisClient::getClient()->del("couch_{$this->user_id}");
        return parent::afterSave();
    }

}