<?php

Yii::import('application.models.AR.AlbumAR');
Yii::import('application.models.Album.Album');

/**
 * This is the model class for table "photo".
 *
 * The followings are the available columns in table 'photo':
 * @property integer $id
 * @property string $title
 * @property integer $album_id
 * @property string $create_time
 * @property string $img
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property Album $album
 */
class PhotoAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PhotoAR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'photo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('album_id, img', 'required'),
            array('album_id', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 64),
            array('img', 'length', 'max' => 45),
            array('album_id', 'albumIdValidate'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, title, album_id, create_time, img, deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'album' => array(self::BELONGS_TO, 'Album', 'album_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'title' => 'Title',
            'album_id' => 'Album',
            'create_time' => 'Create Time',
            'img' => 'Img',
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
        $criteria->compare('title', $this->title, true);
        $criteria->compare('album_id', $this->album_id);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('img', $this->img, true);
        $criteria->compare('deleted', $this->deleted);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
            $this->deleted = 0;
        }
        return parent::beforeSave();
    }
    

    public function albumIdValidate($attribute, $params) {
        if (!AlbumAR::model()->exists('id = :id and user_id = :user_id', array(
                    ':id' => $this->album_id,
                    ':user_id' => Yii::app()->user->id,
                )))
            $this->addError($attribute, '不存在该相册');
    }

}