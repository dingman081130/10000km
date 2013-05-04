<?php

/**
 * This is the model class for table "trip".
 *
 * The followings are the available columns in table 'trip':
 * @property integer $id
 * @property string $title
 * @property string $cover
 * @property string $start_date
 * @property string $end_date
 * @property integer $creator_id
 * @property string $create_time
 * @property integer $from_city
 * @property integer $difficulty_level
 * @property integer $culture_level
 * @property integer $remote_level
 * @property integer $risk_level
 * @property string $content
 *
 * The followings are the available model relations:
 * @property User $creator
 * @property City $fromCity
 * @property TripDst[] $tripDsts
 * @property TripWay[] $tripWays
 */
class TripAR extends CActiveRecord {
    
    public $dsts;
    public $trip_way;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Trip the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'trip';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, cover, content, dsts, trip_way', 'required'),
            array('title', 'length', 'max' => 256),
            array('cover', 'length', 'max' => 128),
            
            array('from_city', 'fromCityValidator'),
            array('difficulty_level, culture_level, remote_level, risk_level', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 5),
            array('start_date, end_date', 'dateValidator'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, title, cover, start_date, end_date, creator_id, create_time, from_city, difficulty_level, culture_level, remote_level, risk_level, content', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'creator' => array(self::BELONGS_TO, 'UserAR', 'creator_id'),
            'fromCity' => array(self::BELONGS_TO, 'CityAR', 'from_city'),
            'tripDsts' => array(self::HAS_MANY, 'TripDstAR', 'trip_id'),
            'tripWays' => array(self::HAS_MANY, 'TripWayAR', 'trip_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'title' => '标题',
            'cover' => '封面',
            'start_date' => '开始时间',
            'end_date' => '结束时间',
            'creator_id' => 'Creator',
            'create_time' => 'Create Time',
            'from_city' => '出发城市',
            'difficulty_level' => 'Difficulty Level',
            'culture_level' => 'Culture Level',
            'remote_level' => 'Remote Level',
            'risk_level' => 'Risk Level',
            'content' => '内容',
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
        $criteria->compare('cover', $this->cover, true);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('creator_id', $this->creator_id);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('from_city', $this->from_city);
        $criteria->compare('difficulty_level', $this->difficulty_level);
        $criteria->compare('culture_level', $this->culture_level);
        $criteria->compare('remote_level', $this->remote_level);
        $criteria->compare('risk_level', $this->risk_level);
        $criteria->compare('content', $this->content, true);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

    public function dateValidator($attribute, $params) {
        if (empty($this->$attribute)) {
            $this->$attribute == null;
            return true;
        }
        
        $t = strtotime($this->$attribute);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->$attribute) || $t == false) {
            $this->addError($attribute, '日期不合法');
        }

        if ($attribute == 'start_date') {
            if ($t < strtotime(date('Y-m-d'))) {
                $this->addError($attribute, '出发日期不合法');
            }
        } else if ($attribute == 'end_date') {
            if ($t < strtotime($this->start_date)) {
                $this->addError($attribute, '结束日期不能比出发日期更早');
            }
        }
    }
    
    public function fromCityValidator($attribute, $params) {
        if ($this->from_city && !CityAR::model()->exists('id = :id', array('id' => $this->from_city)))
                $this->addError ($attribute, '不存在该城市');
    }

    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->creator_id || $this->creator_id = Yii::app()->user->id;
            $this->create_time = date('Y-m-d H:i:s');
        }
        
        $purifier = new CHtmlPurifier();
        $purifier->options = array('URI.AllowedSchemes' => array(
                'http' => true,
                'https' => true,
                ));
        $this->content = $purifier->purify($this->content);
        
        return parent::beforeSave();
    }
    
    protected function afterSave() {
        
        $dsts = explode(',', $this->dsts);
        foreach ($dsts as $dst) {
            $dst_obj = new TripDstAR();
            $dst_obj->trip_id = $this->id;
            $dst_obj->dst_name = $dst;
            $dst_obj->save();
        }
        
        foreach ($this->trip_way as $way) {
            $trip_way = new TripWayAR;
            $trip_way->trip_id = $this->id;
            $trip_way->way_id = $way;
            $trip_way->save();
        }
        
        return parent::afterSave();
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
