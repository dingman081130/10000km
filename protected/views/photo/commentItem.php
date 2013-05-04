<?php
    Yii::import('application.models.User.User');
    Yii::import('application.models.Emotion.Emotion');

    $cid    = $comment['id'];
    $pid    = $comment['photo_id'];
    $uid    = $comment['user_id'];
    $ctime  = $comment['create_time'];
    $content= $comment['content'];

    $user   = User::getUser($uid);
    $avatar = $user['avatar'];
    $uname  = $user['name'];
    $aurl  = ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $avatar);

?>
<div class="photo-comment-item" uid="<?php echo $uid; ?>">
    <a class="photo-comment-item-avatar" href="/user/<?php echo $uid;?>"><img src="<?php echo $aurl ?>"></a>
    <div class="photo-comment-item-data">
        <p class="content">
            <a class="username" href="/user/<?php echo $uid;?>" uid="<?php echo $uid;?>"><?php echo CHtml::encode($uname); ?></a>:
            <?php echo Emotion::replaceEmotion(CHtml::encode($content)); ?>
        </p>
        <div class="photo-comment-item-footer">
            <span class="time">
                <?php echo CHtml::encode(Helpers::friendlyTime($ctime)); ?>
            </span>
            <div class="btns">
                <?php if(Yii::app()->user->id){?>
                <a href="#" class="reply" uid="<?php echo $uid; ?>" uname="<?php echo CHtml::encode($uname); ?>">回复</a>
                <?php }?>
            </div>
        </div>
    </div>
    <div style="clear: both"></div>
</div>
