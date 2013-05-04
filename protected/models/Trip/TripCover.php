<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.Form.*');
Yii::import('ext.iwi.Iwi');

/**
 * Description of TripCover
 *
 * @author yemin
 */
class TripCover {
    //put your code here
    
    private $tmp_dir = '/tmp/10000km/cover/';
    
    /**
     * 
     * @param CUploadedFile $uploaded_file
     */
    public function saveCover($uploaded_file) {
        $model = new TripCoverForm();
        $model->cover = $uploaded_file;
        if ($model->validate()) {
            if (!is_dir($this->tmp_dir)) mkdir($this->tmp_dir, 0777, true);
            
            $ext = $uploaded_file->extensionName;
            
            $file_name = date('YmdHis') . mt_rand(10000, 99999);
            $save_path = "{$this->tmp_dir}{$file_name}.$ext";
            
            
            
            if ($uploaded_file->saveAs($save_path)) {
                
                $oss = new OSSClient();
                if (!$oss->upload(ImageUrlHelper::imgPath(ImageUrlHelper::TRIP_COVER_ORIG, "$file_name.$ext"), $save_path)) {
                    return null;
                }
                
                $pic = new Iwi($save_path);
                
                $pic->adaptive(220, 165);
                $middle_path = "{$this->tmp_dir}{$file_name}_middle.$ext";
                $pic->save($middle_path);
                if (!$oss->upload(ImageUrlHelper::imgPath(ImageUrlHelper::TRIP_COVER_MIDDLE, "$file_name.$ext"), $middle_path)) {
                    return null;
                }
                
                $pic->adaptive(120, 90);
                $small_path = "{$this->tmp_dir}{$file_name}_small.$ext";
                $pic->save($small_path);
                if (!$oss->upload(ImageUrlHelper::imgPath(ImageUrlHelper::TRIP_COVER_SMALL, "$file_name.$ext"), $small_path)) {
                    return null;
                }
                
                return "$file_name.$ext";
            }
        }
    }
}

?>
