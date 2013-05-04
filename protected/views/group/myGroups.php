<?php
Yii::import('application.models.User.User');
Yii::import('application.models.Group.Group');
Yii::import('application.models.Group.GroupCategory');

$categories = GroupCategory::getAllCategories();
foreach ( $groups as &$group1 ) {
    $group1['a_url'] = ImageUrlHelper::imgUrl(ImageUrlHelper::GROUP_IMAGE, $group1['image']);
}

?>
<div class="row">
    <div class="span8">
        <h3>我的旅行小组</h3>

        <ul class="nav nav-tabs">
            <li><a href="/group">我的小组话题</a></li>
            <li><a href="/group/myTopics">我发起的话题</a></li>
            <li><a href="/group/myReplies">我回复的话题</a></li>
            <li class="active"><a href="/group/myGroups">我加入的小组</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div id="group-list" class="clearfix">
                <?php 
                    foreach ( $groups as $group ) { 
                        $this->renderPartial('groupItem', array('group' => $group, 'categories' => $categories));
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="span4">
        <?php
        $this->renderPartial('mySidebar');
        ?>
    </div>
</div>
