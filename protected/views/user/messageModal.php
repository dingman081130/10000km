<div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">发送私信</h3>
    </div>
    <div class="modal-body">
        <div class="alert hide"></div>
        <form class="form-horizontal" style="margin-left: -70px;" method="post" action="/user/message">
            <?php echo Helpers::csrfInput(); ?>
            <input type="hidden" name="uid" value="<?php echo intval($user['id']); ?>">
            <div class="control-group">
                <label class="control-label">收信人:</label>
                <div class="controls">
                    <input type="text" value="<?php echo CHtml::encode($user['name']); ?>" disabled>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">私信内容:</label>
                <div class="controls">
                    <textarea name="content" rows="8" style="width: 380px;"></textarea>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
        <button class="btn btn-primary" id="message-post">发送</button>
    </div>
</div>