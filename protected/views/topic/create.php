<div class="row">
    <div class="span8">
        <?php
        $this->widget('BreadCrumbWidget', array('crumbs' => array(
                array(
                    'name' => '小组',
                    'url' => array('/group'),
                ),
                array(
                    'name' => $group['name'],
                    'url' => array('/group/' . $group['id']),
                ),
                array(
                    'name' => '新建话题',
                )
                )));
        ?>

        <form class="form-horizontal" method="post" style="margin-left: -120px;">
            <?php echo Helpers::csrfInput(); ?>
            <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
            <div class="control-group <?php if ($form->getError('title')) echo 'error'; ?>">
                <label class="control-label">标题</label>
                <div class="controls">
                    <input class="span5" type="text" name="title" value="<?php echo CHtml::encode($form->title); ?>">
                    <span class="help-inline"><?php echo CHtml::encode($form->getError('title')); ?></span>
                </div>
            </div>

            <div class="control-group <?php if ($form->getError('content')) echo 'error'; ?>">
                <label class="control-label">内容</label>
                <div class="controls">
                    <textarea name="content" rows="5"><?php echo CHtml::encode($form->content); ?></textarea>
                    <span class="help-block"><?php echo CHtml::encode($form->getError('content')); ?></span>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">发表话题</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    $('form textarea[name="content"]').redactor({
        buttons: ['bold', 'italic', 'deleted', '|', 'orderedlist', '|', 'image', 'video', 'file', 'link', '|', 'horizontalrule'],
        imageUpload: '/topic/uploadImage',
        uploadFields: {csrf_token: $('meta[name="csrf_token_value"]').attr('content')},
        lang: 'zh_cn',
        imageGetJson: '/photo/photoList',
        minHeight: 240
    });
</script>
