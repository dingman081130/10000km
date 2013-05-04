<div>
    <form class="reset-pwd-form" method="post">
        <fieldset>
            <?php echo Helpers::csrfInput(); ?>
            <legend>重置密码</legend>
            <label>请输入新密码</label>
            <div class="input-prepend" style="margin-bottom: 16px;">
                <span class="add-on"><i class="icon-lock"></i></span>
                <input placeholder="密码" type="password" name="password">
            </div>
            
            <label>再次输入密码</label>
            <div class="input-prepend" style="margin-bottom: 16px;">
                <span class="add-on"><i class="icon-lock"></i></span>
                <input placeholder="再输入密码" type="password" name="repassword">
            </div>
            <span class="help-block result"></span>
            <button type="submit" class="btn btn-primary">重置</button>
        </fieldset>
    </form>
</div>

<script>
    $(function()  {
        $('.reset-pwd-form').ajaxForm({
            complete: function(xhr) {
                var resp = xhr.responseText;
                var data = $.parseJSON(resp);
                $('.reset-pwd-form .result').text(data.msg);
            }
        });
        
        $('.reset-pwd-form button[type="submit"]').click(function() {
            var pwd1 = $('.reset-pwd-form input[name="password"]').val();
            var pwd2 = $('.reset-pwd-form input[name="repassword"]').val();
            if (pwd1.length < 6) {
                $('.reset-pwd-form .result').text('密码长度至少为6');
                return false;
            }
            if (pwd1 != pwd2) {
                $('.reset-pwd-form .result').text('两次输入的密码不一样，请重新输入');
                return false;
            }
            return true;
        });
    });
    
</script>
    