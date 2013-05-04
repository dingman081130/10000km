<?php

/**
 * This is the model class for table "couch_search".
 *
 * The followings are the available columns in table 'couch_search':
 * @property integer $id
 * @property integer $user_id
 * @property integer $city_id
 * @property string $arrive_date
 * @property string $leave_date
 * @property integer $number
 * @property string $detail
 * @property string $create_time
 * @property integer $status
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property User $user
 */
class CouchSearchAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CouchSearchAR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'couch_search';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id, arrive_date, leave_date, number, detail', 'required'),
            array('city_id', 'numerical', 'integerOnly' => true),
            array('number', 'numerical', 'integerOnly' => true, 'max'=>6, 'min'=>1),
            array('arrive_date, leave_date', 'dateValidator'),
            array('detail', 'length', 'max' => 1024),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, arrive_date, leave_date, number, detail, create_time, status, deleted', 'safe', 'on' => 'search'),
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
            'city' => array(self::BELONGS_TO, 'City', 'city_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'city_id' => 'City',
            'arrive_date' => 'Arrive Date',
            'leave_date' => 'Leave Date',
            'number' => 'Number',
            'detail' => 'Detail',
            'create_time' => 'Create Time',
            'status' => 'Status',
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
        $criteria->compare('city_id', $this->city_id);
        $criteria->compare('arrive_date', $this->arrive_date, true);
        $criteria->compare('leave_date', $this->leave_date, true);
        $criteria->compare('number', $this->number);
        $criteria->compare('detail', $this->detail, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('deleted', $this->deleted);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    public function dateValidator($attribute, $params) {
        $t = strtotime($this->$attribute);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->$attribute) || $t == false) {
            $this->addError($attribute, '日期格式不合法');
        }

        if ($attribute == 'arrive_date') {
            if ($t < strtotime(date('Y-m-d'))) {
                $this->addError($attribute, '到达日期不应早于当前时间');
            }
        } else if ($attribute == 'leave_date') {
            if ($t < strtotime($this->arrive_date)) {
                $this->addError($attribute, '离开日期不能比到达日期更早');
            }
        }
    }
    
    /**
     * 获取第一个错误
     * @return null
     */
    public function getFirstError(){
        $errors = $this->getErrors();
        if(!$errors){
            return null;
        }
        $errors = array_pop($errors);
        return array_pop($errors);
    }
    
    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
            $this->deleted = 0;
            $this->user_id = Yii::app()->user->id;
        }
        return parent::beforeSave();
    }

}