<div class="pagination pagination-centered">
    <ul>
        <?php
        $page_cur = $pagination['cur'];
        $page_cnt = $pagination['page_cnt'];

        $page_start = $page_cur - 2;
        if ($page_start <= 0)
            $page_start = 1;

        $page_end = $page_start + 4;
        if ($page_end > $page_cnt)
            $page_end = $page_cnt;
        
        if (strpos($base_url, '?') === false) {
            $base_url .= '?';
        }
        ?>

        <li class="<?php if ($page_cur == 1) echo 'disabled'; ?>">
            <a href="<?php echo CHtml::encode($base_url.'&page='.($page_cur-1 > 0 ? $page_cur -1 : 1));?>">«</a>
        </li>
        <?php
        for ($i = $page_start; $i <= $page_end; $i++) {
        ?>
            <li<?php if ($i == $page_cur) echo ' class="active"'; ?>>
                <a href="<?php echo CHtml::encode("$base_url&page=$i");?>"><?php echo $i; ?></a>
            </li>
        <?php } ?>
        <li class="<?php if ($page_cur == $page_end) echo 'disabled'; ?>">
            <a href="<?php echo CHtml::encode($base_url.'&page='.(($page_cur + 1 <= $page_end) ? $page_cur + 1 : $page_end));?>">»</a>
        </li>
    </ul>
</div>