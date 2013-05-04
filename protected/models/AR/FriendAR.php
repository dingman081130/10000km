<?php

/**
 * This is the model class for table "friend".
 *
 * The followings are the available columns in table 'friend':
 * @property integer $id
 * @property integer $user1_id
 * @property integer $user2_id
 * @property integer $status
 * @property string $time
 *
 * The followings are the available model relations:
 * @property User $user1
 * @property User $user2
 */
class FriendAR extends CActiveRecord {
    
    const STATUS_PEDDING = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_REJECTED = 2;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Friend the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'friend';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user1_id, user2_id, status, time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user1' => array(self::BELONGS_TO, 'User', 'user1_id'),
            'user2' => array(self::BELONGS_TO, 'User', 'user2_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user1_id' => 'User1',
            'user2_id' => 'User2',
            'status' => 'Status',
            'time' => 'Time',
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
        $criteria->compare('user1_id', $this->user1_id);
        $criteria->compare('user2_id', $this->user2_id);
        $criteria->compare('status', $this->status);
        $criteria->compare('time', $this->time, true);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_PEDDING;
            $this->time = date('Y-m-d H:i:s');
            $this->user1_id = Yii::app()->user->id;
        }
        return parent::beforeSave();
    }

}