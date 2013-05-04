<div class="participant-list clearfix">
<?php
foreach ($participants as $participant) {
?>
    <a title="<?php echo CHtml::encode($participant['name']); ?>" href="/user/<?php echo $participant['id']; ?>"><img style="width: 60px; border: 1px solid #666;margin-right: 8px; margin-bottom: 8px; float: left;" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $participant['avatar']); ?>"></a>
<?php } ?>
</div>