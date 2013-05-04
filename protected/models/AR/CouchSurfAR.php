<?php

/**
 * This is the model class for table "couch_surf".
 *
 * The followings are the available columns in table 'couch_surf':
 * @property integer $id
 * @property integer $host_id
 * @property integer $surf_id
 * @property integer $type
 * @property string $title
 * @property string $content
 * @property string $reason
 * @property integer $couch_number
 * @property integer $status
 * @property string $arrive_date
 * @property string $leave_date
 * @property string $create_time
 * @property string $deal_time
 *
 * The followings are the available model relations:
 * @property User $host
 * @property User $surf
 */
class CouchSurfAR extends CActiveRecord {
    
    const TYPE_HOST_INVITE_SURF = 1; // 沙发主主动邀请沙发客入住
    const TYPE_SURF_REQUEST_HOST = 2; // 沙发客向沙发主申请沙发
    
    const STATUS_PEDDING = 1; // 等待处理
    const STATUS_ACCEPED = 2; // 已接受
    const STATUS_REJECTED = 3; // 拒绝了
    const STATUS_CANCELED = 4; // 取消了

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CouchSurf the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'couch_surf';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title',   'required', 'message' => '请输入标题'),
            array('content', 'required', 'message' => '请输入内容'),
            array('couch_number', 'required', 'message' => '请输入沙发数量'),
            array('arrive_date', 'required', 'message' => '请输入到达时间'),
            array('leave_date', 'required', 'message' => '请输入离开时间'),
            array('title', 'length', 'max' => 128, 'tooLong' => '标题应该少于128个字符'),
            array('reason', 'length', 'max' => 256 ),
            array('reason', 'length', 'min' => 20, 'on'=>'update'),
            array('content', 'length', 'max' => 1024, 'tooLong' => '内容应少于1024个字符'),
            array('arrive_date, leave_date', 'dateValidate', 'on' => 'insert'),
            array('couch_number', 'in', 'range' => array(1,2,3,4,5,6)),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, host_id, surf_id, type, title, content, reason, couch_number, status, arrive_date, leave_date, create_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'host' => array(self::BELONGS_TO, 'User', 'host_id'),
            'surf' => array(self::BELONGS_TO, 'User', 'surf_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'host_id' => 'Host',
            'surf_id' => 'Surf',
            'type' => 'Type',
            'title' => 'Title',
            'content' => 'Content',
            'reason' => 'Reason',
            'couch_number' => 'Couch Number',
            'status' => 'Status',
            'arrive_date' => 'Arrive Date',
            'leave_date' => 'Leave Date',
            'create_time' => 'Create Time',
            'deal_time' => 'Deal Time',
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
        $criteria->compare('host_id', $this->host_id);
        $criteria->compare('surf_id', $this->surf_id);
        $criteria->compare('type', $this->type);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('couch_number', $this->couch_number);
        $criteria->compare('status', $this->status);
        $criteria->compare('arrive_date', $this->arrive_date, true);
        $criteria->compare('leave_date', $this->leave_date, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('deal_time', $this->create_time, true);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    
    public function dateValidate($attribute, $params) {
        
        $str = trim($this->$attribute);
        if (!$str) {
            if ($attribute == 'arrive_date')
                $this->addError($attribute, '请输入到达日期');
            else
                $this->addError($attribute, '请输入离开日期');
        }
        
        $t = strtotime($this->$attribute);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->$attribute) || $t == false) {
            if ($attribute == 'arrive_date')
                $this->addError($attribute, '到达日期不合法');
            else
                $this->addError($attribute, '离开日期格式不合法');
        }

        if ($attribute == 'arrive_date') {
            if ($t < strtotime(date('Y-m-d'))) {
                $this->addError($attribute, '到达日期不应早于当前时间');
            }
        } else if ($attribute == 'leave_date') {
            if ($t <= strtotime($this->arrive_date)) {
                $this->addError($attribute, '离开日期要大于到达日期');
            }
        }
    }
    
    
    
    protected function beforeValidate() {
        if ($this->host_id == $this->surf_id) {
            if ($this->type == self::TYPE_HOST_INVITE_SURF)
                $this->addError('surf_id', '不能邀请自己');
            else
                $this->addError ('host_id', '不能申请自己的沙发');
            return false;
        }
        
        return parent::beforeValidate();
    }


    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
            $this->status = self::STATUS_PEDDING;
            $this->reason = '';
        }
        
        if ($this->status != self::STATUS_PEDDING) {
            $this->deal_time = date('Y-m-d H:i:s');
        }
        return parent::beforeSave();
    }
    
    public function getEErrors() {
        $errors = $this->getErrors();
        $ret = array();
        foreach($errors as $attribute => $ones) {
            $ret[] = array(
                    'attribute' => $attribute,
                    'error'     => array_shift($ones)
                );
        }
        return $ret;
    }

}