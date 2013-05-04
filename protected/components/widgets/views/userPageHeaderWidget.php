<?php
$loc_arr = Utils::getIpLocation($user['last_login_ip']);
$loc_str = Utils::locationToString($loc_arr);
?>
<div class="row clearfix user-page-head">
    <div class="span7">
        <div class="pull-left">
            <img style="width:120px;height: 120px;" class="img-polaroid" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_LARGE, $user['avatar']); ?>">
        </div>

        <div style="margin-left: 140px;">
            <h3 style="margin-top:0px;margin-bottom: 0px;"><?php echo CHtml::encode($user['name']); ?>  </h3>
            <p style="margin-bottom:3px">
                <?php echo $user['sex'] == 0 ? '女' : '男'; ?>，<?php echo Helpers::ageFromBirthday($user['birthday']); ?>岁，<?php echo CHtml::encode(Helpers::cityName($user['live_city_id'])); ?>
                <small class="muted pull-right">最近登录: <?php echo Helpers::timeDelta($user['last_login_time']); ?><?php if (!empty($loc_str)) echo '从'.CHtml::encode($loc_str).'登录'; ?></small>
            </p>
            
            <span class="muted">简介：<?php echo CHtml::encode(Helpers::substr($user['description'], 30)); ?></span>
            <ul class="unstyled inline" style="margin-top:10px;">
                <?php if ($couch && $user['id'] != Yii::app()->user->id) { ?>
                    <li>
                        <a uid="<?php echo $user['id']; ?>" href="<?php echo '/user/'.$user['id'].'/couch'; ?>">
                            <?php $this->controller->renderView(array('couch', 'couchItem'), array('size' => 60, 'cnt' => $couch['capacity'])); ?>
                        </a>
                    </li>
                <?php 
                }
                if (Yii::app()->user->id != $user['id']) { ?>
                    <li><a href="#" uid="<?php echo $user['id']; ?>" class="auth btn unfollow-user-btn <?php if (!Yii::app()->user->id || !$ifollowed) echo 'hide'; ?>">取消关注</a></li>
                    <li><a href="#" uid="<?php echo $user['id']; ?>" class="auth btn btn-small follow-user-btn <?php if ($ifollowed) echo 'hide'; ?>">+关注</a></li>
                    <li><a href="#" uid="<?php echo $user['id']; ?>" class="auth btn btn-small message-btn" data-toggle="modal">私信</a></li>
                <?php } ?>
            </ul>
        </div>

        <div class="couch-info-box hide">
            <?php
            $capacity = intval($couch['capacity']);
            $guest_sex = intval($couch['guest_sex']);
            $no_smoke = $couch['no_smoke'];
            ?>

            <h4>为客人提供沙发</h4>
            <dl class="span4 dl-horizontal" style="margin-left: -60px;">
                <dt>可接纳人数:</dt>
                <dd><?php if ($capacity >= 1 && $capacity <= 5) echo $capacity; else echo '6+'; ?></dd>

                <dt>性别要求:</dt>
                <dd>
                    <?php
                    if ($guest_sex == 1)
                        echo '男';
                    else if ($guest_sex == 0)
                        echo '女';
                    else
                        echo '不限';
                    ?>
                </dd>

                <dt>禁烟:</dt>
                <dd><?php echo $no_smoke ? '是' : '否'; ?></dd>
            </dl>
            <div style="text-align: center;">
                <?php if (Yii::app()->user->id) { ?>
                <a href="/user/couchRequestModal/<?php echo $user['id']; ?>" class="btn btn-primary request-couch-btn" data-toggle="modal">申请沙发</a>
                <?php } ?>
            </div>
        </div>
    </div>


    <div class="span5">
        <div>
            <h4>最近想去<?php if (Yii::app()->user->id == $user['id']) { ?><a href="/home/personality"><small> 编辑</small></a><?php } ?></h4>
            <ul class="unstyled clearfix want-place-list">
                <?php
                $list = explode(',', $user['want_places']);
                foreach ($list as $one) {
                    if (!empty($one)) {
                ?>
                <li><?php echo CHtml::encode($one); ?></li>
                <?php }} ?>
            </ul>
        </div>

        <div>
            <h4>个性标签<?php if (Yii::app()->user->id == $user['id']) { ?><a href="/home/personality"><small> 编辑</small></a><?php } ?></h4>
            <ul class="unstyled clearfix personality-tags-list">
                            <?php
                $list = explode(',', $user['personal_tags']);
                foreach ($list as $one) {
                    if (!empty($one)) {
                ?>
                <li><?php echo CHtml::encode($one); ?></li>
                <?php }} ?>
            </ul>
        </div>
    </div>
</div>

<script>
    $(function() {
        UserPageHead.init($('.user-page-head'));
    });
</script>