<?php

/**
 * This is the model class for table "city".
 *
 * The followings are the available columns in table 'city':
 * @property integer $id
 * @property string $name
 * @property double $longitude
 * @property double $latitude
 * @property integer $map_zoom
 * @property integer $upid
 * @property string $pinyin
 *
 * The followings are the available model relations:
 * @property Trip[] $trips
 */
class CityAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return City the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'city';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, upid', 'required'),
            array('map_zoom, upid', 'numerical', 'integerOnly' => true),
            array('longitude, latitude', 'numerical'),
            array('name, pinyin', 'length', 'max' => 45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, longitude, latitude, map_zoom, upid', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'trips' => array(self::HAS_MANY, 'Trip', 'from_city'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'map_zoom' => 'Map Zoom',
            'upid' => 'Upid',
            'pinyin' => 'PingYin',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('longitude', $this->longitude);
        $criteria->compare('latitude', $this->latitude);
        $criteria->compare('map_zoom', $this->map_zoom);
        $criteria->compare('upid', $this->upid);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

}