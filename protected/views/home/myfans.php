<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'myfans',
        ));
        ?>
    </div>

    <div class="span10">
        <h4>我的粉丝</h4>

        <ul class="unstyled">
            <?php
            foreach ($fans as $fan) {
                ?>
                <li class="pull-left" style="border: 1px solid #CCC; padding: 10px; margin: 0 20px 20px 0; width: 180px; height: 80px;">
                    <div class="clearfix">
                        <div class="pull-left" style="width: 60px;">
                            <img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $fan['avatar']); ?>">
                        </div>
                        <div style="margin-left: 70px;">
                            <a href="/user/<?php echo $fan['id']; ?>"><?php echo CHtml::encode($fan['name']); ?></a>
                            <p><small><?php echo $fan['sex'] == 0 ? '女' : '男'; ?>, <?php echo Helpers::ageFromBirthday($fan['birthday']); ?>岁，<?php echo Helpers::cityName($fan['live_city_id']); ?></small></p>
                        </div>
                    </div>
                    <div>
                        <p><small>简介：<?php echo CHtml::encode(Helpers::substr($fan['description'], 10, true)); ?></small></p>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>

</div>

