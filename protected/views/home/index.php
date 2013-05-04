<?php
Yii::import('application.models.User.UserFollow');
?>
<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'index',
        ));
        ?>
    </div>

    <div class="span7" style=" margin: 0 10px 10px 0px; border-left: 1px solid #CCC;">
        <?php
            $this->widget('PubHeehawWidget', array());
            $this->renderView(array('fresh','freshList'), array('freshes' => $freshes));
        ?>
    </div>

    <div class="span3" >
        <div class="clearfix">
            <div class="pull-left" style="width: 100px;">
                <a href="/user/<?php echo $user['id']; ?>" class="thumbnail"><img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_MIDDLE, $user['avatar']); ?>"></a>
            </div>

            <div class="pull-left" style="margin-left: 10px;">
                <p><h5><a href="/user/<?php echo $user['id']; ?>"><?php echo CHtml::encode($user['name']); ?></a></h5></p>
                <p>粉丝:<a href="/home/myfans"><span class="badge badge-info"><?php echo UserFollow::getUserFansCount($user['id']); ?></span></a></p>
                <p>关注:<a href="/home/myfollow"><span class="badge badge-info"><?php echo UserFollow::getUserFollowCount($user['id']); ?></span></a></p>
            </div>
        </div>

        <div>
            <div>
                <div style="border-bottom: 1px solid #EEE;">
                    <h4>你们可能感兴趣的人</h4>
                </div>
                <ul class="unstyled">
                    <?php foreach ($suggest_users as $one) { ?>
                    <li class="clearfix" style="margin: 10px 0;">
                        <a href="/user/<?php echo $one['id']; ?>"><img style="width: 40px; float: left;" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $one['avatar']); ?>"></a>
                        <div style="margin-left: 50px;">
                            <ul class="unstyled">
                                <li><a href="/user/<?php echo $one['id']; ?>"><?php echo CHtml::encode($one['name']); ?></a><small class="muted"> <?php echo Helpers::cityName($one['live_city_id'], false); ?></small></li>
                                <li><?php echo $one['sex'] == 0 ? '女':'男' ?>，<?php echo Helpers::ageFromBirthday($one['birthday']); ?>岁</li>
                            </ul>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        Fresh.waterfall();
        Fresh.initFreshList($('.fresh-list'));
        Heehaw.init($('.heehaw'));
    });
        
</script>