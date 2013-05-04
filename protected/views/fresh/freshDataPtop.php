<?php
Yii::import('application.models.Group.Topic');
Yii::import('application.models.Group.Group');
Yii::import('application.models.Group.GroupCategory');

$tid = $fresh['content']['topic_id'];
$topic = Topic::getTopic($tid);
$gid = $topic['group_id'];
$group = Group::getGroup($gid);
$a_url = ImageUrlHelper::imgUrl(ImageUrlHelper::GROUP_IMAGE, $group['image']);
$cname = GroupCategory::getCategoryName($group['category_id']);
?>

<div class="fresh-header">
    <a href="/user/<?php echo $fresh['uid']; ?>"><?php echo CHtml::encode($fresh['uname']); ?></a> <span class="muted">发布了话题</span>
</div>
<div class="fresh-body clearfix">
    <div class="pull-left">
        <div>
            <span class="muted">标题:</span>
            <a href="/topic/<?php echo CHtml::encode($tid);?>"><?php echo CHtml::encode($topic['title']); ?></a>
            <small class="muted pull-right"><?php echo CHtml::encode($topic['reply_count']); ?>人回复</small>
        </div>
        <div>
            <span class="muted">所属小组:</span>
            <a href="/group/<?php echo CHtml::encode($gid);?>"><?php echo CHtml::encode(Utils::tripDescStriper($group['name'], 200)); ?></a>
            <span class="label label-info"><?php echo CHtml::encode($cname); ?></span>
            <small class="muted pull-right"><?php echo CHtml::encode($group['user_count']); ?>人参与</small>
        </div>
        <div style="width:450px;word-break: break-all">
            <span class="muted">描述: <?php echo Utils::tripDescStriper($topic['content'], 300); ?></span>
        </div>
    </div>
    
    
</div>
<div class="fresh-footer">
    <div class="pull-left">
        <span><small class="muted"><?php echo CHtml::encode($fresh['ftime']) ?></small></span>
    </div>
</div>