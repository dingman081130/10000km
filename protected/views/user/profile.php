<?php
$this->widget('UserPageHeaderWidget', array(
    'user' => $user,
)); 

$uid = intval($user['id']);
?>

<div class="row" style="margin-top: 10px;">
    <div class="span12" style="position: relative;">
        <div style="position: absolute; top: -70px;" id="tab"></div>
        <?php $this->renderPartial('userTabs', array('uid' => $uid, 'active' => 'profile')) ?>

        <div>
            <h5>基本信息</h5>
            <dl class="dl-horizontal" style="margin-left: -90px;">
                <dt>昵称：</dt>
                <dd><?php echo CHtml::encode($user['name']); ?></dd>
                <dt>所在地：</dt>
                <dd><?php if($user['live_city_id']) echo Chtml::encode(Helpers::cityName($user['live_city_id'])); else echo '暂未填写';?></dd>
                <dt>性别：</dt>
                <dd><?php echo $user['sex'] == 0 ? '女':'男' ?></dd>
                <dt>年龄：</dt>
                <dd><?php echo Helpers::ageFromBirthday($user['birthday']); ?></dd>
                <dt>简介：</dt>
                <dd><?php echo $user['description'] ? CHtml::encode($user['description']) : '暂未填写'; ?></dd>
                
            </dl>

            <h5>联系信息</h5>
            <dl class="dl-horizontal" style="margin-left: -90px;">
                <dt>博客：</dt>
                <dd><?php if (!$user['website']) echo '暂未填写'; else { ?>
                    <a href="<?php echo CHtml::encode($user['website']); ?>"><?php echo CHtml::encode($user['website']); ?></a>
                    <?php } ?>
                </dd>
                <dt>QQ：</dt>
                <dd><?php echo $user['qq'] ? CHtml::encode($user['qq']) : '暂未填写'; ?> </dd>
                <dt>邮箱：</dt>
                <dd><?php echo $user['email'] ? CHtml::encode($user['email']) : '暂未填写'; ?> </dd>
            </dl>

            <h5>教育及工作信息</h5>
            <dl class="dl-horizontal" style="margin-left: -90px;">
                <dt>学历：</dt>
                <dd><?php echo $user['education'] ? CHtml::encode($user['education']) : '暂未填写'; ?></dd>
                <dt>职业：</dt>
                <dd><?php echo $user['occupation'] ? CHtml::encode($user['occupation']) : '暂未填写'; ?></dd>
            </dl>
        </div>
    </div>

    <div class="span5">
    </div>
</div>