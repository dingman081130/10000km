<?php

/**
 * This is the model class for table "conversation_comment".
 *
 * The followings are the available columns in table 'conversation_comment':
 * @property integer $id
 * @property integer $author_id
 * @property integer $conversation_id
 * @property string $content
 * @property string $create_time
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property User $author
 * @property Conversation $conversation
 */
class ConversationCommentAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ConversationCommentAR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'conversation_comment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('conversation_id, content', 'required'),
            array('conversation_id', 'numerical', 'integerOnly' => true),
            array('content', 'length', 'max' => 1024),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, author_id, conversation_id, content, create_time, deleted', 'safe', 'on' => 'search'),
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
            'conversation' => array(self::BELONGS_TO, 'Conversation', 'conversation_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'author_id' => 'Author',
            'conversation_id' => 'Conversation',
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
        $criteria->compare('conversation_id', $this->conversation_id);
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