<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'album',
        ));
        ?>
    </div>

    <div class="span10">
        <div class="clearfix">
            <div class="pull-left">
                <h4>我的相册</h4>
            </div>
            <div class="pull-left" style="margin-left: 10px;">
                <button class="btn btn-small btn-info" data-toggle="modal" data-target="#new-album-dlg" style="margin-top: 7px;">新建相册</button>
            </div>
        </div>
        <ul class="unstyled album-list">
            <?php foreach ($albums as $one) {?>
                <li style="float: left; text-align: center; position: relative;  height: 200px; width: 160px; margin: 0 20px 20px 0;">
                    <a href="/album/<?php echo $one['id']; ?>" title="<?php echo CHtml::encode($one['name']); ?>">
                        <div class="thumbnail">
                            <div style="overflow: hidden;" >
                            <img style="width: 150px; height: 150px;" src="<?php echo CHtml::encode($one['cover_surl']); ?>">
                            </div>
                        </div>
                        <span class="label label-inverse" style="position: absolute; right: 10px; top: 130px; opacity: 0.8;">共<?php echo $one['photo_count']; ?>张</span>
                        <p style="margin-bottom: 0;"><b><?php echo CHtml::encode(Helpers::substr($one['name'], 10, true)); ?></b></p>
                    </a>
                    <p class="muted"><small>更新于<?php echo Helpers::friendlyTime($one['update_time']); ?></small></p>
                </li>
            <?php } ?>
        </ul>
    </div>

</div>

<div class="modal hide fade" id="new-album-dlg">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>新建相册</h3>
    </div>
    <div class="modal-body">
        <form class="form-horizontal" method="post" action="/album/create">
            <?php echo Helpers::csrfInput(); ?>
            <div class="control-group">
                <label class="control-label">相册名称</label>
                <div class="controls">
                    <input type="text" name="name" autocomplete="off">
                    <span class="help-inline"></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">相册描述</label>
                <div class="controls">
                    <textarea name="description"></textarea>
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">创建</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function() {
        Album.CreateAlbumDlg($('#new-album-dlg'));
    });
</script>
