<?php

/**
 * This is the model class for table "trip_dst".
 *
 * The followings are the available columns in table 'trip_dst':
 * @property integer $id
 * @property integer $trip_id
 * @property string $dst_name
 *
 * The followings are the available model relations:
 * @property Trip $trip
 */
class TripDstAR extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return TripDst the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'trip_dst';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('trip_id, dst_name', 'required'),
            array('trip_id', 'numerical', 'integerOnly' => true),
            array('dst_name', 'length', 'max' => 45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, trip_id, dst_name', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'trip' => array(self::BELONGS_TO, 'TripAR', 'trip_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'trip_id' => 'Trip',
            'dst_name' => 'Dst Name',
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
        $criteria->compare('trip_id', $this->trip_id);
        $criteria->compare('dst_name', $this->dst_name, true);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    protected function beforeValidate() {
        if ($this->exists('trip_id = :trip_id and dst_name = :dst_name', array(
            ':trip_id' => $this->trip_id,
            ':dst_name' => $this->dst_name,
        ))) {
            return false;
        }
        
        return parent::beforeValidate();
    }

}
