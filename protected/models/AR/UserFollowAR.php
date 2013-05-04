<?php

/**
 * This is the model class for table "user_follow".
 *
 * The followings are the available columns in table 'user_follow':
 * @property integer $id
 * @property integer $user1_id
 * @property integer $user2_id
 * @property string $time
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property User $user1
 * @property User $user2
 */
class UserFollowAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return UserFollowAR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'user_follow';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user2_id', 'required'),
            array('user2_id', 'numerical', 'integerOnly' => true),
            array('user2_id', 'userIdValidate'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user1_id, user2_id, time, deleted', 'safe', 'on' => 'search'),
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
            'time' => 'Time',
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
        $criteria->compare('user1_id', $this->user1_id);
        $criteria->compare('user2_id', $this->user2_id);
        $criteria->compare('time', $this->time, true);
        $criteria->compare('deleted', $this->deleted);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    
    public function userIdValidate($attribute, $params) {
        if ($this->user2_id == Yii::app()->user->id) {
            $this->addError($attribute, '不能关注自己啊');
        }
    }
    
    
    protected function beforeSave() {
        if ($this->isNewRecord)  {
            $this->user1_id = Yii::app()->user->id;
            $this->deleted = 0;
        }
        if(!$this->time){
            $this->time = date('Y-m-d H:i:s');
        }
        return parent::beforeSave();
    }
        
    /**
     * 对self::save()函数的简单封装
     * 不管执行是否成功均打log
     * 
     * @param bool $runValidation
     * @param array $attributes
     * @return bool
     */
    public function saveL($runValidation=true, $attributes=NULL){
        $ret = $this->save($runValidation, $attributes);
        if(!$ret){
            MuleApp::log('写入'.__CLASS__.'失败'.Utils::arrToString($this->getErrors()), 'error');
            return false;
        }
        MuleApp::log('写入'.__CLASS__.'成功'.Utils::arrToString($this->getAttributes()));
        return true;
    }

}
