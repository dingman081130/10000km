<div class="row">
    <div class="span8">
        <ul class="nav nav-tabs" id="myTab">
            <li><a href="/account/signup">设置用户信息</a></li>
            <li class="active"><a href="/account/bindAccount">绑定到已有账号</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <form class="form-horizontal" method="post">
                    <?php echo Helpers::csrfInput();?>
                    
                    <div class="control-group <?php if ($form->getError('login_email')) echo 'error'; ?>">
                        <label class="control-label">邮箱</label>
                        <div class="controls">
                            <input type="text" id="bind-email" name="login_email" placeholder="Email" value="<?php echo CHtml::encode($form->login_email);?>">
                            <span class="help-inline"><?php if ($form->getError('login_email')) echo $form->getError('login_email'); ?></span>
                        </div>
                    </div>

                    <div class="control-group  <?php if ($form->getError('password')) echo 'error'; ?>">
                        <label class="control-label">密码</label>
                        <div class="controls">
                            <input type="password" id="bind-password" name="password">
                            <span class="help-inline"><?php if ($form->getError('password')) echo $form->getError('password'); ?></span>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <button type="submit" class="btn btn-primary">开始一万公里</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="span4">
        <img src="<?php echo CHtml::encode($avatar); ?>">
    </div>
</div>

<script>
    $('form input').focus(function() {
        $(this).parent().parent().removeClass('error');
        $(this).next('span').text('');
    });
</script>