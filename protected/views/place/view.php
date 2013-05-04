<?php
Yii::import('application.models.User.User');
//print_r($users);exit;
?>
<div class="row">
    <div class="span8">
        <h4><?php if ($type == 'local') echo $place['name'].'的当地人'; if ($type == 'surfer') echo '在此旅行的沙发客'; ?> <span class="muted">| </span><a href="/search/in/city,<?php echo urlencode($place['id']); ?>,<?php echo $type ?>">查看更多</a></h4>
        <?php
        foreach ($users as $auser) {
            ?>
            <div class="clearfix pull-left" style="margin: 0 10px 10px 0; width: 195px; height: 100px;">
                <div style="width: 100px; float: left;">
                    <a href="/user/<?php echo $auser['id']; ?>" class="thumbnail"><img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_MIDDLE, $auser['avatar']); ?>"></a>
                </div>
                <div style="float: left; padding-left: 5px;">
                    <p><a title="<?php echo CHtml::encode($auser['name']); ?>" href="/user/<?php echo $auser['id']; ?>"><?php echo CHtml::encode(Helpers::substr($auser['name'], 5)); ?></a></p>
                    <p class="muted" style="margin: 0;"><?php echo Helpers::cityName($auser['live_city_id']); ?></p>
                    <p class="muted" style="margin: 0;"><?php echo $auser['sex'] == 0 ? '女' : '男'; ?>，<?php echo Helpers::ageFromBirthday($auser['birthday']); ?>岁</p>
                    <?php if (isset($auser['couch'])) { ?>
                    <a href="/user/<?php echo $auser['id'] ?>/couch">
                        <?php $this->renderView(array('couch', 'couchItem'), array('size' => 60, 'cnt' => $auser['couch']['capacity'])); ?>
                    </a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="span4">
        <div id="map" style="border: 1px solid #CCC;width: 100%; height: 240px;"></div>
    </div>
</div>

<div class="row" style="margin-top: 20px;">
    <div class="span7">
        <h4><?php echo CHtml::encode($place['name']); ?>的热门话题</h4>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 300px;">话题</th>
                    <th>作者</th>
                    <th>回复</th>
                    <th>最后回复</th>
                </tr>
            </thead>
            
            <tbody>
                <?php
                foreach ($topics as $topic) {
                    $author = User::getUser($topic['author_id']);
                ?>
                <tr>
                    <td><a href="/topic/<?php echo $topic['id']; ?>" title="<?php echo CHtml::encode($topic['title']); ?>"><?php echo CHtml::encode(Helpers::substr($topic['title'], 20)); ?></a></td>
                    <td><a href="/user/<?php echo $author['id']; ?>"><?php echo CHtml::encode($author['name']);?></a></td>
                    <td><?php echo $topic['reply_count'] ?></td>
                    <td><?php echo Helpers::friendlyTime($topic['create_time'], false); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <div class="span5">
        <h4><?php echo CHtml::encode($place['name']); ?>相关的小组</h4>
        <ul class="unstyled inline">
            <?php foreach ($groups as $group) { ?>
            <li style="margin-bottom: 10px; width: 175px; overflow: hidden; white-space:nowrap;" title="<?php echo CHtml::encode($group['name']);?>">
                <a href="#"><img class="pull-left" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::GROUP_IMAGE, $group['image']); ?>"></a>
                <div style="margin-left: 70px;">
                    <p><a href="/group/<?php echo $group['id']; ?>"><?php echo CHtml::encode($group['name']); ?></a></p>
                    <p>成员：<?php echo $group['user_count']; ?></p>
                </div>
            </li>
            <?php } ?>
        </ul>
    </div>
</div>

<script>
    var place_name = <?php echo json_encode($place['name']); ?>;
    function initialize() {
        var mp = new BMap.Map('map');
        mp.centerAndZoom(place_name);
        mp.addControl(new BMap.NavigationControl({type: BMAP_NAVIGATION_CONTROL_ZOOM}));
    }
 
    function loadScript() {
        var script = document.createElement("script");
        script.src = "http://api.map.baidu.com/api?v=1.4&callback=initialize";
        document.body.appendChild(script);
    }
 
    $(function() {
        loadScript();
    });

</script>