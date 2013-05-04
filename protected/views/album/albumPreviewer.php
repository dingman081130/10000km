<?php
$photos = Album::getAlbumPhotos($album['id']);
foreach ($photos as $photo) {
    $photo['surl'] = ImageUrlHelper::imgUrl(ImageUrlHelper::PHOTO_SMALL, $photo['img']);
}
?>

<ul class="unstyled album-previewer clearfix" aid="<?php echo CHtml::encode($album['id']); ?>">
<?php
foreach ($photos as $photo) {
    ?>
        <li class="photo-clip" pid="<?php echo $photo['id'] ?>">
            <div>
                <img style="width: 56px; height: 56px;" src="<?php echo CHtml::encode($photo['surl']); ?>">
            </div>
        </li>
    <?php
}
?>
</ul>
