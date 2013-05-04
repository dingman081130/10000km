<?php

/**
 * This is the model class for table "emotion".
 *
 * The followings are the available columns in table 'emotion':
 * @property integer $id
 * @property string $category
 * @property string $phrase
 * @property string $value
 * @property string $filename
 * @property integer $hot
 * @property integer $deleted
 */
class EmotionAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return EmotionAR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'emotion';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('phrase, filename', 'required'),
            array('hot, deleted', 'numerical', 'integerOnly' => true),
            array('category, phrase, value, filename', 'length', 'max' => 45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, category, phrase, value, filename, hot, deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'category' => 'Category',
            'phrase' => 'Phrase',
            'value' => 'Value',
            'filename' => 'Filename',
            'hot' => 'Hot',
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
        $criteria->compare('category', $this->category, true);
        $criteria->compare('phrase', $this->phrase, true);
        $criteria->compare('value', $this->value, true);
        $criteria->compare('filename', $this->filename, true);
        $criteria->compare('hot', $this->hot);
        $criteria->compare('deleted', $this->deleted);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

}