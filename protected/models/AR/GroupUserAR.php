<?php

/**
 * This is the model class for table "group_user".
 *
 * The followings are the available columns in table 'group_user':
 * @property integer $id
 * @property integer $user_id
 * @property integer $group_id
 * @property string $create_time
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Group $group
 */
class GroupUserAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return GroupUserAR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'group_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, group_id', 'required'),
            array('user_id, group_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, group_id, create_time, deleted', 'safe', 'on' => 'search'),
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
            'group' => array(self::BELONGS_TO, 'Group', 'group_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'group_id' => 'Group',
            'create_time' => 'Create Time',
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
        $criteria->compare('group_id', $this->group_id);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('deleted', $this->deleted);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    protected function beforeValidate() {
        if ($this->isNewRecord) {
            if ($this->exists('user_id = :user_id and group_id = :group_id', array(
                ':user_id' => $this->user_id,
                ':group_id' => $this->group_id,
            ))) {
                $this->addError('user_id', '已经关注该group');
                return false;
            }
        }
        return parent::beforeValidate();
    }


    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
            $this->deleted = 0;
        }
        return parent::beforeSave();
    }

}