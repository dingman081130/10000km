<?php
/**
 * @file class 渲染新鲜事列表
 * @package application.components.widgets
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-14
 * @version
 */
?>
<ul class="unstyled fresh-list">
    <?php foreach($freshes as $fresh){
        $this->renderView(array('fresh','freshItem'), array('fresh'=> $fresh));
    }?>
</ul>
