<li uid="<?php echo $user['id']; ?>" style="width:120px;height:60px;float:left">
    <img class="pull-left" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_TINY, $user['avatar']); ?>">
    <div style="margin-left: 50px;">
        <a title="<?php echo CHtml::encode($user['name']); ?>" href="/user/<?php echo $user['id']; ?>"><?php echo CHtml::encode(Utils::tripDescStriper($user['name'], 12)); ?></a><br>
        <p><small><?php echo Helpers::cityName($user['live_city_id']); ?></small></p>
    </div>
</li>