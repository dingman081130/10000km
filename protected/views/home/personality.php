<?php
Yii::import('application.models.User.User');
$user = User::getUser(Yii::app()->user->id);
?>
<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'personality',
        ));
        ?>
    </div>

    <div class="span10">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="/home/personality">个性设置</a></li>
        </ul>

        <div>
            <h4>最近想去的地方</h4>
            <input class="want-place-input" t="want_places" value="<?php echo CHtml::encode($user['want_places']); ?>">
            <button class="btn btn-primary save-want-places" style="margin: 5px 0 10px 360px;">保存</button>
        </div>

        <div>
            <h4>个性标签</h4>
            <input class="personal-tag-input" t="personal_tags" value="<?php echo CHtml::encode($user['personal_tags']); ?>">
            <button class="btn btn-primary save-personal-tags" style="margin: 5px 0 10px 360px;">保存</button>
        </div>


    </div>

</div>

<script>
    $(function() {
        $('.want-place-input, .personal-tag-input').tagsInput({
            height: '26px',
            width: '400px',
            defaultText: '点击添加'
        });
        
        $('.save-want-places, .save-personal-tags').click(function() {
            $.post('/home/personality', {
                type: $(this).prevAll('input').attr('t'),
                value: $(this).prevAll('input').val(),
                csrf_token: $('meta[name=csrf_token_value]').attr('content')
            });
        })
    });
</script>
