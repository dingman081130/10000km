<?php
/**
 * @file class AlbumHeaderWidget
 * @package application.components.widgets
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-1-9
 * @version
 */


if(!isset($album['cover_surl']) || !$album['cover_surl']){
    $album['cover_surl'] = ImageUrlHelper::imgUrl(ImageUrlHelper::PHOTO_SMALL, 'no_photo.png');
}
$album['update_time'] = Helpers::friendlyTime($album['update_time']);
?>
<div class="album-header clearfix" aid="<?php echo CHtml::encode($album['id']);?>">
    <div class="album-cover" style="margin-right:10px;" pid="<?php echo CHtml::encode($album['cover']); ?>">
        <div>
            <img class="img-polaroid" src="<?php echo CHtml::encode($album['cover_surl']); ?>">
        </div>
    </div>
    <?php 
        $this->widget('BreadCrumbWidget', 
                array('crumbs' => array(
                    array(
                        'name'=> $user['name'],
                        'url' => array('/user/'.$user['id']),
                    ),
                    array(
                        'name' => '相册',
                        'url'  => array('/user/'.$user['id'].'/album'),
                    ),
                    array(
                        'name' => $album['name'],
                        'url' => array('/album/'.$album['id']),
                    ),
                )));
    ?>
    <?php if($user['id'] == Yii::app()->user->id){ ?>
        <div style="position: absolute; right: 0px; top: 2px;">
            <button class="btn btn-info upload-photo-btn" data-toggle="modal">上传照片</button>
        </div>
    <?php } ?>
</div>

