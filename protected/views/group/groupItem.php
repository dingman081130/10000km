<?php
    $user_cnt = $group['user_count'];
    $topic_cnt = $group['topic_count'];
?>
<div style="float:left;width: 300px;height:90px;margin-right: 10px;margin-top: 10px;border-bottom:  #CCC solid 1px;">
    <div class="avatar pull-left" style="width:60px;height: 60px">
        <a href="/group/<?php echo CHtml::encode($group['id']); ?>">
        <img src="<?php echo CHtml::encode($group['a_url']); ?>">
        </a>
    </div>
    <div class="data" style="margin-left:65px;">
        <div class="header clearfix" style="height: 20px;">
            <a class="pull-left" href="/group/<?php echo CHtml::encode($group['id']); ?>"><?php echo CHtml::encode(Helpers::substr($group['name'], 10)); ?></a>
            <small class="pull-right muted" style="margin-left:5px;"><?php echo CHtml::encode($user_cnt); ?>个成员</small>
            <small class="pull-right muted" style="margin-left:5px;"><?php echo CHtml::encode($topic_cnt); ?>个话题</small>
        </div>
        <div class="body">
            <small><?php echo CHtml::encode(Helpers::substr($group['description'], 35)); ?></small>
        </div>
        <div class="footer clearfix">
            <?php
                $cid = $group['category_id'];
                $cname = isset($categories[$cid]) ? $categories[$cid] : '';
                if($cname){
            ?>
                <span cid="<?php echo CHtml::encode($cid); ?>" class="pull-left label label-info"><?php echo $cname; ?></span>
            <?php } ?>
                <small class="pull-right muted"><?php echo Helpers::friendlyTime($group['create_time']); ?></small>
        </div>
    </div>
</div>