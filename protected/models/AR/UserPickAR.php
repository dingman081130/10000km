<?php

/**
 * This is the model class for table "user_pick".
 *
 * The followings are the available columns in table 'user_pick':
 * @property integer $id
 * @property string $dst
 * @property string $start
 * @property string $end
 * @property string $desc
 * @property string $last_update
 */
class UserPickAR extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserPickAR the static model class
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
		return 'user_pick';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
                    array('id, dst', 'required'),
                    array('id', 'numerical', 'integerOnly'=>true),
                    array('dst', 'length', 'max'=>45),
                    array('desc', 'length', 'max'=>256),
                    array('start, end', 'safe'),
                    // The following rule is used by search().
                    // Please remove those attributes that should not be searched.
                    array('id, dst, start, end, desc, last_update', 'safe', 'on'=>'search'),
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
			'dst' => 'Dst',
			'start' => 'Start',
			'end' => 'End',
			'desc' => 'Desc',
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
		$criteria->compare('dst',$this->dst,true);
		$criteria->compare('start',$this->start,true);
		$criteria->compare('end',$this->end,true);
		$criteria->compare('desc',$this->desc,true);
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
            MuleApp::log('写入UserPick表失败'.Utils::arrToString($this->getErrors()), 'error');
            return false;
        }
        MuleApp::log('写入UserPick成功'.Utils::arrToString($this->getAttributes()));
        return true;
    }
}