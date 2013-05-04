<?php

/**
 * This is the model class for table "photo_comment".
 *
 * The followings are the available columns in table 'photo_comment':
 * @property integer $id
 * @property integer $photo_id
 * @property integer $user_id
 * @property string $create_time
 * @property string $content
 * @property string $deleted
 *
 * The followings are the available model relations:
 * @property Photo $photo
 * @property User $user
 */
class PhotoCommentAR extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PhotoCommentAR the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'photo_comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('photo_id, user_id, content', 'required'),
			array('photo_id, user_id', 'numerical', 'integerOnly'=>true),
			array('content', 'length', 'max'=>256),
			array('deleted', 'length', 'max'=>45),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, photo_id, user_id, create_time, content, deleted', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'photo' => array(self::BELONGS_TO, 'Photo', 'photo_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'photo_id' => 'Photo',
			'user_id' => 'User',
			'create_time' => 'Create Time',
			'content' => 'Content',
			'deleted' => 'Deleted',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('photo_id',$this->photo_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('deleted',$this->deleted,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
    protected function beforeSave() {
        if(!$this->create_time){
            $this->create_time = date('Y-m-d H:i:s');
        }
        if($this->deleted === NULL){
            $this->deleted = 0;
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
            MuleApp::log('写入'.__CLASS__.'失败:'.Utils::arrToString($this->getErrors()), 'error');
            return false;
        }
        MuleApp::log('写入'.__CLASS__.'成功:'.Utils::arrToString($this->getAttributes()));
        return true;
    }
}