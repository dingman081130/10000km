<?php

/**
 * This is the model class for table "heehaw".
 *
 * The followings are the available columns in table 'heehaw':
 * @property integer $id
 * @property integer $user_id
 * @property string $msg
 * @property string $last_update
 */

class HeehawAR extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HeehawAR the static model class
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
		return 'heehaw';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, msg', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('msg', 'length', 'max'=>256),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, msg, last_update', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'msg' => 'Msg',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('msg',$this->msg,true);
		$criteria->compare('last_update',$this->last_update,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
    
        protected function beforeSave() {
            $this->last_update = date('Y-m-d H:i:s');
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