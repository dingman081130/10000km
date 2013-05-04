<?php

/**
 * This is the model class for table "user_comment".
 *
 * The followings are the available columns in table 'user_comment':
 * @property integer $id
 * @property integer $user_id
 * @property integer $author_id
 * @property integer $opinion
 * @property string $content
 * @property integer $host_days
 * @property integer $surf_days
 * @property integer $travel_days
 * @property string $create_time
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property User $author
 * @property User $user
 */
class UserCommentAR extends CActiveRecord {
    
    const OPINION_POSITIVE = 1;
    const OPINION_NEUTRAL = 2;
    const OPINION_NEGATIVE = 3;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return UserCommentAR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'user_comment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, opinion, content', 'required'),
            array('user_id, opinion, host_days, surf_days, travel_days', 'numerical', 'integerOnly' => true),
            array('content', 'length', 'max' => 1024),
            array('opinion', 'in', 'range' => array(self::OPINION_POSITIVE, self::OPINION_NEUTRAL, self::OPINION_NEGATIVE)),
            array('host_days, surf_days, travel_days', 'numerical', 'min' => 0, 'max' => 30),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, author_id, opinion, content, host_days, surf_days, travel_days, create_time, deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'author' => array(self::BELONGS_TO, 'User', 'author_id'),
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
            'author_id' => 'Author',
            'opinion' => 'Opinion',
            'content' => 'Content',
            'host_days' => 'Host Days',
            'surf_days' => 'Surf Days',
            'travel_days' => 'Travel Days',
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
        $criteria->compare('author_id', $this->author_id);
        $criteria->compare('opinion', $this->opinion);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('host_days', $this->host_days);
        $criteria->compare('surf_days', $this->surf_days);
        $criteria->compare('travel_days', $this->travel_days);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('deleted', $this->deleted);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    protected function beforeValidate() {
        if ($this->user_id == Yii::app()->user->id) {
            $this->addError('user_id', '不能给自己评论');
            return false;
        }
        return parent::beforeValidate();
    }


    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->author_id = Yii::app()->user->id;
            $this->create_time = date('Y-m-d H:i:s');
            $this->deleted = 0;
        }
        return parent::beforeSave();
    }

}