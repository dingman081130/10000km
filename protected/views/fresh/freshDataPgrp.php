<?php
Yii::import('application.models.Group.Group');
Yii::import('application.models.Group.GroupCategory');
$gid = $fresh['content']['grp_id'];
$group = Group::getGroup($gid);
$a_url = ImageUrlHelper::imgUrl(ImageUrlHelper::GROUP_IMAGE, $group['image']);
$cname = GroupCategory::getCategoryName($group['category_id']);
?>

<div class="fresh-header">
    <a href="/user/<?php echo $fresh['uid']; ?>"><?php echo CHtml::encode($fresh['uname']); ?></a> <span class="muted">创建了讨论组</span>
</div>
<div class="fresh-body clearfix">
    <div class="pull-left" style="width: 85%">
        <div>
            <span class="muted">标题:</span>
            <a href="/group/<?php echo CHtml::encode($gid);?>"><?php echo CHtml::encode(Utils::tripDescStriper($group['name'], 100)); ?></a>
            <span class="label label-info"><?php echo CHtml::encode($cname); ?></span>
            <small class="muted"><?php echo CHtml::encode($group['user_count']); ?>人参与</small>
        </div>
        <div>
            <span class="muted">描述: <?php echo CHtml::encode(Utils::tripDescStriper($group['description'], 200)); ?></span>
        </div>
    </div>
    <img class="pull-right" src="<?php echo CHtml::encode($a_url) ?>" style="width: 40px;height: 40px;margin-right: 10px;">
    
</div>
<div class="fresh-footer">
    <div class="pull-left">
        <span><small class="muted"><?php echo CHtml::encode($fresh['ftime']) ?></small></span>
    </div>
</div>