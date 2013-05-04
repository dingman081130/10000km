<?php
Yii::import('application.models.User.User');
Yii::import('application.models.Group.Group');
?>
<div class="row">
    <div class="span8">
        <h3>我的旅行小组</h3>

        <ul class="nav nav-tabs">
            <li><a href="/group">我的小组话题</a></li>
            <li class="active"><a href="/group/myTopics">我发起的话题</a></li>
            <li><a href="/group/myReplies">我回复的话题</a></li>
            <li><a href="/group/myGroups">我加入的小组</a></li>
        </ul>

        <?php $this->renderView(array('topic', 'topicList'), array('topics' => $topics)); ?>
    </div>
    
    <div class="span4">
        <?php
        $this->renderPartial('mySidebar');
        ?>
    </div>
</div>
