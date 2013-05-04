<div id="user-comment-modal" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>评价<?php echo CHtml::encode($user['name']); ?></h3>
    </div>
    <div class="modal-body">
        <form class="form-horizontal" style="margin-left: -30px;" action="/user/<?php echo $user['id']; ?>/comment" method="post">
            <?php echo Helpers::csrfInput(); ?>
            <div class="control-group">
                <label class="control-label">我们的经历:</label>
                <div class="controls">
                    我们一起旅行
                    <select class="span1" name="travel_days">
                        <option value="0"></option>
                        <?php
                        for ($i = 1; $i < 30; $i++) {
                            ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php } ?>
                        <option value="30">30+</option>
                    </select>天
                    <br/>
                    他在我这借宿
                    <select class="span1" name="surf_days">
                        <option value="0"></option>
                        <?php
                        for ($i = 1; $i < 30; $i++) {
                            ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php } ?>
                        <option value="30">30+</option>
                    </select>天
                    <br/>
                    我在他那借宿
                    <select class="span1" name="host_days">
                        <option value="0"></option>
                        <?php
                        for ($i = 1; $i < 30; $i++) {
                            ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php } ?>
                        <option value="30">30+</option>
                    </select>天
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">评价:</label>
                <div class="controls">
                    <select style="width: 70px;" name="opinion">
                        <option value="1">好评</option>
                        <option value="2">中评</option>
                        <option value="3">差评</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">评论:</label>
                <div class="controls">
                    <textarea style="width: 300px;" rows="6" name="content"></textarea>
                </div>a
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
        <button class="btn btn-primary user-comment-post-btn">评价</button>
    </div>
</div>