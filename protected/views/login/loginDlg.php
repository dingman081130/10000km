<div id="login-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" style="width: 402px; margin-left: -201px;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>登录一万公里</h3>
    </div>
    <div class="modal-body" style="max-height: 800px;">
        <div class="login-dlg">
            <div class="login-dlg-social">
                <h4>使用合作网站账号登录</h4>
                <div class="clearfix">
                    <a href="/login/weibo" class="weibo"></a>
                    <a href="/login/qq" class="qq"></a>
                    <a href="/login/douban" class="douban"></a>
                    <a href="/login/renren" class="renren"></a>
                </div>
            </div>

            <div style="text-align: center; margin: 0 40px;">
                <hr class="pull-left" style="width: 40%;">
                <span style="display: inline-block; margin-top: 10px;">或者</span>
                <hr class="pull-right" style="width: 40%;">
            </div>

            <div style="margin: 0 40px;">
                <form action="/login" class="form-horizontal" style="padding: 0 24px;" method="post" id="login-form">
                    <input type="hidden" name="<?php echo Yii::app()->request->csrfTokenName; ?>" value="<?php echo Yii::app()->request->csrfToken; ?>">
                    <div class="input-prepend" style="margin-bottom: 16px;">
                        <span class="add-on"><i class="icon-envelope"></i></span>
                        <input placeholder="Email" type="text" name="login_email">
                    </div>

                    <div class="input-prepend" style="margin-bottom: 16px;">
                        <span class="add-on"><i class="icon-lock"></i></span>
                        <input placeholder="密码" type="password" name="password">
                    </div>
                    <p style="color: red; display: none;" id="login-err-msg"></p>
                    <div class="clearfix">
                        <label class="checkbox pull-left"><input type="checkbox" name="rememberMe" value="1">记住密码</label>
                        <a href="/account/forget" class="pull-right">忘记密码？</a>
                    </div>
                    <div style="text-align: center; margin-top: 10px;">
                        <button class="btn btn-primary" id="login-btn">登 录</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('#login-btn').click(function() {
            $.post('/login', $('#login-form').serialize(), function(data) {
                if (data['code'] != 0) {
                    $('#login-err-msg').text(data['msg']).show();
                } else {
                    location.reload();
                }
            });
            return false;
        });
    });
</script>
