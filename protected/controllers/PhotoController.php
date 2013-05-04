<?php
/**
 * @file class PhotoController
 * 
 * @package application.controllers
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */

Yii::import('application.models.Album.PhotoComment');
Yii::import('application.models.Album.Album');
Yii::import('application.models.Album.Photo');
Yii::import('application.models.User.User');

class PhotoController extends Controller {

    //put your code here

    public function filters() {
        return array(
            'accessControl',
            'postOnly + comment, delete',
        );
    }

    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('comment', 'delete', 'photoList'),
                'users' => array('?'),
            ),
        );
    }
    
    public function actionCommentHtml(){
        $pid    = $_GET['pid'];
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $comments = PhotoComment::getComment($pid, $offset, Yii::app()->params['commentPageSize']);
        
        $html = '';
        foreach($comments as $comment){
            $html .= $this->renderPartial('commentItem', array('comment' => $comment), TRUE);
        }
        echo $html;
    }
    
    /**
     * 提交评论信息
     */
    public function actionComment(){
        $success = true;$msg = 'ok';$data = array();

        $ruid = isset($_POST['ruid']) ? intval($_POST['ruid']) : 0;
        $comment = PhotoComment::addComment($_POST['pid'], Yii::app()->user->id, $_POST['comment'], $_POST['ruid']);
        if($comment === false){
            $success = false;$msg = '回复失败';
        }else{
            $data['code'] = $this->renderPartial('commentItem', array(
                'comment' => $comment,
            ), true);
        }
        $this->returnJson(array(
            'success' => $success,
            'msg' => $msg,
            'data' => $data
        ));
    }
    
    /**
     * 修改描述信息
     */
    public function actionSetTitle(){
        $pid = isset($_POST['pid']) ? $_POST['pid'] : null;
        $title = isset($_POST['value']) ? trim($_POST['value']) : '';
        if(Photo::setTitle(Yii::app()->user->id, $pid, $title)){
            $this->returnText($title);
        }
    }
    
    /**
     * 渲染照片模版
     * @return boolean
     */
    public function actionView($id){
        $album = Album::getAlbum($id);
        if(!$album){
            return $this->renderText('不能找到图片所在的相册');
        }
        $this->pageTitle = '浏览相册 '.$album['name'];
        $user = User::getUser($album['user_id']);
        $this->render('view', array('album' => &$album,'user' => &$user));
    }
    /**
     * 删除图片
     * @param int $pid 图片id
     * @return array 返回下一张图片信息
     */
    public function actionDelete(){
        $success = FALSE;$msg = 'ok';$data = array();
        if (!isset($_POST['pid'])) {
            $msg = '缺失照片参数';
            return $this->returnJson(array('success' => $success,'msg' => $msg,'data' => &$data));
        }
        $pid = intval($_POST['pid']);
        if(Yii::app()->user->id !== Photo::getOwner($pid)){
            $msg = '您没有权利删除此张照片';
            return $this->returnJson(array('success' => $success,'msg' => $msg,'data' => &$data));
        }
        $np = Photo::delPhoto($pid);
        //print_r($np);exit;
        $comments = array();    //// 评论信息
        $album = array();       //// 相册信息
        if(isset($np['id'])){
            $comments = PhotoComment::getComment($np['id'], 0, Yii::app()->params['commentPageSize']);
            $album    = Album::getAlbum($np['album_id']);
        }
        $html = $this->renderPartial('commentList', array('comments' => $comments), TRUE);

        $success = TRUE;
        $data = array('photo' => $np, 'commenthtml' => $html, 'album' => $album);
        return $this->returnJson(array('success' => $success,'msg' => $msg,'data' => $data));
    }
    
    /**
     * 获取图片的详情和评论
     * @param int $id 图片id
     * @return array
     */
    public function actionGetPhoto($id){
        $success = TRUE;$msg = 'ok';$data = array();
        $p = Photo::getPhoto($id);
        if(!$p){
            $success = FALSE;
            $msg = '找不到图片信息';
        }
        $comments = PhotoComment::getComment($p['id'], 0, Yii::app()->params['commentPageSize']);
        $html = $this->renderPartial('commentList', array('comments' => $comments), TRUE);
        return $this->returnJson(array(
            'success' => $success,
            'msg' => $msg,
            'data' => array(
                'photo' => $p,
                'commenthtml' => $html
            )
        ));
    }
    
    /**
     * 获取同一相册的后一张图片和评论
     * @param int $pid 图片id
     * @return array
     */
    public function actionNext($id){
        $success = TRUE;$msg = 'ok';$data = array();
        $np = Photo::getNextPhoto($id, TRUE);
        if(!$np){
            $success = FALSE;$msg = '没有下一张图片了';
            return $this->returnJson(array('success' => $success,'msg' => $msg,'data' => array()));
        }
        $comments = PhotoComment::getComment($np['id'], 0, Yii::app()->params['commentPageSize']);
        $html = $this->renderPartial('commentList', array('comments' => $comments), TRUE);
        return $this->returnJson(array('success' => $success,'msg' => $msg,'data' => array('photo' => $np,'commenthtml' => $html)));
    }
    
    /**
     * 获取同一相册的前一张图片和评论
     * @param int $pid 图片id
     * @return array
     */
    public function actionPrev($id){
        $success = TRUE;$msg = 'ok';
        $pp = Photo::getPrevPhoto($id, TRUE);
        if(!$pp){
            $success = FALSE;
            $msg = '已经是最后一张图片了';
        }
        $comments = PhotoComment::getComment($pp['id'], 0, Yii::app()->params['commentPageSize']);
        $html = $this->renderPartial('commentList', array('comments' => $comments), TRUE);
        return $this->returnJson(array(
            'success' => $success,
            'msg' => $msg,
            'data' => array(
                'photo'=>$pp,
                'commenthtml' => $html
            )
        ));
    }
    
    /**
     * 获取一个photostory的html代码
     * @param int $id
     * @return html
     */
    public function actionPhotoStoryHtml($id){
        $pid = intval($id);
        $mode = 'view';
        isset($_GET['mode']) &&  $mode = $_GET['mode'];
        $photo = Photo::getPhoto($id);
        $this->renderPartial('photoStory', array('photo' => $photo, 'mode' => $mode));
    }
    
    
    public function actionPhotoList() {
        $albums = Album::getUserAlbums(Yii::app()->user->id);
        $data = array();
        
        foreach ($albums as $album) {
            $photos = Album::getAlbumPhotos($album['id']);
            foreach ($photos as $photo) {
                $data[] = array(
                    'thumb' => ImageUrlHelper::imgUrl(ImageUrlHelper::PHOTO_SMALL, $photo['img']),
                    'image' => ImageUrlHelper::imgUrl(ImageUrlHelper::PHOTO_ORIG, $photo['img']),
                    'title' => $photo['title'],
                    'folder' => $album['name'],
                );
            }
        }
        $this->returnJson($data);
    }
}