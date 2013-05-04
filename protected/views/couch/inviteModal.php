<div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>
            向<?php echo CHtml::encode($user['name']); ?>提供沙发
        </h3>
    </div>
    <div class="modal-body">
        <div class="alert result-box hide"></div>
        <form class="form-horizontal couch-invite-form" style="margin-left: -30px;" action="/couch/invite" method="post">
            <?php echo Helpers::csrfInput(); ?>
            <input type="hidden" name="uid" value="<?php echo $user['id']; ?>">
            <div class="control-group">
                <label class="control-label">标题</label>
                <div class="controls">
                    <input type="text" name="title" value="来自<?php echo CHtml::encode(Helpers::cityName($host['live_city_id'], false)); ?>的沙发邀请">
                    <span class="help-inline"></span>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">入住日期</label>
                <div class="controls">
                    <input class="span2" type="text" name="arrive_date" autocomplete="off" value="<?php echo CHtml::encode($couch_search['arrive_date']); ?>">
                    <span class="help-inline"></span>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">离开日期</label>
                <div class="controls">
                    <input class="span2" type="text" name="leave_date" autocomplete="off" value="<?php echo CHtml::encode($couch_search['leave_date']); ?>">
                    <span class="help-inline"></span>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">可接纳人数</label>
                <div class="controls">
                    <select class="span1" name="number">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6+</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">给沙发客的附加信息</label>
                <div class="controls">
                    <textarea rows="5" name="content" style="width: 300px;"></textarea>
                    <span class="help-inline"></span>
                </div>
            </div>

        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary send-invite-btn">发送邀请</button>
    </div>
</div>
