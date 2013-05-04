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
            <li class="active"><a href="/home/profile">基本资料</a></li>
            <li><a href="/home/socialAccount">账号绑定</a></li>
            <li><a href="/home/password">修改密码</a></li>
        </ul>


        <div>
            <form action="/home/profile" method="post" class="form-horizontal user-profile-form" style="margin-left: -80px; width: 500px; float: left;">
                <?php echo Helpers::csrfInput(); ?>
                <div class="control-group">
                    <label class="control-label" for="name-input">登录名:</label>
                    <div class="controls">
                        <label style="padding-top: 5px;"><?php echo CHtml::encode($user['login_email']); ?></label>
                    </div>
                </div>

                <div class="control-group <?php if ($form->getError('name')) echo 'error'; ?>">
                    <label class="control-label" for="name-input"><em style="color: red;">* </em>昵称:</label>
                    <div class="controls">
                        <input type="text" id="name-input" name="name" autocomplete="off" value="<?php echo CHtml::encode($form['name']); ?>">
                        <span class="help-inline"><?php if ($form->getError('name')) echo $form->getError('name'); ?></span>
                    </div>
                </div>

                <div class="control-group <?php if ($form->getError('sex')) echo 'error'; ?>">
                    <label class="control-label"><em style="color: red;">* </em>性别:</label>
                    <div class="controls">
                        <label class="radio inline"><input type="radio" name="sex" value="1" <?php if ($form['sex'] == 1) echo 'checked'; ?>>男</label>
                        <label class="radio inline"><input type="radio" name="sex" value="0" <?php if ($form['sex'] == 0) echo 'checked'; ?>>女</label>
                        <span class="help-inline"><?php if ($form->getError('sex')) echo $form->getError('sex'); ?></span>
                    </div>
                </div>

                <div class="control-group <?php if ($form->getError('birthday')) echo 'error'; ?>">
                    <label class="control-label"><em style="color: red;">* </em>生日:</label>
                    <div class="controls ">
                        <?php
                        $birthday = $form['birthday'];
                        if ($birthday && $birthday != '0000-00-00') {
                            $date_parts = getdate(strtotime($birthday));
                            $year = $date_parts['year'];
                            $month = $date_parts['mon'];
                            $day = $date_parts['mday'];
                        } else {
                            $year = 1990;
                            $month = 1;
                            $day = 1;
                        }
                        ?>
                        <input type="hidden" name="birthday" value="<?php echo "$year-$month-$day"; ?>" id="birthday-input">
                        <select style="width: 70px;" id="birthyear-select">
                            <?php
                            for ($i = 2012; $i >= 1950; $i--) {
                                ?>
                                <option value="<?php echo $i; ?>" <?php if ($i == $year) echo 'selected' ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>年 
                        <select style="width: 55px;" id="birthmonth-select">
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                ?>
                                <option value="<?php echo $i; ?>" <?php if ($i == $month) echo 'selected' ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>月 
                        <select style="width: 55px;" id="birthday-select">
                            <?php
                            for ($i = 1; $i <= 31; $i++) {
                                ?>
                                <option value="<?php echo $i; ?>" <?php if ($i == $day) echo 'selected' ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>日
                        <span class="help-inline"><?php if ($form->getError('birth_day')) echo $form->getError('birth_day'); ?></span>
                    </div>
                </div>

                <div class="control-group <?php if ($form->getError('live_city_id')) echo 'error'; ?>">
                    <label class="control-label"><em style="color: red;">* </em>居住城市:</label>
                    <div class="controls">
                        <select class="span2" id="city1">
                            <?php
                            if (!$live_city) {
                                // $live_city为空的时候，插入一个空的option。
                                ?>
                                <option value=""></option>
                            <?php } ?>
                            <?php
                            $up_city_id = $live_city && $live_city['up_city'] ? $live_city['up_city']['id'] : $live_city['id'];
                            foreach ($provinces as $province) {
                                ?>
                                <option value="<?php echo intval($province['id']); ?>" <?php if ($province['id'] == $up_city_id) echo 'selected'; ?>><?php echo CHtml::encode($province['name']); ?></option>
                            <?php } ?>
                        </select>
                        <select class="span2" id="city2" name="live_city_id">
                            <?php
                            if ($cities) {
                                foreach ($cities as $city) {
                                    ?>
                                    <option value="<?php echo intval($city['id']); ?>" <?php if ($live_city['id'] == $city['id']) echo 'selected'; ?>><?php echo CHtml::encode($city['name']); ?></option>
                                <?php }
                            }
                            ?>
                        </select>
                        <span class="help-inline"><?php if ($form->getError('live_city_id')) echo $form->getError('live_city_id'); ?></span>
                    </div>
                </div>

                <div class="control-group <?php if ($form->getError('occupation')) echo 'error'; ?>">
                    <label class="control-label">职业:</label>
                    <div class="controls">
                        <input type="text" name="occupation" value="<?php echo CHtml::encode($form['occupation']); ?>">
                        <span class="help-inline"><?php if ($form->getError('occupation')) echo $form->getError('occupation'); ?></span>
                    </div>
                </div>

                <div class="control-group <?php if ($form->getError('education')) echo 'error'; ?>">
                    <label class="control-label">学历:</label>
                    <div class="controls">
                        <input type="text" name="education" value="<?php echo CHtml::encode($form['education']); ?>">
                        <span class="help-inline"><?php if ($form->getError('education')) echo $form->getError('education'); ?></span>
                    </div>
                </div>

                <div class="control-group <?php if ($form->getError('website')) echo 'error'; ?>">
                    <label class="control-label">博客:</label>
                    <div class="controls">
                        <input type="text" name="website" value="<?php echo CHtml::encode($form['website']); ?>">
                        <span class="help-inline"><?php if ($form->getError('website')) echo $form->getError('website'); ?></span>
                    </div>
                </div>

                <div class="control-group <?php if ($form->getError('email')) echo 'error'; ?>">
                    <label class="control-label">邮箱:</label>
                    <div class="controls">
                        <input type="text" name="email" value="<?php echo CHtml::encode($form['email']); ?>">
                        <span class="help-inline"><?php if ($form->getError('email')) echo $form->getError('email'); ?></span>
                    </div>
                </div>

                <div class="control-group <?php if ($form->getError('qq')) echo 'error'; ?>">
                    <label class="control-label">QQ:</label>
                    <div class="controls">
                        <input type="text" name="qq" value="<?php echo CHtml::encode($form['qq']); ?>">
                        <span class="help-inline"><?php if ($form->getError('qq')) echo $form->getError('qq'); ?></span>
                    </div>
                </div>

                <div class="control-group <?php if ($form->getError('description')) echo 'error'; ?>">
                    <label class="control-label">个人简介:</label>
                    <div class="controls">
                        <textarea name="description" class="span5" style="height: 100px;"><?php echo CHtml::encode($form['description']); ?></textarea>
                        <span class="help-inline"><?php if ($form->getError('description')) echo $form->getError('description'); ?></span>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <button type="submit" class="btn btn-primary">保存</button>
                    </div>
                </div>
            </form>

            <form id="avatar-form" style="float: left; margin-left: 100px;" action="/home/avatar" method="post">
                <?php echo Helpers::csrfInput(); ?>
                <div class="thumbnail">
                    <img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_LARGE, Yii::app()->user->attrs['avatar']); ?>">
                </div>
                <div style="margin: 10px 0 0 50px;">
                    <span class="btn btn-success fileinput-button">
                        <span>修改头像</span>
                        <input type="file" name="avatar" id="avatar-input">
                    </span>
                </div>
            </form>
        </div>


    </div>

</div>


<script>
    $(function() {
        
        $('.user-profile-form input').focus(function() {
            $(this).closest('.control-group').removeClass('error')
                .find('span.help-inline').text('');
        });
        
        $('#city1').change(function() {
            var id = $(this).val();
            if (id == '') {
                $('#city2 > option').remove();
                return;
            }
            $.get('/api/getChildCities', {id: id}, function(data) {
                $('#city2 > option').remove();
                if (data.length == 0) {
                    $('#city2').append($('<option></option>').val($('#city1').val()).text($('#city1 option:selected').text()));
                    $('#city2 option:eq(0)').attr('selected', true);
                    return;
                }
                
                for (var i = 0; i < data.length; i++) {
                    $('#city2').append($('<option></option>').val(data[i].id).text(data[i].name));
                }
            });
        });
        
        $('#birthday-select, #birthmonth-select, #birthyear-select').change(function() {
            $('#birthday-input').val($('#birthyear-select').val() + '-' + $('#birthmonth-select').val() + '-' + $('#birthday-select').val());
        });
        
        $('#avatar-input').change(function(){
            if (this.files[0].size > 2*1024*1024) {
                alert('文件太大了啊！');
                return;
            }
            $('#avatar-form').ajaxForm({
                complete: function(xhr) {
                    var data = $.parseJSON(xhr.responseText);
                    if (data.code == 0) {
                        $('#avatar-form img').attr('src', data.url);
                    } else {
                        alert(data.msg);
                    }
                }
            }).submit();
            
        });
        
        $('.user-profile-form input[name="name"]').blur(function() {
            var name = $(this).val();
            var that = $(this);
            $.get('/account/nameAvailable', {name: name}, function(data) {
                if (data.code != 0) {
                    that.closest('.control-group').addClass('error')
                        .find('span.help-inline')
                        .text(data.msg);
                }
            });
        });
        
    });
            
</script>