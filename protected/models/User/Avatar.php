<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('ext.iwi.Iwi');
Yii::import('application.models.AR.UserAR');
Yii::import('application.models.User.User');
Yii::import('application.vendors.*');

/**
 * Description of Avatar
 *
 * @author yemin
 */
class Avatar {
    //put your code here

    const TMP_DIR = '/tmp/10000km/avatar/';

    private static $size = array(
        ImageUrlHelper::AVATAR_TINY => 40,
        ImageUrlHelper::AVATAR_SMALL => 60,
        ImageUrlHelper::AVATAR_MIDDLE => 90,
        ImageUrlHelper::AVATAR_LARGE => 180);

    public static function updateAvatarFromFile($filename) {
        if (!is_dir(self::TMP_DIR))
            mkdir(self::TMP_DIR, 0777, true);

        $oss = new OSSClient();
        $new_image = date('YmdHis') . mt_rand(10000, 99999) . '.jpg';

        try {
            foreach (self::$size as $t => $size) {
                $img = new Iwi($filename);
                //$img->adaptive
                $img->adaptive($size, $size);
                $img->save(self::TMP_DIR . $new_image);
                $oss->upload(ImageUrlHelper::imgPath($t, $new_image), self::TMP_DIR . $new_image);
            }
        } catch (Exception $e) {
            MuleApp::log($e->getMessage());
            return false;
        }

        UserAR::model()->updateByPk(Yii::app()->user->id, array(
            'avatar' => $new_image,
        ));

        User::updateMyCacheProfile();

        return $new_image;
    }

    public static function updateAvatarFromUrl($url) {
        require_once 'httpful.phar';

        if (!is_dir(self::TMP_DIR))
            mkdir(self::TMP_DIR, 0777, true);

        $tmp_file = self::TMP_DIR . mt_rand();

        $response = \Httpful\Request::get($url)->send();
        $data = $response->body;
        
        file_put_contents($tmp_file, $data);
        $ret = self::updateAvatarFromFile($tmp_file);
        unlink($tmp_file);
        return $ret;
    }

    /**
     * 从上传的文件更新头像
     * 
     * @param integer $uid
     * @param UploadedFile $uploadedfile
     * @return boolean
     */
    public static function updateAvatarFromUploadedfile($uploadedfile) {
        if ($uploadedfile) {
            if (!is_dir(self::TMP_DIR))
                mkdir(self::TMP_DIR, 0777, true);

            $tmp_file = self::TMP_DIR . mt_rand();
            if ($uploadedfile->saveAs($tmp_file)) {
                return self::updateAvatarFromFile($tmp_file);
            }
        }
        return null;
    }

}

?>
