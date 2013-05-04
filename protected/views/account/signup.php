<div class="row">
    <div class="span8">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="/account/signup">设置用户信息</a></li>
            <li><a href="/account/bindAccount">绑定到已有账号</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane in active" id="new-account">
                <form class="form-horizontal" method="post">
                    <?php echo Helpers::csrfInput();?>
                    
                    <!--div class="control-group <?//php if ($form->getError('invitation')) echo 'error'; ?>">
                        <label class="control-label">请输入邀请码</label>
                        <div class="controls">
                            <input type="text" id="invitation-input" name="invitation" value="">
                            <span class="help-inline"><?//php if ($form->getError('invitation')) echo $form->getError('invitation'); ?></span>
                        </div>
                    </div-->
                    
                    <div class="control-group <?php if ($form->getError('name')) echo 'error'; ?>">
                        <label class="control-label">用户名</label>
                        <div class="controls">
                            <input type="text" id="name-input" name="name" value="<?php echo CHtml::encode($form->name);?>">
                            <span class="help-inline"><?php if ($form->getError('name')) echo $form->getError('name'); ?></span>
                        </div>
                    </div>


                    <div class="control-group <?php if ($form->getError('login_email')) echo 'error'; ?>">
                        <label class="control-label">设置登录邮箱</label>
                        <div class="controls">
                            <input type="text" id="email-input" name="login_email" placeholder="Email" value="<?php echo CHtml::encode($form->login_email); ?>">
                            <span class="help-inline"><?php if ($form->getError('login_email')) echo $form->getError('login_email'); ?></span>
                        </div>
                    </div>

                    <div class="control-group <?php if ($form->getError('password')) echo 'error'; ?>">
                        <label class="control-label">设置登录密码</label>
                        <div class="controls">
                            <input type="password" id="password-input" name="password">
                            <span class="help-inline"><?php if ($form->getError('password')) echo $form->getError('password'); ?></span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">再次输入密码</label>
                        <div class="controls">
                            <input type="password" id="repassword-input">
                            <span class="help-inline"></span>
                        </div>
                    </div>
                    

                        <div class="controls">
                            <label class="checkbox"><input type="checkbox" name="share" value="1" checked>分享给好友</label>
                            <span class="help-inline"></span>
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
    
    $('#name-input').blur(function() {
        var _this = this;
        
        if ($(this).val() == '') {
            $(_this).parent().parent().addClass('error');
            $(_this).next('span').text('用户名不能为空');
            return;
        }
        
        $.get('/account/nameAvailable', {
            name : $(_this).attr('value')
        }, function(data) {
            if (data['code'] != 0) {
                $(_this).parent().parent().addClass('error');
                $(_this).next('span').text(data['msg']);
            }
        });
    });
    
    /*
    $('#invitation-input').blur(function(){
        
        var _this = this;
        if($.trim($(this).val())==''){
            $(_this).parent().parent().addClass('error');
            $(_this).next('span').text('邀请码不能为空');
            return true;
        }
        
        $.get('/account/InvitationAvailable', {
            invitation:$(_this).attr('value')
        }, function(data){
            if (data['code'] != 0) {
                $(_this).parent().parent().addClass('error');
                $(_this).next('span').text(data['msg']);
            }
        }, 'json');
        
    });*/
    
    $('#email-input').blur(function() {
        var _this = this;
        
        if (!/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($(this).val())) {
            $(_this).parent().parent().addClass('error');
            $(_this).next('span').text('邮箱地址不合法');
            return;
        }
        
        $.get('/account/emailExist', {
            email : $(_this).val()
        }, function(data) {
            if (data['code'] != 0) {
                $(_this).parent().parent().addClass('error');
                $(_this).next('span').text(data['msg']);
            } else {
                $(_this).next('span').text('');
            }
        });
    });
    
    
    $('#password-input').blur(function() {
        if ($(this).val().length < 6) {
            $(this).parent().parent().addClass('error');
            $(this).next('span').text('密码长度至少为6');
        }
    });
    
    $('#repassword-input').blur(function() {
        if ($(this).val() != $('#password-input').val()) {
            $(this).parent().parent().addClass('error');
            $(this).next('span').text('两次输入的密码不一致');
        }
    });
    

</script>