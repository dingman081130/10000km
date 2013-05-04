<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'settings',
        ));
        ?>
    </div>

    <div class="span10">
        <ul class="nav nav-tabs" id="myTab">
            <li><a href="/home/profile">基本资料</a></li>
            <li class="active"><a href="/home/socialAccount">账号绑定</a></li>
            <li><a href="/home/password">修改密码</a></li>
        </ul>

        <div>
            <?php
            Yii::import('application.models.User.Account');
            Yii::import('application.models.AR.SocialAccountAR');
            $uid = Yii::app()->user->id;
            $weibo = Account::isSocialAccountBinded($uid, SocialAccountAR::TYPE_WEIBO);
            $qq = Account::isSocialAccountBinded($uid, SocialAccountAR::TYPE_QQ);
            $douban = Account::isSocialAccountBinded($uid, SocialAccountAR::TYPE_DOUBAN);
            $renren = Account::isSocialAccountBinded($uid, SocialAccountAR::TYPE_RENREN);
            ?>
            <ul class="unstyled">
                <li style="margin: 20px 0;">
                    <?php if ($weibo) { ?>
                    <button class="btn btn-info" disabled>已绑定新浪微博账号</button><button class="btn btn-link btn-mini unbind-btn" type="<?php echo SocialAccountAR::TYPE_WEIBO; ?>">取消绑定</button>
                    <?php } else { ?>
                        <a href="/login/weibo" class="btn btn-info">绑定新浪微博账号</a>
                    <?php } ?>
                </li>
                <li style="margin: 20px 0;">
                    <?php if ($qq) { ?>
                        <button class="btn btn-info" disabled>已绑定QQ账号</button><button class="btn btn-link btn-mini unbind-btn" type="<?php echo SocialAccountAR::TYPE_QQ; ?>">取消绑定</button>
                    <?php } else { ?>
                        <a href="/login/qq" class="btn btn-info">绑定QQ账号</a>
                    <?php } ?>
                </li>
                <li style="margin: 20px 0;">
                    <?php if ($douban) { ?>
                        <button class="btn btn-info" disabled>已绑定豆瓣账号</button><button class="btn btn-link btn-mini unbind-btn" type="<?php echo SocialAccountAR::TYPE_DOUBAN; ?>">取消绑定</button>
                    <?php } else { ?>
                        <a href="/login/douban" class="btn btn-info">绑定豆瓣账号</a>
                    <?php } ?>
                </li>
                <li style="margin: 20px 0;">
                    <?php if ($renren) { ?>
                        <button class="btn btn-info" disabled>已绑定人人网账号</button><button class="btn btn-link btn-mini unbind-btn" type="<?php echo SocialAccountAR::TYPE_RENREN; ?>">取消绑定</button>
                    <?php } else { ?>
                        <a href="/login/renren" class="btn btn-info">绑定人人网账号</a>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </div>

</div>

<script>
    $(function() {
        $('.unbind-btn').click(function() {
            var type = $(this).attr('type');
            $.post('/account/unbindAccount', {
                type: type,
                csrf_token: $('meta[name=csrf_token_value]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    window.location.reload();
                } else {
                    alert('取消绑定失败');
                }
            });
        });
    });
</script>
