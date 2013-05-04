<ul class="breadcrumb">
    <?php
    foreach ($this->crumbs as $crumb) {
        if (next($this->crumbs)) {
            ?>
            <li>
                <?php
                if (isset($crumb['url'])) {
                    echo CHtml::link(CHtml::encode($crumb['name']), $crumb['url']);
                    ?>
                    <span class="divider"><?php echo CHtml::encode($this->delimiter); ?></span>
                <?php
                } else {
                    echo CHtml::encode($crumb['name']);
                }
                ?>
            </li>
            <?php } else { ?>
            <li class="active">
                <?php echo CHtml::encode($crumb['name']); ?>
            <li>
    <?php }
} ?>
</ul>
