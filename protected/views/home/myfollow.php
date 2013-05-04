<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'myfollow',
        ));
        ?>
    </div>

    <div class="span10">
        <h4>我的关注</h4>

        <ul class="unstyled">
            <?php
            foreach ($follows as $follow) {
                ?>
                <li class="pull-left myfollow-item" style="border: 1px solid #CCC; padding: 10px; margin: 0 20px 20px 0; width: 180px; height: 80px;">
                    <div class="clearfix">
                        <div class="pull-left" style="width: 60px;">
                            <img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $follow['avatar']); ?>">
                        </div>
                        <div style="margin-left: 70px;">
                            <a href="/user/<?php echo $follow['id']; ?>"><?php echo CHtml::encode($follow['name']); ?></a>
                            <p><small><?php echo $follow['sex'] == 0 ? '女' : '男'; ?>, <?php echo Helpers::ageFromBirthday($follow['birthday']); ?>岁，<?php echo Helpers::cityName($follow['live_city_id']); ?></small></p>
                        </div>
                    </div>
                    <div>
                        <p class="description"><small>简介：<?php echo CHtml::encode(Helpers::substr($follow['description'], 10, true)); ?></small></p>
                        <p class="pull-right hide action"><button uid="<?php echo $follow['id']; ?>" class="btn btn-link btn-small message-btn" data-toggle="modal">私信</button><button uid="<?php echo $follow['id']; ?>" class="btn btn-link btn-small unfollow-user-btn">取消关注</button></p>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>

</div>

<script>
    $('li.myfollow-item').hover(function() {
        $(this).find('.description').hide();
        $(this).find('.action').show();
    }, function() {
        $(this).find('.description').show();
        $(this).find('.action').hide();
    });
    
    $('.unfollow-user-btn').click(function() {
        var uid = $(this).attr('uid');
        var this_item = $(this).closest('.myfollow-item');
        $.post('/user/unfollow', {
            uid : uid,
            csrf_token : $('meta[name=csrf_token_value]').attr('content')
        }, function(data) {
            if (data.code == 0) {
                this_item.remove();
            } else {
                alert(code.msg);
            }
        });
            
        return false;
    });
</script>
