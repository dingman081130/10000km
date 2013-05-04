<?php

Yii::import('application.models.Group.Topic');

/**
 * This is the model class for table "topic".
 *
 * The followings are the available columns in table 'topic':
 * @property integer $id
 * @property integer $author_id
 * @property string $title
 * @property string $content
 * @property integer $group_id
 * @property string $create_time
 * @property string $edit_time
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property User $author
 * @property Group $group
 * @property TopicComment[] $topicComments
 */
class TopicAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return TopicAR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'topic';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, content', 'required'),
            array('group_id', 'numerical', 'integerOnly' => true, 'allowEmpty' => false, 'on' => 'insert'),
            array('title', 'length', 'max' => 45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, author_id, title, content, group_id, create_time, deleted', 'safe', 'on' => 'search'),
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
            'group' => array(self::BELONGS_TO, 'Group', 'group_id'),
            'topicComments' => array(self::HAS_MANY, 'TopicComment', 'topic_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'author_id' => 'Author',
            'title' => '标题',
            'content' => '内容',
            'group_id' => '所属组id',
            'create_time' => '创建时间',
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
        $criteria->compare('title', $this->title, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('group_id', $this->group_id);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('deleted', $this->deleted);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    
    protected function beforeSave() {
        if ($this->isNewRecord) {
            if (empty($this->author_id)) $this->author_id = Yii::app()->user->id;
            $this->create_time = date('Y-m-d H:i:s');
            $this->deleted = 0;
        } else {
            $this->edit_time = date('Y-m-d H:i:s');
        }
        
        $purifier = new CHtmlPurifier();
        $purifier->options = array('URI.AllowedSchemes' => array(
                'http' => true,
                'https' => true,
                ),
                'Filter.Custom' => array(new HTMLPurifierFilterVideo()),
            );
        $this->content = $purifier->purify($this->content);
        
        return parent::beforeSave();
    }
    
    
    protected function afterSave() {
        if (!$this->isNewRecord)
            Topic::delTopicCache ($this->id);
        return parent::afterSave();
    }


    public function getFirstError(){
        
        if (defined('YII_DEBUG')) {
            return CJSON::encode($this->getErrors());
        }
        $errors = $this->getErrors();
        $es = array_pop($errors);
        return array_pop($es);
    }

}