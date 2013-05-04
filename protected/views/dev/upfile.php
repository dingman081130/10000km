<form method="post" enctype="multipart/form-data">
    <?php echo Helpers::csrfInput(); ?>
    <input type="file" name="file" />
    <input type="submit" value="提交">
</form>