<?php

Yii::import('ext.iwi.Iwi');

/**
 * This is the model class for table "group".
 *
 * The followings are the available columns in table 'group':
 * @property integer $id
 * @property string $name
 * @property integer $creator_id
 * @property string $create_time
 * @property integer $category_id
 * @property integer $city_id
 * @property string $image
 * @property string $description
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property GroupCategory $category
 * @property City $city
 * @property User $creator
 * @property Topic[] $topics
 */
class GroupAR extends CActiveRecord {
    
    public $uploaded_image;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return GroupAR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, category_id', 'required'),
            array('category_id, city_id', 'numerical', 'integerOnly' => true),
            array('name, image', 'length', 'max' => 45),
            array('description', 'safe'),
            array('uploaded_image', 'file', 'maxSize' => 2097152, 'types' => 'jpg, jpeg, gif, png', 'tooLarge' => '上传图片不能大于2M'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, creator_id, create_time, category_id, city_id, image, description, deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'category' => array(self::BELONGS_TO, 'GroupCategory', 'category_id'),
            'city' => array(self::BELONGS_TO, 'City', 'city_id'),
            'creator' => array(self::BELONGS_TO, 'User', 'creator_id'),
            'topics' => array(self::HAS_MANY, 'Topic', 'group_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => '小组名称',
            'creator_id' => '创建者',
            'create_time' => '创建时间',
            'category_id' => '小组类别',
            'city_id' => '所属城市',
            'image' => '小组图片',
            'description' => '小组描述',
            'deleted' => 'Deleted',
            'uploaded_image' => '小组图片',
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
        $criteria->compare('creator_id', $this->creator_id);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('category_id', $this->category_id);
        $criteria->compare('city_id', $this->city_id);
        $criteria->compare('image', $this->image, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('deleted', $this->deleted);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }
    
    protected function beforeSave() {
        if ($this->isNewRecord) {
            if (empty($this->creator_id)) $this->creator_id = Yii::app()->user->id;
            $this->create_time = date('Y-m-d H:i:s');
            $this->deleted = 0;
            
            if (!empty($this->uploaded_image)) $this->image = $this->saveImage();
        }
        return parent::beforeSave();
    }
    
    
    private function saveImage() {
        if (!$this->validate()) return null;
        
        if (!$this->uploaded_image) return null;
        $tmp_dir = '/tmp/10000km/group/';
        if (!is_dir($tmp_dir))
            mkdir($tmp_dir, 0777, true);
        
        $ext = $this->uploaded_image->extensionName;

        $file_name = date('YmdHis') . mt_rand(10000, 99999);
        $save_path = "$tmp_dir{$file_name}.$ext";
        $this->uploaded_image->saveAs($save_path);
        
        $pic = new Iwi($save_path);
        $pic->adaptive(60, 60);
        $pic->save("$tmp_dir{$file_name}_resize.$ext");
        
        $oss = new OSSClient();
        
        if (!$oss->upload(ImageUrlHelper::imgPath(ImageUrlHelper::GROUP_IMAGE, "$file_name.$ext"), "$tmp_dir{$file_name}_resize.$ext")) {
            return null;
        }
        
        return "$file_name.$ext";
    }
}