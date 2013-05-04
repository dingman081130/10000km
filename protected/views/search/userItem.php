<?php

/**
 * @param $user
 */

Yii::import('application.models.User.UserFollow');
Yii::import('application.models.User.UserComment');
Yii::import('application.models.Album.Album');

$fans_count = UserFollow::getUserFansCount($user['id']);
$tmp = UserComment::getCommentCount($user['id']);
$comment_count = $tmp['comment_count'];
$photo_count = Album::getUserPhotoCount($user['id']);
//print_r($user);exit;
?>


<li>
    <div style="position: relative; padding-top: 5px; margin: 20px 0; border: 1px solid #DFDFDF; border-radius: 5px;">

        <div class="thumbnail" style="position: absolute; left: 10px; top: 10px;">
            <a href="/user/<?php echo intval($user['id']); ?>"><img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_MIDDLE, $user['avatar']); ?>"></a>
        </div>

        <div style="margin-left: 120px; padding-right: 10px;">
            <div>
                <h4><a href="/user/<?php echo intval($user['id']); ?>"><?php echo CHtml::encode($user['name']); ?></a>&nbsp;<small><?php echo CHtml::encode(Helpers::cityName($user['live_city_id'])) ?></small></h4>
                <div class="muted">上次登录: <?php echo Helpers::timeDelta($user['last_login_time']); ?></div>
                <ul class="unstyled">
                    <li style="display: inline-block;">粉丝<span class="badge badge-info"><?php echo $fans_count; ?></span></li>
                    <li style="display: inline-block;">评价<span class="badge badge-info"><?php echo $comment_count; ?></span></li>
                    <li style="display: inline-block;">照片<span class="badge badge-info"><?php echo $photo_count; ?></span></li>
                </ul>
                <ul class="unstyled" style="position: absolute; right: 10px; top: 15px;width: 120px; height: 60px">
                    <?php if (isset($user['couch']['available']) && $user['couch']['available'] == 1 ) { ?>
                    <li class="pull-right" style="display: inline-block">
                        <a class="couch-info-btn" href="/user/<?php echo $user['id']; ?>/couch" title="该用户为客人提供沙发">
                            <?php $this->renderView(array('couch', 'couchItem'), array('size' => 90, 'cnt' => $user['couch']['capacity'])); ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <div style="margin-top: 10px;">
                <dl class="dl-horizontal" style="margin-left: -100px;">
                    <dt>基本信息:</dt>
                    <dd><?php echo $user['sex'] == 0 ? '女' : '男'; ?>，<?php echo Helpers::ageFromBirthday($user['birthday']); ?>岁<?php if (!empty($user['occupation'])) echo '，'.CHtml::encode($user['occupation']); ?></dd>
                    <dt>介绍:</dt>
                    <dd><?php echo CHtml::encode($user['description']); ?></dd>
                </dl>
            </div>
        </div>

        <?php
        if (isset($user['couch_search'])) {
            $s = $user['couch_search'];
        ?>
        <div style="background-color: whitesmoke; padding: 5px 10px;">
            <div class="row">
                <div class="span8">
                    <h5><a href="/user/<?php echo $user['id']; ?>"><?php echo CHtml::encode($user['name']); ?></a> 正在寻找沙发</h5>
                </div>
            </div>
            <div class="row">
                <div class="span5">
                    <p><?php echo CHtml::encode($s['detail']); ?></p>
                </div>
                <div class="span3" style="width: 200px;">
                    <dl class="dl-horizontal" style="margin-left: -100px; margin-top: 0;">
                        <dt>城市:</dt>
                        <dd><?php echo Helpers::cityName($s['city_id']); ?></dd>
                        <dt>到达日期:</dt>
                        <dd><?php echo $s['arrive_date']; ?></dd>
                        <dt>离开日期:</dt>
                        <dd><?php echo $s['leave_date']; ?></dd>
                        <dt>人数:</dt>
                        <dd><?php echo $s['number']; ?></dd>
                    </dl>
                    <?php if (Yii::app()->user->id) { ?>
                    <button class="btn btn-success pull-right invite-btn" data-toggle="modal" sid="<?php echo $s['id']; ?>">邀请这位沙发客</button>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</li>
