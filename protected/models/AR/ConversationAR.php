<?php

/**
 * This is the model class for table "conversation".
 *
 * The followings are the available columns in table 'conversation':
 * @property integer $id
 * @property integer $author_id
 * @property integer $city_id
 * @property string $title
 * @property string $content
 * @property string $create_time
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property User $author
 * @property Place $place
 * @property ConversationComment[] $conversationComments
 */
class ConversationAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ConversationAR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'conversation';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id, title, content', 'required'),
            array('city_id', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 45),
            array('content', 'length', 'max' => 1024),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, author_id, city_id, title, content, create_time, deleted', 'safe', 'on' => 'search'),
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
            'city' => array(self::BELONGS_TO, 'City', 'city_id'),
            'conversationComments' => array(self::HAS_MANY, 'ConversationComment', 'conversation_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'author_id' => 'Author',
            'city_id' => 'city',
            'title' => 'Title',
            'content' => 'Content',
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
        $criteria->compare('author_id', $this->author_id);
        $criteria->compare('city_id', $this->city_id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('deleted', $this->deleted);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
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