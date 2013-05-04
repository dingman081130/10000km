<?php
Yii::import('application.models.User.User');
$sysid = User::getSysId();
?>
<li class="message-item clearfix" style="margin-bottom: 10px;">
    <?php if ( Yii::app()->user->id != $msg['sender_user']['id'] ) { ?>
        <div class="pull-left">
            <a class="message-avatar" href="/user/<?php echo $msg['sender_user']['id']; ?>">
                <img class="img-polaroid" style="width:40px;height:40px" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $msg['sender_user']['avatar']); ?>">
            </a>
        </div>
        
        <div class="message-data clearfix" style="max-width: 500px;margin-left:60px">
            <div>
                <small>
                    <a href="/user/<?php echo $msg['sender_user']['id']; ?>"><?php echo CHtml::encode($msg['sender_user']['name']); ?></a>
                    <span class="muted"><?php echo Helpers::friendlyTime($msg['send_time']); ?></span>
                </small>
            </div>
            <div class="pull-left well" style="padding:8px;margin-bottom: 0px;">
            <?php
                if ($msg['is_read'] || $msg['sender'] == Yii::app()->user->id) {
                    //// 如果是系统发出的消息则不需要转义
                    if ($msg['sender'] == $sysid) {
                        echo $msg['content'];
                    } else {
                        echo CHtml::encode($msg['content']);
                    }
                } else {
                    if ($msg['sender'] == $sysid) {
                        echo '<strong class="unread" name="' . $msg['id'] . '">' . $msg['content'] . '</strong>';
                    } else {
                        echo '<strong class="unread" name="' . $msg['id'] . '">' . CHtml::encode($msg['content']) . '</strong>';
                    }
                }
                ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="pull-right">
            <a class="message-avatar" href="/user/<?php echo $msg['sender_user']['id']; ?>">
                <img class="img-polaroid" style="width:40px;height:40px" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $msg['sender_user']['avatar']); ?>">
            </a>
        </div>
        
        <div class="message-data pull-right" style="max-width: 500px;margin-right:10px">
            <div class="pull-rigth">
                <small class="pull-right">
                    <a href="/user/<?php echo $msg['sender_user']['id']; ?>"><?php echo CHtml::encode($msg['sender_user']['name']); ?></a>
                    <span class="muted"><?php echo Helpers::friendlyTime($msg['send_time']); ?></span>
                </small>
            </div>
            <div class="pull-right well" style="padding: 8px;margin-bottom: 0px;">
            <?php
                if ($msg['is_read'] || $msg['sender'] == Yii::app()->user->id) {
                    //// 如果是系统发出的消息则不需要转义
                    if ($msg['sender'] == $sysid) {
                        echo $msg['content'];
                    } else {
                        echo CHtml::encode($msg['content']);
                    }
                } else {
                    if ($msg['sender'] == $sysid) {
                        echo '<strong class="unread" name="' . $msg['id'] . '">' . $msg['content'] . '</strong>';
                    } else {
                        echo '<strong class="unread" name="' . $msg['id'] . '">' . CHtml::encode($msg['content']) . '</strong>';
                    }
                }
                ?>
            </div>
        </div>
    <?php } ?>
</li>