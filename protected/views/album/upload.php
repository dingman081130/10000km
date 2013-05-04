<div class="row">
    <form id="fileupload" action="/album/upload" method="POST" enctype="multipart/form-data" class="">
        <?php echo Helpers::csrfInput(); ?>
        <div class="row fileupload-buttonbar">
            <div class="span7">
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
            <div class="span5 fileupload-progress fade">
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


<script src="/js/load-image.min.js"></script>
<script src="/js/jquery.fileupload.js"></script>
<script src="/js/jquery.fileupload-ui.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>

<script>
    $(function () {
        $('#fileupload').fileupload({
            dataType: 'json',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('<p/>').text(file.name).appendTo(document.body);
                });
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .bar').css('width', progress + '%');
            },
            filesContainer: $('#upload_files_container'),
            uploadTemplateId: null,
            uploadTemplate: function (o) {
                var rows = $();
                $.each(o.files, function (index, file) {
                    var row = $('<tr class="template-upload fade">' +
                        '<td class="preview"><span class="fade"></span></td>' +
                        '<td class="name"></td>' +
                        '<td class="size"></td>' +
                        (file.error ? '<td class="error" colspan="2"></td>' :
                        '<td><div class="progress">' +
                        '<div class="bar" style="width:0%;"></div></div></td>' +
                        '<td class="start"><button class="btn btn-primary"><i class="icon-upload icon-white"></i><span> 上传</span></button></td>'
                ) + '<td class="cancel"><button class="btn btn-warning"><i class="icon-ban-circle icon-white"></i><span> 取消</span></button></td></tr>');
                    row.find('.name').text(file.name);
                    row.find('.size').text(o.formatFileSize(file.size));
                    if (file.error) {
                        row.find('.error').text(
                        locale.fileupload.errors[file.error] || file.error
                    );
                    }
                    rows = rows.add(row);
                });
                return rows;
            }
        });
    });
</script>