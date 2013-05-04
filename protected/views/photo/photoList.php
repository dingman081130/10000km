<?php
/**
 * @file class PhotoListWidget
 * @package application.components.widgets
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-1-6
 * @version
 */

//Yii::import('zii.widgets.CPortlet');
Yii::import('application.models.Album.Photo');
?>
<div class="photo-list clearfix">
<?php
foreach($pids as $pid){
    $p = Photo::getPhoto($pid);
    if(!$p){
        continue;
    }
    echo "<div class='photo-item' pid='{$pid}' ourl='{$p['ourl']}' surl='{$p['surl']}'>";
    echo "<img class='img-polaroid' src='{$p['surl']}'>";
    echo '</div>';
}
?>
</div>