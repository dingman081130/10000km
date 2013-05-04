<?php
Yii::import('application.models.Emotion.Emotion');
?>
<div class="fresh-header">
    <a href="/user/<?php echo $fresh['uid']; ?>"><?php echo CHtml::encode($fresh['uname']); ?></a>：<?php echo Emotion::replaceEmotion(CHtml::encode($fresh['content']['msg']));?>
</div>
<div class="fresh-body">
</div>
<div class="fresh-footer">
    <div class="pull-left">
        <span><small class="muted"><?php echo CHtml::encode($fresh['ftime']) ?></small></span>
    </div>
</div>