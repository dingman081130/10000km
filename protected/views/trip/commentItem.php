<?php
/**
 * @param  $comment
 * @param  $threaded_comments
 */

Yii::import('application.models.Emotion.Emotion');
Yii::import('application.models.User.User');

$comment_content = CHtml::encode($comment['content']);
$comment_content = Emotion::replaceEmotion($comment_content);
$user = User::getUser($comment['user_id'])

?>


<li class="comment-item" cid="<?php echo intval($comment['id']); ?>">
    <!-- comment data -->
    <div class="comment-data">
        <div class="avatar">
            <img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $user['avatar']); ?>">
        </div>
        <div class="comment-body">
            <header class="comment-header">
                <small>
                    <span><a class="username" uid="<?php echo intval($user['id']); ?>" href="/user/view/<?php echo intval($user['id']); ?>"><?php echo CHtml::encode($user['name']);?></a></span>
                    <span class="muted"><?php echo CHtml::encode($comment['create_time']); ?></span>
                </small>
                <ul class="unstyled pull-right toolbar">
                    <li><a href="javascript:;" class="collapse-btn">隐藏</a></li>
                    <?php if (Yii::app()->user->id) { ?>
                    <li><a href="javascript:;" class="reply-btn">回复</a></li>
                    <?php } ?>
                </ul>
            </header>

            <div class="comment-content">
                <p><?php echo $comment_content; ?></p>
            </div>
        </div>
    </div>

    <!-- children comments -->
    <ul class="unstyled child-list">
        <?php
        $children = $threaded_comments->getChildren($comment);
        foreach ($children as $child) {
            $this->renderPartial('commentItem', array(
                'comment' => $child,
                'threaded_comments' => $threaded_comments,
            ));
        }
        ?>
    </ul>
</li>
