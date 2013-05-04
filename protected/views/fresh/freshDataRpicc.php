<div class="fresh-header">
    <a href="/user/<?php echo $fresh['uid']; ?>"><?php echo $fresh['uname'] ?></a>&nbsp;在图片中回复了你
    <div class="pull-right">
        <span style="color: #339bb9;"><?php echo CHtml::encode($fresh['ftime']) ?></span>
    </div>
</div>
<div class="fresh-body">
    <div class="photo-reply">
        <?php
            $this->renderView(array('photo','photoStory'), array(
                'photo' => $fresh['content']['pid'],
                'mode'  => 'fresh'
                ));
        ?>
    </div>
</div>
<div class="clearfix"></div>
