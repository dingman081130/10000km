<ul class="nav nav-tabs">
    <?php if ($active == 'home') { ?>
        <li class="active"><a href="/user/<?php echo $uid; ?>">他的主页</a></li>
    <?php } else { ?>
        <li><a href="/user/<?php echo $uid; ?>">他的主页</a></li>
    <?php } ?>
        
    <?php if ($active == 'profile') { ?>
        <li class="active"><a href="/user/<?php echo $uid; ?>/profile">个人资料</a></li>
    <?php } else { ?>
        <li><a href="/user/<?php echo $uid; ?>/profile">个人资料</a></li>
    <?php } ?>
        
    <?php if ($active == 'album') { ?>
        <li class="active"><a href="/user/<?php echo $uid; ?>/album">相册</a></li>
    <?php } else { ?>
        <li><a href="/user/<?php echo $uid; ?>/album">相册</a></li>
    <?php } ?>
        
    <?php if ($active == 'comment') { ?>
        <li class="active"><a href="/user/<?php echo $uid; ?>/comment">他的评价</a></li>
    <?php } else { ?>
        <li><a href="/user/<?php echo $uid; ?>/comment">他的评价</a></li>
    <?php } ?>
        
    <?php if ($active == 'couch') { ?>
        <li class="active"><a href="/user/<?php echo $uid; ?>/couch">他的沙发</a></li>
    <?php } else { ?>
        <li><a href="/user/<?php echo $uid; ?>/couch">他的沙发</a></li>
    <?php } ?>
</ul>