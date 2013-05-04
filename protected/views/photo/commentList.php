<div class="photo-comment-list clearfix" page_size="<?php echo Yii::app()->params['commentPageSize'];?>">
    <?php
    foreach ($comments as $comment) {
        $this->renderView(array('photo', 'commentItem'), array('comment' => $comment,));
    }
    if (count($comments) >= Yii::app()->params['commentPageSize']) {?>
        <button class="btn" id="comments_load_more" hasmore ="yes" style="width: 100%;text-align: center;"><small>加载更多</small></button>
    <?php }?>
</div>
