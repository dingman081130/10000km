<?php
$opinion = $comment['opinion'];
?>
<li class="clearfix" style=" border-bottom: 1px solid #CCC; padding-bottom: 10px; margin-top: 10px;">
    <div style="width: 70px; height: 70px; float: left; ">
        <a href="/user/<?php echo $author['id']; ?>" class="thumbnail"><img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $author['avatar']); ?>"></a>
    </div>
    <div style="float: left; margin-left: 15px;">
        <p style="margin: 0;"><a href="/user/<?php echo $author['id']; ?>"><?php echo CHtml::encode($author['name']); ?></a> - <span class="muted"><?php echo Helpers::friendlyTime($comment['create_time']); ?></span>
            <span style="color:
                <?php 
                if ($opinion == UserCommentAR::OPINION_POSITIVE) echo 'green';
                if ($opinion == UserCommentAR::OPINION_NEUTRAL) echo '#3366FF';
                if ($opinion == UserCommentAR::OPINION_NEGATIVE) echo 'red';
                ?>
                  ">
                <strong>
                    <?php
                    if ($opinion == UserCommentAR::OPINION_POSITIVE) echo '好评';
                    if ($opinion == UserCommentAR::OPINION_NEUTRAL) echo '中评';
                    if ($opinion == UserCommentAR::OPINION_NEGATIVE) echo '差评';
                    ?>
                </strong>
            </span>
        </p>
        <p class="muted">我们的经历：
            <?php
            $str = '';
            if ($comment['travel_days'] > 0) {
                $str .= "我们一起旅行{$comment['travel_days']}天";
            }
            if ($comment['host_days'] > 0) {
                if ($str != '') $str .= ',';
                $str .= "我在他那里借宿{$comment['host_days']}天";
            }
            if ($comment['surf_days'] > 0) {
                if ($str != '') $str .= ',';
                $str .= "他在我这里借宿{$comment['surf_days']}天";
            }
            echo $str;
            ?></p>
        <p><?php echo CHtml::encode($comment['content']); ?></p>
    </div>
</li>