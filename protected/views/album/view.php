<div class="row">
    <div class="span9">
        <?php
        $this->renderPartial('albumHeader', array('album'=>$album, 'user'=>$user));
        ?>

        <ul class="unstyled" style="margin-top: 10px;">
            <?php
            foreach ($photos as $photo) {
                ?>
                <li class="card-item">
                    <a href="<?php echo '/photo/' . $photo['album_id'] . '#' . $photo['id']; ?>">
                        <img class="img-polaroid" src="<?php echo CHtml::encode($photo['surl']); ?>">
                    </a>
                    <div>
                        <div class="clearfix">
                            <span class="pull-left muted"><small><?php echo Helpers::friendlyDate($photo['create_time']); ?></small></span>
                            <span class="muted pull-right">
                                <small>评论(<?php echo $photo['comment_count']; ?>)</small>
                            </span>
                        </div>
                        <div>
                            <span class="muted">
                                <a href="<?php echo '/photo/' . $photo['album_id'] . '#' . $photo['id']; ?>"><?php echo CHtml::encode(Utils::tripDescStriper($photo['title'], 20)); ?></a>
                            </span>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>

    <div class="span3">
        <div class="well" style="margin-top: 30px;">
            <h5>他的相册</h5>
            <ul class="unstyled">
                <?php foreach ($albums as $one) { ?>
                    <li class="clearfix" style="margin-bottom: 10px;">
                        <a href="/album/<?php echo $one['id']; ?>"><img style="float: left; width: 60px;" src="
                            <?php
                            if (isset($one['cover_surl']) && $one['cover_surl']) {
                                echo CHtml::encode($one['cover_surl']);
                            } else {
                                echo ImageUrlHelper::imgUrl(ImageUrlHelper::PHOTO_SMALL, 'no_photo.png');
                            }
                            ?>"></a>
                        <div style="margin-left: 70px;">
                            <p style="margin: 0;"><small><a href="/album/<?php echo $one['id']; ?>"><?php echo CHtml::encode($one['name']); ?></a></small></p>
                            <p class="muted"><small>共<?php echo $one['photo_count']; ?>张</small></p>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>

</div>

<div class="modal hide fade" id="upload-photo-modal" style="width: 900px; margin: -50px 0 0 -450px;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>上传照片到 <?php echo CHtml::encode($album['name']); ?></h3>
    </div>
    <div class="modal-body" style="max-height: 500px; min-height: 200px;">
        <form id="fileupload" action="/album/upload" method="POST" enctype="multipart/form-data">
            <?php echo Helpers::csrfInput(); ?>
            <input type="hidden" name="album_id" value="<?php echo $album['id']; ?>">
            <div class="row fileupload-buttonbar">
                <div class="span5">
                    <span class="btn btn-success fileinput-button">
                        <i class="icon-plus icon-white"></i>
                        <span>添加照片</span>
                        <input type="file" name="files[]" multiple="">
                    </span>
                    <button type="submit" class="btn btn-primary start">
                        <i class="icon-upload icon-white"></i>
                        <span>开始上传</span>
                    </button>
                    <button type="reset" class="btn btn-warning cancel">
                        <i class="icon-ban-circle icon-white"></i>
                        <span>全部取消</span>
                    </button>
                </div>
                <div class="span4 fileupload-progress fade">
                    <!-- The global progress bar -->
                    <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                        <div class="bar" style="width: 0%; "></div>
                    </div>
                    <!-- The extended global progress information -->
                    <div class="progress-extended">&nbsp;</div>
                </div>
            </div>
            <!-- The loading indicator is shown during file processing -->
            <div class="fileupload-loading"></div>
            <br>
            <!-- The table listing the files available for upload/download -->
            <table role="presentation" class="table table-striped" id="upload_files_container">
                <tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery">

                </tbody>
            </table>
        </form>
    </div>

</div>

<script src="/js/load-image.min.js"></script>
<script src="/js/jquery.fileupload.js"></script>
<script src="/js/jquery.fileupload-ui.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>

<script>
    $(function () {
        $('.upload-photo-btn').click(function() {
            $('#upload-photo-modal').modal()
                .on('hidden', function() {
                    window.location.reload();
                });
            return false;
        });
        FileUpload.init($('#fileupload'));
    });
</script>