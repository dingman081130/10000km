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
            <li><a href="/home/socialAccount">账号绑定</a></li>
            <li class="active"><a href="/home/password">修改密码</a></li>
        </ul>

        <form class="form-horizontal change-password-form" method="post">
            <?php if ($msg) { ?>
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <p><?php echo CHtml::encode($msg); ?></p>
            </div>
            <?php } ?>
            <?php echo Helpers::csrfInput(); ?>
            <div class="control-group <?php if ($form->getError('password')) echo 'error'; ?>">
                <label class="control-label">旧密码：</label>
                <div class="controls">
                    <input type="password" name="password">
                    <span class="help-inline"><?php if ($form->getError('password')) echo CHtml::encode($form->getError('password')); ?></span>
                </div>
            </div>
            <div class="control-group <?php if ($form->getError('new_password')) echo 'error'; ?>">
                <label class="control-label">新密码：</label>
                <div class="controls">
                    <input type="password" name="new_password">
                    <span class="help-inline"><?php if ($form->getError('new_password')) echo CHtml::encode($form->getError('new_password')); ?></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">再输入新密码：</label>
                <div class="controls">
                    <input type="password" name="re_password">
                    <span class="help-inline"></span>
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">修改密码</button>
                </div>
            </div>
        </form>

    </div>

</div>

<script>
    $('.change-password-form').submit(function() {
        var password = $(this).find('input[name="password"]').val();
        var new_password = $(this).find('input[name="new_password"]').val();
        var re_password = $(this).find('input[name="re_password"]').val();
        
        if (!password.length) {
            $(this).find('input[name="password"]').next('span').text('请输入旧密码');
            $(this).find('input[name="password"]').closest('.control-group').addClass('error');
            return false;
        }
        
        if (new_password.length < 6) {
            $(this).find('input[name="new_password"]').next('span').text('密码长度不能小于6');
            $(this).find('input[name="new_password"]').closest('.control-group').addClass('error');
            return false;
        }
        
        if (new_password != re_password) {
            $(this).find('input[name="re_password"]').next('span').text('两次输入的密码不一致');
            $(this).find('input[name="re_password"]').closest('.control-group').addClass('error');
            return false;
        }
    });
    
    $('.change-password-form input').focus(function() {
        $(this).next('span').text('');
        $(this).closest('.control-group').removeClass('error');
    });
</script>

