<?php
Yii::import('application.models.City.City');
?>
<div class="row search-user-box">

    <div class="span8">
        <div class="row">
            <form action="/search/search" class="search-form">
                <div class="input-append">
                    <input class="span6" name="kw" type="text" id="location-input" autocomplete="off" style="height: 30px; font-size: 16px;" value="<?php
if ($params['type'] == 'city') {
    echo CHtml::encode(Helpers::cityName($params['city_id']));
} else {
    echo '地图区域';
}
?>">
                    <button class="btn search-btn" type="submit" style="height: 40px; width: 84px; font-size: 16px;">搜 索</button>
                </div>
            </form>
            <div style="position: relative;">
                <?php
                if ($params['type'] == 'city') {
                    $city = City::getCity($params['city_id']);
                } else {
                    $sw_lng = $params['sw_lng'];
                    $sw_lat = $params['sw_lat'];
                    $ne_lng = $params['ne_lng'];
                    $ne_lat = $params['ne_lat'];
                }
                ?>
                <input type="hidden" id="city-id" value="<?php
                echo isset($city) ? $city['id'] : '';
                ?>">
                <input type="hidden" id="area-id" sw_lat="<?php echo isset($sw_lat) ? CHtml::encode($sw_lat) : ''; ?>" sw_lng="<?php echo isset($sw_lng) ? CHtml::encode($sw_lng) : ''; ?>" ne_lat="<?php echo isset($ne_lat) ? CHtml::encode($ne_lat) : ''; ?>" ne_lng="<?php echo isset($ne_lng) ? CHtml::encode($ne_lng) : ''; ?>">
                <a href="javascript:;" id="current-location-btn"><i class="icon-map-marker"></i><span style=" display: inline-block; width: 60px;">当前位置</span></a>
                <a href="javascript:;"><i class="icon-list"></i><span style=" display: inline-block; width: 60px;" class="select-city-btn">选择地点</span></a>
                <div class="select-city-box" style="display: none; position: absolute; left: 160px; background-color: white; z-index: 10; padding: 10px; border: 1px solid #CCC;">
                    <select size="12" class="span2 city1">
                        <option value="1">北京</option>
                        <option value="2">上海</option>
                        <option value="3">天津</option>
                        <option value="4">重庆</option>
                        <option value="5">安徽</option>
                        <option value="23">福建</option>
                        <option value="33">甘肃</option>
                        <option value="48">广东</option>
                        <option value="70">广西</option>
                        <option value="85">贵州</option>
                        <option value="95">海南</option>
                        <option value="114">河北</option>
                        <option value="126">河南</option>
                        <option value="144">黑龙江</option>
                        <option value="158">湖北</option>
                        <option value="176">湖南</option>
                        <option value="191">江苏</option>
                        <option value="205">江西</option>
                        <option value="217">吉林</option>
                        <option value="227">辽宁</option>
                        <option value="242">内蒙古</option>
                        <option value="255">宁夏</option>
                        <option value="261">青海</option>
                        <option value="270">山东</option>
                        <option value="288">山西</option>
                        <option value="300">陕西</option>
                        <option value="311">四川</option>
                        <option value="333">西藏</option>
                        <option value="341">新疆</option>
                        <option value="360">云南</option>
                        <option value="377">浙江</option>
                        <option value="389">香港</option>
                        <option value="390">澳门</option>
                        <option value="391">台湾</option>
                    </select>

                    <select size="12" class="span2 city2">

                    </select>
                    <div class="clearfix">
                        <button class="pull-right btn btn-primary ok-btn">确定</button>
                        <button class="pull-right btn cancel-btn" style="margin: 0 10px;">取消</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <ul class="unstyled search-result-list">
                <?php
                foreach ($users as $user) {
                    $this->renderPartial('userItem', array(
                        'user' => $user,
                    ));
                }
                ?>

            </ul>
            <button class="btn btn-large load-more-btn" style="width: 100%;">显示更多</button>
        </div>
    </div>


    <div class="span4">
        <div id="right-float-side" style="position: absolute;">
            <div class="clearfix" style="position: relative;">
                <div id="map" style="width:300px;height:240px"></div>
                <div style="width: 110px; position: absolute; right: 0; bottom: 0; background-color: white; border-top-left-radius: 5px; padding: 2px 5px;">
                    <label class="checkbox"><input type="checkbox" id="search-map-area" <?php if ($params['type'] == 'area') echo 'checked'; ?>>搜索地图区域</label>
                </div>
            </div>

            <div style=" overflow: auto;">
                <div>
                    <h5>查找：</h5>
                    <label class="radio">
                        <input type="radio" name="search_type" value="local" <?php if ($params['user_type'] == 'local') echo 'checked'; ?>>
                        当地人
                    </label>
                    <!--
                    <label class="radio">
                        <input type="radio" name="search_type" value="traveler"  <?php if ($params['user_type'] == 'traveler') echo 'checked'; ?>>
                        在此旅行的人
                    </label>
                    -->
                    <label class="radio">
                        <input type="radio" name="search_type" value="host" <?php if ($params['user_type'] == 'host') echo 'checked'; ?>>
                        沙发主
                    </label>
                    <label class="radio">
                        <input type="radio" name="search_type" value="surfer"  <?php if ($params['user_type'] == 'surfer') echo 'checked'; ?>>
                        沙发客
                    </label>
                </div>
                <div>
                    <h5>更多条件：</h5>
                    <div style="display: block;">
                        <ul class="nav nav-tabs nav-stacked">
                            <li>
                                <div style="border-top: 1px solid #CCC;">
                                    <a href="#" style="display: block; padding: 5px 0;" class="show-btn">年龄和性别▾</a>
                                </div>
                                <div>
                                    年龄：<select style="width: 60px;" id="start_age">
                                        <option value=""></option>
                                        <?php for ($i = 18; $i <= 100; $i++) { ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>到
                                    <select style="width: 60px;" id="end_age">
                                        <option value=""></option>
                                        <?php for ($i = 18; $i <= 100; $i++) { ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                    <p>
                                        <label class="checkbox inline"><input id="sex-male" type="checkbox" checked>男</label>
                                        <label class="checkbox inline"><input id="sex-female" type="checkbox" checked>女</label>
                                    </p>
                                </div>
                            </li>

                            <li>
                                <div style="border-top: 1px solid #CCC;">
                                    <a href="#" class="show-btn">信誉▾</a>
                                </div>
                                <div>
                                    <label class="checkbox"><input id="have-photo" type="checkbox">有照片</label>
                                </div>
                            </li>

                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function initialize() {
        SearchUser.init($('.search-user-box'));
    }
    $(function() {
        var script = document.createElement("script");
        script.src = "http://api.map.baidu.com/api?v=1.4&callback=initialize";
        document.body.appendChild(script);
    });
</script>
