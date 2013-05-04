<?php

/**
 * This is the model class for table "trip_comment".
 *
 * The followings are the available columns in table 'trip_comment':
 * @property integer $id
 * @property integer $parent_id
 * @property integer $user_id
 * @property integer $trip_id
 * @property string $create_time
 * @property string $content
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property Trip $trip
 * @property User $user
 * @property TripComment $parent
 * @property TripComment[] $tripComments
 */
class TripCommentAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return TripComment the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'trip_comment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('trip_id, content, parent_id', 'required'),
            array('parent_id, trip_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, parent_id, user_id, trip_id, create_time, content, deleted', 'safe', 'on' => 'search'),
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
            'parent' => array(self::BELONGS_TO, 'TripComment', 'parent_id'),
            'tripComments' => array(self::HAS_MANY, 'TripComment', 'parent_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'parent_id' => 'Parent',
            'user_id' => 'User',
            'trip_id' => 'Trip',
            'create_time' => 'Create Time',
            'content' => 'Content',
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
        $criteria->compare('parent_id', $this->parent_id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('trip_id', $this->trip_id);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('deleted', $this->deleted);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
            $this->user_id = Yii::app()->user->id;
        }
        
        if ($this->parent_id == 0) 
            $this->parent_id = null;
        
        return parent::beforeSave();
    }
    
    protected function afterSave() {
        $key = "trip_{$this->trip_id}_comments";
        RedisClient::getClient()->del($key);
        return parent::afterSave();
    }

}