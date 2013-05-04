<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.Form.TripImageForm');

/**
 * Description of TripImage
 *
 * @author yemin
 */
class TripImage {
    //put your code here
    
    private $tmp_dir = '/tmp/10000km/tripimg/';

    /**
     * 
     * 保存图片
     * 
     * @param CUploadedFile $uploaded_file
     * @return string  uploaded file name, or null if failed.
     */
    public function saveImage($uploaded_file) {
        $model = new TripImageForm();
        $model->image = $uploaded_file;
        if ($model->validate()) {
            if (!is_dir($this->tmp_dir))
                mkdir($this->tmp_dir, 0777, true);

            $ext = $uploaded_file->extensionName;

            $file_name = date('YmdHis') . mt_rand(10000, 99999);
            $save_path = "{$this->tmp_dir}{$file_name}.$ext";



            if ($uploaded_file->saveAs($save_path)) {

                $oss = new OSSClient();
                if ($oss->upload(ImageUrlHelper::imgPath(ImageUrlHelper::TRIP_IMAGE, "$file_name.$ext"), $save_path)) {
                    return "$file_name.$ext";
                }
            }
        }
        
        return null;
        
    }

}

?>
