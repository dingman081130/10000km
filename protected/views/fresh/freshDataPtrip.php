<?php
    Yii::import('application.models.Trip.Trip');
    
    $trip_id = $fresh['content']['trip_id'];
    $trip    = Trip::getTrip($trip_id);
?>
<div class="fresh-header">
    <a href="/user/<?php echo $fresh['uid']; ?>"><?php echo CHtml::encode($fresh['uname']);?></a>&nbsp;<span class="fresh-action">发布了旅行计划</span>
</div>
<div class="fresh-body trip-publish clearfix">
    <a href="/trip/<?php echo $trip['id']; ?>">
        <img class="pull-left" style="max-width: 120px;" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::TRIP_COVER_SMALL, $trip['cover']);?>">
    </a>
    <div style="margin-left: 130px; margin-top: -15px;word-break: break-all;">
        <h5><a href="/trip/<?php echo $trip['id']; ?>"><?php echo CHtml::encode($trip['title']); ?></a></h5>
        <p><?php echo CHtml::encode(Helpers::substr(strip_tags($trip['content']), 80, true)); ?></p>
    </div>
</div>
<div class="fresh-footer">
    <div class="pull-left">
        <span><small class="muted"><?php echo CHtml::encode($fresh['ftime']) ?></small></span>
    </div>
    <?php if(Yii::app()->user->id){ ?>  
        <div class="pull-right">
            <small>
                <?php if(!Trip::isUserFollowTrip(Yii::app()->user->id, $trip_id)){?>
                    <a class="follow-trip-btn" tid="<?php echo $trip_id ?>>" href="#">关注</a>
                <?php }else{?>
                        已关注
                <?php }?>
                <?php if(!Trip::isUserJoinTrip(Yii::app()->user->id, $trip_id)){?>
                    | <a class="join-trip-btn" tid="<?php echo $trip_id ?>" href="#">参加</a>
                <?php }else{?>
                    | 已参加
                <?php }?>
            </small>
        </div>
    <?php }?>
</div>

