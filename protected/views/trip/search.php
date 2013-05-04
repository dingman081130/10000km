<div class="row">
    <div class="span8">
        <h3>你要去哪里？</h3>
        <form class="form-horizontal trip-search-form" method="get" action="/trip/search">
            <div style="margin-left: -100px;">
                <div class="control-group">
                    <label class="control-label">目的地</label>
                    <div class="controls">
                        <input type="text" id="dest-input" name="dsts">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">时间</label>
                    <div class="controls" style="position: relative;">
                        <input autocomplete="off" type="text" class="input-small startdate-input" placeholder="任意" name="start_date" value="<?php echo CHtml::encode($start_date); ?>"> - 
                        <input autocomplete="off" type="text" class="input-small enddate-input" placeholder="任意" name="end_date" value="<?php echo CHtml::encode($end_date); ?>">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">旅行类型</label>
                    <div class="controls">
                        <ul class="unstyled trip-category">
                            <?php
                            foreach ($all_ways as $way) {
                                ?>
                                <li><label style="width: 75px;" class="checkbox inline"><input type="checkbox" name="trip_way[]" value="<?php echo CHtml::encode($way['id']); ?>" <?php if (in_array($way['id'], $trip_way)) echo 'checked'; ?>><?php echo CHtml::encode($way['name']); ?></label></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="clearfix" style="text-align: center;">
                <button class="btn btn-primary">查找旅行</button>
            </div>
        </form>


        <div class="">
            <ul class="unstyled trip-list">

                <?php
                foreach ($trips as $trip) {
                    $this->renderPartial('tripItem', array('trip' => $trip));
                }
                ?>
            </ul>
        </div>


        <?php
        $page_total = intval($total / $page_size) + ($total % $page_size == 0 ? 0 : 1);

        $params = array(
            'dsts' => $trip_way,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'dsts' => join(',', $dsts),
            'trip_way' => $trip_way,
        );
        $base_url = Yii::app()->createUrl('trip/search', $params);
        $this->widget('PaginationWidget', array(
            'base_url' => $base_url,
            'pagination' => array(
                'page_cnt' => $page_total,
                'cur' => $page,
            ),
        ));
        ?>

    </div>

    <div class="span4">
        <div class="well">
            <p>
                想当带头大哥？来吧！
                <a class="btn btn-success auth" href="/trip/create">发起旅行计划</a>
            </p>
        </div>

        <div>
            <div style="border-bottom: 1px solid #EEE;">
                <h4>你们可能感兴趣的人</h4>
            </div>
            <ul class="unstyled">
                <?php foreach ($suggest_users as $one) { ?>
                    <li class="clearfix" style="margin: 10px 0;">
                        <a href="/user/<?php echo $one['id']; ?>"><img style="width: 60px; float: left;" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $one['avatar']); ?>"></a>
                        <div style="margin-left: 70px;">
                            <ul class="unstyled">
                                <li><a href="/user/<?php echo $one['id']; ?>"><?php echo CHtml::encode($one['name']); ?></a><small class="muted"> <?php echo Helpers::cityName($one['live_city_id'], false); ?></small></li>
                                <li><?php echo $one['sex'] == 0 ? '女' : '男' ?>，<?php echo Helpers::ageFromBirthday($one['birthday']); ?>岁</li>
                            </ul>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>


<script>
    $(function() {
        $('#dest-input').tagsInput({
            autocomplete_url:'/api/dstAutocomplete',
            defaultText: '此处添加地点',
            height: '26px',
            width: '400px'
        }).importTags(<?php echo json_encode(join(',', $dsts)); ?>);
        
        $('.startdate-input, .enddate-input').datepicker({
            dateFormat: "yy-mm-dd",
            onSelect: function() {
                $('.enddate-input').datepicker('option', 'minDate',
                    $('.startdate-input').datepicker('getDate')
                );
            }
        });
    });
</script>
