<?php
Yii::import('application.models.Album.PhotoComment');
Yii::import('application.models.Album.Photo');

if(!is_array($photo)){
    $photo = Photo::getPhoto($photo);
}
if(!$photo){
    return FALSE;
}
$permit = array('view');
$comments = PhotoComment::getComment($photo['id'], 0, Yii::app()->params['commentPageSize']);
$own_id = Photo::getOwner($photo['id']);
$owner = User::getUser($own_id);

Yii::app()->user->id && $permit[] = 'reply';  //如果是登陆用户则允许回复
isset($owner['id']) && $owner['id'] == Yii::app()->user->id && $permit[] = 'edit';  //如果是当前用户则允许编辑
?>

<div class="photo-story" aid="<?php echo $photo['album_id'] ?>" mode="<?php echo $mode; ?>" pid="<?php echo (isset($photo['id']) ? $photo['id'] : 0 ); ?>">

    <!--相片的操作按钮组件-->
    <div class="photo-area">
        <img class="photo-article" src="<?php echo CHtml::encode($photo['ourl']) ?>">
        <?php if($mode != 'fresh'){ ?>
            <div class="nav-btn prev"></div>
            <div class="nav-btn next"></div>
        <?php }?>
    </div>

    <div class="clearfix photo-desc" style="margin: 10px 0;">
        <?php if (in_array('edit', $permit) && $mode != 'fresh' ){ ?>
            <div class="edit_area pull-left edit_able" pid="<?php echo $photo['id']; ?>"><?php echo CHtml::encode($photo['title']); ?></div>
            <span class="pull-left pencil-icon" style="background: url('/img/pencil-icon.png') no-repeat; display: block; width: 16px; height: 16px;"></span>
        <?php } else { ?>
            <p><?php echo CHtml::encode($photo['title']); ?></p>
        <?php } ?>
    </div>

    <?php if (Yii::app()->user->id) {
        $avatar = ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, Yii::app()->user->attrs['avatar']);
        ?>
    <div class="photo-comment-reply clearfix">
        <a class="photo-comment-reply-avatar" href="/user/<?php echo Yii::app()->user->attrs['id']; ?>"><img src="<?php echo $avatar; ?>"></a>
        <div class="photo-comment-replay-data">
            <textarea class="photo-comment-reply-text" rows="4" placeholder="发表评论"></textarea>
            <div>
                <span class="photo-comment-reply-emotion pull-left"></span>
                <button class="photo-comment-reply-button btn btn-primary pull-right" style="margin-right: 20px" ruid="0">回复</button>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php
        $this->renderView(array('photo', 'commentList'), array('comments' => $comments));
    ?>
</div>