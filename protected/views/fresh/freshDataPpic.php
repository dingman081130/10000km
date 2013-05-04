<?php
    Yii::import('application.models.Album.Photo');
    Yii::import('application.models.Album.Album');
    
    $con    = $fresh['content'];
    $uid    = $fresh['uid'];
    $uname  = $fresh['uname'];
    $avatar = $fresh['avatar'];
    
?>
<div class="fresh-header">
    <a href="/user/<?php echo $uid; ?>"><?php echo CHtml::encode($uname);?></a>&nbsp;上传了
    <?php
        if(isset($con['pcnt']) && isset($con['album_id'])){
            $aid = $con['album_id'];
            $a   = Album::getAlbum($aid);
            echo "{$con['pcnt']}张照片至相册<a href='/album/{$aid}'> ".CHtml::encode($a['name'])."</a>";
        }
    ?>
</div>
<div class="fresh-body">
    <?php
        $this->renderView(array('photo','photoPublish'), array('pids'=>$con['photo']));
    ?>
</div>

<div class="fresh-footer">
    <div class="pull-left">
        <span><small class="muted"><?php echo CHtml::encode($fresh['ftime']) ?></small></span>
    </div>
</div>