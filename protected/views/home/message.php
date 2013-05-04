<?php
Yii::import('application.models.User.User');
$sysid = User::getSysId();
?>

<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'message',
        ));
        ?>
    </div>

    <div class="span10">
        <ul class="nav nav-tabs" id="myTab" style="margin-bottom: 10px;">
            <li class="active"><a href="#tab1" data-toggle="tab">私信列表</a></li>
        </ul>
        
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <div>
                    <ul class="unstyled inline">
                        <li class="pull-left" style="margin-right: 15px; margin-left: 5px;"><label class="checkbox inline" style="padding-top: 0;"><input id="selectall" type="checkbox">全选</label></li>
                        <li class="pull-left" style="margin-right: 15px;"><button id="markread" class="btn btn-info btn-small">标记为已读</button></li>
                    </ul>
                </div>

                <div class="clearfix">
                <table class="table table-condensed table-hover">

                    <thead>
                        <tr>
                            <th style="width: 15px;"></th>
                            <th style="width: 80px;"></th>
                            <th style="width: 40px"></th>
                            <th style="width: 300px"></th>
                            <th style="width: 100px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($message as $sender_id => $one) {?>
                            <tr style="cursor: pointer" uid="<?php echo $one['sender'] ?>">
                                <td><input class="select" type="checkbox" name="<?php echo $sender_id ?>"></td>
                                <td class="message-sender">
                                    <span class="muted"><?php echo CHtml::encode($one['sender_user']['name'])?></span>
                                </td>
                                <?php if($one['unread']){ ?>
                                    <td class="message-cnt">
                                        <strong><?php echo $one['unread'] ?></strong>/<?php echo $one['cnt'] ?>
                                    </td>
                                    <td class="message-content">
                                        <strong>
                                        <?php echo strip_tags($one['content']);?>
                                        </strong>
                                    </td>
                                <?php }else{ ?>
                                    <td class="message-cnt">
                                        <?php echo $one['unread'] ?>/<?php echo $one['cnt'] ?>
                                    </td>
                                    <td class="message-content">
                                        <?php echo strip_tags($one['content']);?>
                                    </td>
                                <?php } ?>
                                    <td class="message-time" style="text-align: right;"><span class="muted"><?php echo Helpers::friendlyTime($one['send_time']); ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        //// 全选联动
        $('#selectall').click(function(){
            if($(this).attr("checked") == 'checked'){
                $('.select').each(function(){
                    $(this).attr('checked', 'cheked');
                });
            }else{
                $('.select').each(function(){
                    $(this).attr('checked', false);
                });
            }
        });
        //// 点击标记为已读时
        $('#markread').click(function(){
            var id_list = '0';
            $('.select').each(function(){
                if($(this).attr('checked') == 'checked'){
                    id_list += ','+ $(this).attr('name');
                }
            });
            $.post(
                '/home/marksessionread',{
                    'id':id_list,
                    <?php echo Yii::app()->request->csrfTokenName ?>:'<?php echo Yii::app()->request->csrfToken ?>'
                }, 
                function(data){
                    alert(data.msg);
                    if(data.success == true){
                        window.location.href = '/home/message';
                    }
                },
                'json'
            );
        });
        $('.message-sender, .message-cnt, .message-content, .message-time').click(function(){
            var uid = $(this).parent().attr('uid');
            window.location.href = '/home/message/' + uid;
        });
    });
</script>