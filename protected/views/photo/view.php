<div class="row">
    <div class="span9">
       <?php
        $this->renderView(array('album','albumHeader'), array('album'=>$album, 'user'=>$user));
        ?>
    </div>
    <div class="span3">
        <div class="clearfix" style="margin: 36px 0 10px 0;">
        <!-- JiaThis Button BEGIN -->
        <div class="jiathis_style">
        <a class="jiathis_button_qzone"></a>
        <a class="jiathis_button_tsina"></a>
        <a class="jiathis_button_tqq"></a>
        <a class="jiathis_button_renren"></a>
        <a class="jiathis_button_kaixin001"></a>
        <a href="http://www.jiathis.com/share?uid=1736140" class="jiathis jiathis_txt jiathis_separator jtico jtico_jiathis" target="_blank"></a>
        <a class="jiathis_counter_style"></a>
        </div>
        <script type="text/javascript" >
        var jiathis_config={
                data_track_clickback:true,
                summary:"",
                ralateuid:{
                        "tsina":"3168359687"
                },
                appkey:{
                        "tsina":"174356130",
                        "tqq":"100353422"
                },
                hideMore:false
        }
        </script>
        <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js?uid=1736140" charset="utf-8"></script>
        <!-- JiaThis Button END -->

        </div>

        <?php
        $this->renderView(array('album', 'albumPreviewer'), array('album' => $album));
        ?>
        <?php if (Yii::app()->user->id == $album['user_id']) { ?>
            <ul class="photo-set-btns unstyled" style="margin-top: 20px;">
                <li class="set-cover pull-left"><a href="#"><i class="icon-picture"></i>&nbsp;设置封面&nbsp;&nbsp;</a></li>
                <li class="set-delete"><a href="#"><i class="icon-trash"></i>&nbsp;删除&nbsp;&nbsp;</a></li>
            </ul>
        <?php } ?>
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
    $(function() {
        $('.upload-photo-btn').click(function() {
            $('#upload-photo-modal').modal()
                .on('hidden', function() {
                    window.location.reload();
                });;
            return false;
        });
        AlbumPreviewer.resize($('.album-previewer'), 200, 30, 30);
        FileUpload.init($('#fileupload'));
        PhotoStory.boom();
        PhotoSetBtns.init($('.photo-set-btns'));
    });

</script>