<?php
/**
 * @file class 照片查看器
 * @package application.components.widgets
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-1-5
 * @version
 */
?>
<div class="photo-publish">
<?php $this->renderView(array('photo', 'photoList'), array('pids' => $pids));?>
</div>