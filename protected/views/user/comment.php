<?php
$this->widget('UserPageHeaderWidget', array(
    'user' => $user,
)); 

$uid = intval($user['id']);
?>

<div class="row" style="margin-top: 10px;">
    <div class="span12" style="position: relative;">
        <div style="position: absolute; top: -70px;" id="tab"></div>
        <?php $this->renderPartial('userTabs', array('uid' => $uid, 'active' => 'comment')); ?>

        <div class="user-comment-box" uid="<?php echo $uid; ?>">
            <div class="clearfix">
                <div class="user-comment-filter">
                    <p style="margin: 0">
                        <label class="inline radio">评价：</label>
                        <label class="inline radio"><input type="radio" name="type" value="0" checked>全部(<?php echo $comment_count['positive_count'] + $comment_count['neutral_count'] + $comment_count['negative_count'] ?>)</label>
                        <label class="inline radio"><input type="radio" name="type" value="1">好评(<?php echo $comment_count['positive_count']; ?>)</label>
                        <label class="inline radio"><input type="radio" name="type" value="2">中评(<?php echo $comment_count['neutral_count']; ?>)</label>
                        <label class="inline radio"><input type="radio" name="type" value="3">差评(<?php echo $comment_count['negative_count']; ?>)</label>
                    </p>

                    <p>
                        <label class="inline radio">来自：</label>
                        <label class="inline radio"><input type="radio" name="from" value="" checked>全部</label>
                        <label class="inline radio"><input type="radio" name="from" value="travel">旅行者(<?php echo $comment_count['travel_count']; ?>)</label>
                        <label class="inline radio"><input type="radio" name="from" value="surf">沙发客(<?php echo $comment_count['surf_count']; ?>)</label>
                        <label class="inline radio"><input type="radio" name="from" value="host">沙发主(<?php echo $comment_count['host_count']; ?>)</label>
                    </p>
                </div>
                <?php if(Yii::app()->user->id && Yii::app()->user->id != $uid) { ?>
                <a href="/user/<?php echo $uid; ?>/commentModal" class="btn btn-info user-comment-modal" data-toggle="modal" >我来评价他</a>
                <?php } ?>
            </div>
            <ul class="unstyled user-comment-list">
            </ul>
            <button class="btn load-more-btn span10">查看更多</button>
        </div>
    </div>

</div>

<script>
    $(function() {
        UserComment.init($('.user-comment-box'));
    });
</script>