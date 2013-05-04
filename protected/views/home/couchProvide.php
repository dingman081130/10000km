<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'couch_provide',
        ));
        ?>
    </div>

    <div class="span10">
        <h4>我的沙发</h4>
        <?php
        $available = $form['available'];
        ?>

        <form class="form-horizontal" action="/home/couchProvide" method="post">
            <div class="alert alert-success result-box <?php if (empty($msg)) echo 'hide'; ?>">
                <span><?php if (!empty($msg)) echo CHtml::encode($msg); ?></span>
            </div>
            <?php echo Helpers::csrfInput(); ?>
            <div class="control-group">
                <input type="hidden" name="available" value="0">
                <label class="checkbox"><input name="available" value="1" type="checkbox" class="couch-available-input" <?php if ($available) echo 'checked'; ?>>我可以提供沙发</label>
            </div>

            <div class="detail <?php if (!$available) echo 'hide'; ?>">
                <h5>沙发详细信息：</h5>

                <div class="control-group">
                    <label class="control-label" style="width: 80px;">可接纳人数:</label>
                    <div class="controls" style="margin-left: 100px;">
                        <select name="capacity" style="width: 70px;" class="capacity-select">
                            <option value="1" <?php if ($available && $form['capacity'] == 1) echo 'selected'; ?>>1</option>
                            <option value="2" <?php if ($available && $form['capacity'] == 2) echo 'selected'; ?>>2</option>
                            <option value="3" <?php if ($available && $form['capacity'] == 3) echo 'selected'; ?>>3</option>
                            <option value="4" <?php if ($available && $form['capacity'] == 4) echo 'selected'; ?>>4</option>
                            <option value="5" <?php if ($available && $form['capacity'] == 5) echo 'selected'; ?>>5</option>
                            <option value="6" <?php if ($available && $form['capacity'] == 6) echo 'selected'; ?>>6+</option>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" style="width: 80px;">沙发照片:</label>
                    <div class="controls" style="margin-left: 100px;">
                        <select name="album_id" class="album-select">
                            <option value=""></option>
                            <?php foreach ($albums as $album) { ?>
                                <option value="<?php echo $album['id']; ?>" <?php if ($form['album_id'] == $album['id']) echo 'selected'; ?>><?php echo CHtml::encode($album['name']); ?></option>
                            <?php } ?>
                        </select>
                        <span class="help-inline">选择一个相册来展示你的沙发</span>
                    </div>
                </div>

                <div class="control-group <?php if ($form->getError('description')) echo 'error'; ?>">
                    <label class="control-label" style="width: 80px;">沙发描述:</label>
                    <div class="controls" style="margin-left: 100px;">
                        <textarea name="description" class="description" style="width: 400px; height: 120px;"><?php echo CHtml::encode($form['description']); ?></textarea>
                        <span class="help-block"><?php echo CHtml::encode($form->getError('description')); ?></span>
                    </div>
                </div>

                <h5>对沙发客的条件：</h5>

                <div class="control-group">
                    <label class="control-label" style="width: 80px;">性别:</label>
                    <div class="controls" style="margin-left: 100px;">
                        <select name="guest_sex" style="width: 70px;" class="sex-select">
                            <option value="-1"  <?php if ($available && $form['guest_sex'] == -1) echo 'selected'; ?>>不限</option>
                            <option value="1" <?php if ($available && $form['guest_sex'] == 1) echo 'selected'; ?>>男</option>
                            <option value="0" <?php if ($available && $form['guest_sex'] == 0) echo 'selected'; ?>>女</option>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" style="width: 80px;">吸烟:</label>
                    <div class="controls" style="margin-left: 100px;">
                        <input type="hidden" name="no_smoke" value="0">
                        <label class="checkbox inline"><input type="checkbox" name="no_smoke" value="1" class="smoke-input" <?php if ($available && $form['no_smoke'] == 1) echo ' checked' ?>>不允许</label>
                    </div>
                </div>
            </div>

            <div class="control-group">
                <div class="controls" style="margin-left: 80px;">
                    <button type="submit" class="btn btn-primary update-couch">更新</button>
                </div>
            </div>
        </form>


    </div>

</div>

<script>
    $(function() {
        CouchManage.CouchProvide($('form'));
    });
</script>
