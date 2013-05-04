<?php
/**
 * @file class 胶片列表组件
 * @package application.components.widgets
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-1-5
 * @version
 */
Yii::import('application.models.Album.PhotoComment');
foreach($photos as &$photo){
    !isset($photo['comment_count']) && $photo['comment_count'] = PhotoComment::getCommentCnt($photo['id']);
}
?>
<ul class="card-list unstyled" style="margin-top: 10px;">
<?php foreach($photos as $photo){?>  
<li class="card-item">
    <a href="<?php echo '/photo/'.$photo['album_id'].'#'.$photo['id']; ?>">
        <img class="img-polaroid" src="<?php echo CHtml::encode($photo['surl']); ?>">
    </a>
    <div>
        <span class="pull-left clearfix">
            <?php echo CHtml::encode(Utils::tripDescStriper($photo['title'],20)); ?>
        </span>
        <span class="pull-right clearfix">
            <i class="icon-comment"></i><?php echo $photo['comment_count']; ?>
        </span>
    </div>
</li>
<?php }?>
</ul>