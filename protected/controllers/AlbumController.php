<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.AlbumAR');
Yii::import('application.models.AR.PhotoAR');
Yii::import('application.models.Album.Album');
Yii::import('application.models.Album.Photo');
Yii::import('application.models.User.User');
Yii::import('application.models.Form.PhotoForm');

/**
 * Description of AlbumController
 *
 * @author yemin
 */
class AlbumController extends Controller {

    //put your code here
    
    protected $defaultPageInfo = array(
        
    );
    

    public function filters() {
        return array(
            'accessControl',
            'postOnly + upload, create, setCover',
        );
    }

    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('create', 'upload', 'setCover'),
                'users' => array('?'),
            ),
        );
    }

    public function actionView($id) {
        $album = Album::getAlbum($id);
        if ($album) {
            $this->pageTitle = '相册 '.$album['name'];
            $user = User::getUser($album['user_id']);
            $albums = Album::getUserAlbums($album['user_id']);
            $photos = Album::getAlbumPhotos($id);
            
            $this->render('view', array(
                'album' => $album,
                'albums' => $albums,
                'user' => $user,
                'photos' => $photos,
            ));
        } else {
            throw new CHttpException(404);
        }
    }

    public function actionUpload() {
        
        $album_id = isset($_POST['album_id']) ? intval($_POST['album_id']) : null;
        if ($album_id == null || !Album::albumBelongToUser($album_id, Yii::app()->user->id))
            throw new CHttpException(404);

        $ret = array('files' => array());
        foreach (CUploadedFile::getInstancesByName('files') as $file) {
            $form = new PhotoForm();
            $form->photo = $file;
            $filename = $form->savePhoto();
            if ($filename) {
                $photo = Photo::savePhoto($album_id, $filename, $form->photo->name);
                Album::hasCover($album_id) || Album::setCover($photo);
                $ret['files'][] = array(
                    'name' => $form->photo->name,
                    'size' => $form->photo->size,
                    'url' => ImageUrlHelper::imgUrl(ImageUrlHelper::PHOTO_ORIG, $filename),
                );
            }
        }
        //$this->returnJson($ret);
    }

    public function actionCreate() {
        $album = new AlbumAR();
        $album->attributes = $_POST;
        if ($album->save()) {
            $this->returnJson(array(
                'code' => 0,
                'album_id' => $album->id,
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => '创建相册失败',
            ));
        }
    }
    
    /**
     * 设置相册封面
     */
    public function actionSetCover(){
        $success = FALSE;$msg = 'ok';$data = array();
        if(!isset($_POST['pid'])){
            $msg = '缺少图片id参数';
            return $this->returnJson(array('success' => $success,'msg' => $msg,'data' => &$data));
        }
        $pid = intval($_POST['pid']);
        if(Yii::app()->user->id != Photo::getOwner($pid)){
            $msg = '您无权将此照片设置为封面，该照片不属于您';
            return $this->returnJson(array('success' => $success,'msg' => $msg,'data' => &$data));
        }
        if(!($photo = Album::setCover($pid))){
            $msg = '设置封面失败';
            return $this->returnJson(array('success' => $success,'msg' => $msg,'data' => &$data));
        }
        $success = TRUE;
        $data = array('photo' => &$photo);
        return $this->returnJson(array('success' => $success,'msg' => $msg,'data' => &$data));
    }

}

?>
