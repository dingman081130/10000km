<?php
if (Yii::app()->user->id) {
    ?>

<li>
    <a href="/user/<?php echo Yii::app()->user->id; ?>" style="padding: 2px 0 0 0;">
        <img id="sb" style="width: 35px;height: 35px;margin-top: 3px;margin-right: -10px;" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_TINY, Yii::app()->user->attrs['avatar']); ?>">
    </a>
</li>
<li>
        <a class="dropdown-toggle pull-left" data-toggle="dropdown" href="#"><?php echo CHtml::encode(Yii::app()->user->attrs['name']); ?><b class="caret"></b></a>
        <ul class="dropdown-menu">
            <!--li><a href="/user/<?php echo Yii::app()->user->id; ?>"><i class="icon-user"></i> 我的主页 </a></li-->
            <li><a href="/home"><i class="icon-home"></i> 用户中心 </a></li>
            <li><a href="/home/couchProvide"><i class="icon-leaf"></i> 我的沙发</a></li>
            <li><a href="/home/message"><i class="icon-envelope"></i> 消息 <span class="badge badge-info"><?php echo $unread_msg_cnt; ?></span></a></li>
            <li><a href="/home/profile"><i class="icon-wrench"></i> 个人资料 </a></li>
            <li class="divider"></li>
            <li><a href="/account/logout"><i class="icon-off"></i> 退出 </a></li>
        </ul>
</li>
<script>
    /*
    $('#sb').qtip({
        content: {
            text: '<a href="#">something</a>'
        },
        position: {
            at: 'bottom center',
            my: 'top center',
            viewport: $(window),
            effect: false
        },
        hide: {
            event: '',
            solo: true
        },
        style: {
            classes: 'qtip-shadow qtip-bootstrap'
        }
    });
    
    $(function() {
        function get_message() {
            $.ajax({
                url: '/api/getMessage',
                success: function (data) {
                    
                },
                complete: get_message,
                timeout: 3000
            });
        }
        //get_message();
    });
    $('#sb').qtip('option', 'content.text', 'sbbbb').qtip('show');
    */
</script>

    <?php
} else {
    ?>
    <li>
        <button data-toggle="modal" data-target="#login-modal" class="btn btn-primary" style="margin-top: 7px;">登录</button>
    </li>
    <li>
        <button data-toggle="modal" data-target="#register-modal" class="btn btn-info" style="margin-top: 7px; margin-left: 10px;">注册</button>
    </li>

<?php } ?>