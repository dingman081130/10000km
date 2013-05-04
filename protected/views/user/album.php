<?php
$this->widget('UserPageHeaderWidget', array(
    'user' => $user,
));

$uid = intval($user['id']);
?>

<div class="row" style="margin-top: 10px;">
    <div class="span12" style="position: relative;">
        <div style="position: absolute; top: -70px;" id="tab"></div>
        <?php $this->renderPartial('userTabs', array('uid' => $uid, 'active' => 'album')) ?>
        <ul class="unstyled album-list">
            <?php foreach ($albums as $one) { ?>
                <li style="float: left; text-align: center; position: relative;  height: 200px; width: 160px; margin: 0 20px 20px 0;">
                    <a href="/album/<?php echo $one['id']; ?>" title="<?php echo CHtml::encode($one['name']); ?>">
                        <div class="thumbnail">
                            <div style="overflow: hidden;" >
                            <img  style="width: 150px; height: 150px;" src="<?php echo CHtml::encode($one['cover_surl']); ?>">
                            </div>
                        </div>
                        <span class="label label-inverse" style="position: absolute; right: 10px; top: 130px; opacity: 0.8;">共<?php echo $one['photo_count']; ?>张</span>
                        <p style="margin-bottom: 0;"><b><?php echo CHtml::encode(Helpers::substr($one['name'], 10, true)); ?></b></p>
                    </a>
                    <p class="muted"><small>更新于<?php echo Helpers::friendlyTime($one['update_time']); ?></small></p>
                </li>
            <?php } ?>
        </ul>
    </div>


</div>