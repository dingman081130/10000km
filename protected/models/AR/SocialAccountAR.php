<?php

/**
 * This is the model class for table "social_account".
 *
 * The followings are the available columns in table 'social_account':
 * @property integer $id
 * @property integer $type
 * @property string $open_id
 * @property integer $user_id
 *
 * The followings are the available model relations:
 * @property User $user
 */
class SocialAccountAR extends CActiveRecord {

    const TYPE_RENREN = 1;
    const TYPE_WEIBO = 2;
    const TYPE_QQ = 3;
    const TYPE_DOUBAN = 4;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return SocialAccount the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'social_account';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type, open_id, user_id', 'required'),
            array('type, user_id', 'numerical', 'integerOnly' => true),
            array('open_id', 'length', 'max' => 45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, type, open_id, user_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'type' => 'Type',
            'open_id' => 'Open',
            'user_id' => 'User',
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
        $criteria->compare('type', $this->type);
        $criteria->compare('open_id', $this->open_id, true);
        $criteria->compare('user_id', $this->user_id);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    
    protected function beforeValidate() {
        if ($this->isNewRecord) {
            if ($this->model()->exists('type = :type and open_id = :open_id', array(
                'type' => $this->type,
                'open_id' => $this->open_id,
            ))) {
                $this->addError('email', '已经绑定了该账号');
                return false;
            }
        }
        return parent::beforeValidate();
    }

}
