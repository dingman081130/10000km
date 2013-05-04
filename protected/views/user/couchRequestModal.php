<div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">申请沙发</h3>
    </div>
    <div class="modal-body">
        <div class="alert hide"></div>
        <form class="form-horizontal" style="margin-left: -70px;" method="post" action="/user/couchRequest">
            <?php echo Helpers::csrfInput(); ?>
            <input type="hidden" name="uid" value="<?php echo intval($uid); ?>">
            <div class="control-group">
                <label class="control-label">标题:</label>
                <div class="controls">
                    <input type="text" class="span5" name="title">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">附加信息:</label>
                <div class="controls">
                    <textarea name="content" class="span5" rows="5"></textarea>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">到达时间:</label>
                <div class="controls date">
                    <input type="text" class="input-small pull-left" name="arrive_date"> 
                    <div style="margin: -160px 0 0 120px;"></div>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">离开时间:</label>
                <div class="controls date">
                    <input type="text" class="input-small pull-left" name="leave_date">
                    <div style="margin: -180px 0 0 120px;"></div>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">沙发数:</label>
                <div class="controls">
                    <select class="span1" name="couch_number">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6+</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
        <button class="btn btn-primary" id="request-couch-post">申请</button>
    </div>
</div>