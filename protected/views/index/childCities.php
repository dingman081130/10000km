<ul class="unstyled">
<?php foreach ($child_cities as $city) {?>
    <li style="display: block; float: left; margin: 0 10px;">
        <a style="white-space:nowrap;" href="/place/<?php echo $city['pinyin']; ?>"><?php echo $city['name']; ?></a>
    </li>
<?php } ?>
</ul>