<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('ext.iwi.Iwi');

/**
 * Description of PhotoForm
 *
 * @author yemin
 */
class PhotoForm extends CFormModel {

    //put your code here
    
    const THUMB_WIDTH = 200;
    const THUMB_HEIGHT = 200;
    
    const MAX_WIDTH = 1200;
    const MAX_HEIGHT = 900;

    private $tmp_dir = '/tmp/10000km/photo/';
    public $photo;

    public function rules() {
        return array(
            array('photo', 'file', 'types' => 'jpeg, jpg, png, gif', 'maxSize' => 5 * 1024 * 1024, 'allowEmpty' => false),
        );
    }

    public function savePhoto() {
        if (!$this->validate())
            return null;

        if (!is_dir($this->tmp_dir))
            mkdir($this->tmp_dir, 0777, true);

        $ext = $this->photo->extensionName;

        $file_name = date('YmdHis') . mt_rand(10000, 99999);
        $save_path = "{$this->tmp_dir}{$file_name}.$ext";

        if ($this->photo->saveAs($save_path)) {
            $pic = new Iwi($save_path);
            
            $big_path = "{$this->tmp_dir}{$file_name}_big.$ext";
            if ($pic->width > self::MAX_WIDTH || $pic->height > self::MAX_HEIGHT) {
                $pic->resize(self::MAX_WIDTH, self::MAX_HEIGHT);
                $pic->save($big_path);
            } else {
                $big_path = $save_path;
            }
            
            $oss = new OSSClient();
            if (!$oss->upload(ImageUrlHelper::imgPath(ImageUrlHelper::PHOTO_ORIG, "$file_name.$ext"), $big_path)) {
                return null;
            }

            
            $pic->adaptive(self::THUMB_WIDTH, self::THUMB_WIDTH);
            $small_path = "{$this->tmp_dir}{$file_name}_small.$ext";
            $pic->save($small_path);
            if (!$oss->upload(ImageUrlHelper::imgPath(ImageUrlHelper::PHOTO_SMALL, "$file_name.$ext"), $small_path)) {
                return null;
            }
            
            return "$file_name.$ext";
        }
        
        return null;
    }

}

?>
