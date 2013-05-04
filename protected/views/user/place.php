<?php
$this->widget('UserPageHeaderWidget', array(
    'user' => $user,
)); 

$uid = intval($user['id']);
?>

<div class="row" style="margin-top: 20px;">
    <div class="span12" style="position: relative;">
        <div style="position: absolute; top: -70px;" id="tab"></div>
        <?php $this->renderPartial('userTabs', array('uid' => $uid, 'active' => 'place')) ?>

        <div>
            <img style="width: 600px;" src="http://dl.zhishi.sina.com.cn/upload/82/52/80/1081825280.3568845.jpg">
        </div>
    </div>
</div>