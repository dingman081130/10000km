<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TopicImageForm
 *
 * @author yemin
 */
class TopicImageForm extends CFormModel{
    //put your code here
    
    public $image;
    
    private $tmp_dir = '/tmp/10000km/topic/';
    
    public function rules() {
        return array(
            array('image', 'file', 'types' => 'jpeg, jpg, png, gif', 'maxSize' => 2*1024*1024, 'allowEmpty' => false),
        );
    }
    
    
    public function saveImage() {
        if (!$this->validate()) return null;
        
        if (!is_dir($this->tmp_dir))
            mkdir($this->tmp_dir, 0777, true);
        
        $ext = $this->image->extensionName;

        $file_name = date('YmdHis') . mt_rand(10000, 99999);
        $save_path = "{$this->tmp_dir}{$file_name}.$ext";
        
        if ($this->image->saveAs($save_path)) {
            $oss = new OSSClient();
            if (!$oss->upload(ImageUrlHelper::imgPath(ImageUrlHelper::TOPIC_IMAGE, "$file_name.$ext"), $save_path)) {
                return null;
            }
            return "$file_name.$ext";
        }
        return null;
    }
}

?>
