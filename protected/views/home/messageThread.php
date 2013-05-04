<?php
Yii::import('application.models.User.User');
?>
<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'message',
        ));
        ?>
    </div>

    <div class="span8">
        <div class="message-box" uid="<?php echo $sender['id']; ?>">
            <div class="alert alert-info">
                <?php if($sender['id'] == User::getSysId()){ ?>    
                    <h4>来自一万公里旅行网的消息~</h4>
                <?php } else { ?>
                    <h4>和<?php echo CHtml::encode($sender['name']); ?>的对话~</h4>
                <?php }?>
            </div>
            <ul class="unstyled message-list">
                <?php foreach ($message['list'] as $msg) {
                    $this->renderPartial('messageItem', array(
                        'msg' => $msg,
                    ));
                } ?>
            </ul>

            <?php
            $this->widget('PaginationWidget', array(
                'pagination' => $message['page'],
                'base_url' => '/home/message/' . $sender['id']
            ));
            ?>

            <?php if ( isset($sender['id']) && $sender['id'] && $sender['id'] != User::getSysId()) { ?>
                <div style="margin-left: 0px;">
                    <h5>回复：</h5>
                    <div><textarea id="msg" style="width: 400px;"></textarea></div>
                    <div><button id="pubMsg" class="btn btn-primary">发表回复</button></div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('#pubMsg').click(function(){
            var recipient = $('.message-box').attr('uid');
            var msg = $.trim($('#msg').val());
            if(!msg){
                alert("请输入回复消息");
                return false;
            }
            $.post('/home/pubMsg', {
                'recipient':recipient,
                'msg':msg,
                csrf_token: '<?php echo Yii::app()->request->csrfToken; ?>'
            }, function(data){
                if(!data.success){
                    alert(data.msg);
                }else{
                    $(data.data.code).prependTo($('.message-list'));
                    $('#msg').val('');
                    $('body').animate({scrollTop:0}, 'fast');
                }
            }, 'json');
        });
    });
</script>