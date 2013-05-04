<?php

/**
 * This is the model class for table "private_letter".
 *
 * The followings are the available columns in table 'private_letter':
 * @property integer $id
 * @property integer $sender
 * @property integer $recipient
 * @property string $send_time
 * @property string $content
 * @property integer $is_read
 * @property string $last_update
 */
class PrivateLetterAR extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PrivateLetterAR the static model class
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
		return 'private_letter';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sender, recipient, send_time, content', 'required'),
			array('sender, recipient, is_read', 'numerical', 'integerOnly'=>true),
			array('content', 'length', 'max'=>512),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sender, recipient, send_time, content, is_read, last_update', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sender' => 'Sender',
			'recipient' => 'Recipient',
			'send_time' => 'Send Time',
			'content' => 'Content',
			'is_read' => 'Is Read',
			'last_update' => 'Last Update',
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
		$criteria->compare('sender',$this->sender);
		$criteria->compare('recipient',$this->recipient);
		$criteria->compare('send_time',$this->send_time,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('is_read',$this->is_read);
		$criteria->compare('last_update',$this->last_update,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        protected function beforeSave() {
            $this->last_update = date('Y-m-d H:i:s');
            return parent::beforeSave();
        }
}