<div class="row">
    <div class="span8">
        <?php
        $this->widget('BreadCrumbWidget', array('crumbs' => array(
                array(
                    'name' => '小组',
                    'url' => array('/group'),
                ),
                array(
                    'name' => $group['name'],
                    'url' => array('/group/' . $group['id']),
                ),
                array(
                    'name' => '小组成员',
                    'url' => array('/group/users/' . $group['id']),
                ),
                )));
        ?>
        <ul class="unstyled inline">
            <?php foreach ($users as $user) { ?>
            <li style="float:left;font-size: 14px;">
                <a href="/user/<?php echo $user['id'] ?>">
                    <img class="pull-left" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $user['avatar']); ?>">
                </a>
                <div style="margin-left:70px">
                    <a href="/user/<?php echo $user['id']; ?>"><?php echo CHtml::encode($user['name']); ?></a>
                    <p class="muted" style="margin:0px;"><?php echo $user['sex']==0 ? '女':'男' ?>，<?php echo Helpers::ageFromBirthday($user['birthday']); ?>岁</p>
                    <p class="muted" style="margin:0px;"><?php echo Helpers::cityName($user['live_city_id']); ?></p>
                </div>
            </li>
            <?php } ?>
        </ul>
    </div>
</div>
