<div class="album-item">
    <div>
        <div>
            <a href="/album/<?php echo $album['id'] ?>">
                <img class="img-polaroid" src="<?php echo $album['cover_surl'] ?>">
            </a>
        </div>
    </div>
    <span class="label label-inverse">共<?php echo $album['photo_count'] ?>张</span>
    <p class="name">
        <b>
            <a href="/album/<?php echo $album['id'] ?>">
                <?php echo CHtml::encode(Utils::amputate($album['name'])); ?>
            </a>
        </b>
    </p>
    <p class="time muted"><small>更新于<?php echo $album['update_time'];?></small></p>
</div>