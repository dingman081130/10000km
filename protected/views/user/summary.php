<div style="padding: 10px 10px 0 10px;">
    <div class="row">
        <div class="span1 thumbnail">
            <img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $user['avatar']); ?>">
        </div>
        
        <div class="span2">
            <p><a href="/user/<?php echo $user['id']; ?>"><?php echo CHtml::encode($user['name']); ?></a></p>
            <p>
                <small>
                    <?php echo $user['sex'] == 0 ? '女':'男'; ?>,<?php echo Helpers::ageFromBirthday($user['birthday']); ?>岁,<?php echo CHtml::encode(Helpers::cityName($user['live_city_id'])); ?>
                </small>
            </p>
            <p>粉丝:<?php echo intval($fans_count); ?> | 关注:<?php echo intval($follow_count); ?></p>
        </div>
    </div>
    
    <div class="row">
        
        <div class="span3">
            <p><small><span class="muted">简介:</span><?php echo CHtml::encode($user['description']); ?></small></p>
        </div>
    </div>
</div>