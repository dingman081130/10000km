<?php
    !isset($size) and $size = 90;
    $width = $size;
    $heigt = ($size * 3/5);
?>
<div class="clearfix">
    <img style="width: <?php echo $width?>px;height: <?php echo $heigt ?>px" src="/img/shafa.png">
    <span class="badge badge-success" style="position: relative;left: -20px;top: -10px;"><?php echo CHtml::encode($cnt) ?></span>
</div>