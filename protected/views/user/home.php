<?php
$this->widget('UserPageHeaderWidget', array(
    'user' => $user,
)); 

$uid = intval($user['id']);
?>

<div class="row" style="margin-top: 10px;">
    <div class="span7" style="position: relative;">
        <div style="position: absolute; top: -70px;" id="tab"></div>
        <?php $this->renderPartial('userTabs', array('uid' => $uid, 'active' => 'home')); ?>
        <?php $this->renderView(array('fresh', 'freshList'), array('freshes' => $freshes)); ?>
    </div>

    <div class="span5">
        <div>
            <h4>他的关注(<?php echo count($follows); ?>)</h4>
            <?php 
            foreach ($follows as $follow) {
            ?>
            <div class="clearfix" style="margin-bottom: 20px;">
                <img style="width: 60px; float: left;" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $follow['avatar']); ?>">
                <div  style="margin-left: 70px;">
                    <p><a href="/user/<?php echo $follow['id']; ?>"><?php echo CHtml::encode($follow['name']); ?></a></p>
                    <p><small><?php echo $follow['sex'] == 0 ? '女':'男' ?>，<?php echo Helpers::ageFromBirthday($follow['birthday']); ?>岁，<?php echo CHtml::encode(Helpers::cityName($follow['live_city_id'])); ?></small></p>
                </div>
            </div>
            <?php } ?>
        </div>


    </div>
</div>

<script>
    Fresh.waterfallSB();
    Fresh.initFreshList($('.fresh-list'));
</script>