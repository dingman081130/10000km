<div>
    <form class="forget-pwd-form" method="post">
        <fieldset>
            <?php echo Helpers::csrfInput(); ?>
            <legend>找回密码</legend>
            <label>请输入您的注册邮箱</label>
            <div class="input-prepend" style="margin-bottom: 16px;">
                <span class="add-on"><i class="icon-envelope"></i></span>
                <input placeholder="Email" type="email" name="login_email">
            </div>
            <span class="help-block result"></span>
            <button type="submit" class="btn btn-primary">重置</button>
        </fieldset>
    </form>
</div>

<script>
    $(function() {
        $('.forget-pwd-form').ajaxForm({
            complete: function(xhr) {
                var resp = xhr.responseText;
                var data = $.parseJSON(resp);
                $('.forget-pwd-form .result').text(data.msg);
                $('.forget-pwd-form button').attr('disabled', null);
            }
        });
        
        $('.forget-pwd-form').submit(function(){
            $(this).find('button').attr('disabled', true);
        });
    });
</script>