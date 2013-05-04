<?php
Yii::import('application.models.User.User');
Yii::import('application.models.Emotion.Emotion');
Yii::import('application.models.Group.TopicComment');
$user = User::getUser($comment['author_id']);
?>

<div class="comment-item clearfix" style="margin:10px 0;" cid="<?php echo CHtml::encode($comment['id']); ?>" uname="<?php echo CHtml::encode($user['name']); ?>">
    <div class="comment-avatar thumbnail" style="float:left;width: 40px;height: 40px">
        <img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_TINY, $user['avatar']) ?>">
    </div>
    <div class="comment-data" style="margin-left:60px;">
        <div class="comment-header">
            <small>
                <a class="username" uid="<?php echo $user['id']; ?>" href="/user/<?php echo $user['id']; ?>"><?php echo CHtml::encode($user['name']);?></a>
                <?php echo CHtml::encode(Helpers::friendlyTime($comment['create_time']));?>
                <?php if(Yii::app()->user->id){ ?>
                <a class="comment-reply pull-right" href="#">回复</a>
                <?php } ?>
            </small>
        </div>
        <?php 
            echo Emotion::replaceEmotion(CHtml::encode($comment['content'])); 
            $cid  = $comment['id']; // 防止无限循环
            $upid = $comment['upid'];
            //var_dump($upid);exit;
            while($upid && $upid != $cid){
                $cc = TopicComment::getComment($upid);
                if(!$cc){
                    break;
                }
                $user = User::getUser($cc['author_id']);
                if($user){
                    echo ' //<a href="/user/'.$user['id'].'">@'.CHtml::encode($user['name']).'</a> ';
                }
                echo Emotion::replaceEmotion(CHtml::encode($cc['content']));
                $upid = $cc['upid'];
            }
        ?>
    </div>
</div>