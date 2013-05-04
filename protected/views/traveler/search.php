<div class="row">

    <div class="span8">
        <div class="row">
            <div class="input-append">
                <input class="span6" type="text" style="height: 30px; font-size: 16px;">
                <button class="btn" type="button" style="height: 40px;">搜索</button>
            </div>
            <div><a href="javascript:;"><i class="icon-globe"></i><span style=" display: inline-block; width: 60px;">任意地点</span></a><a href="javascript:;"><i class="icon-map-marker"></i><span style=" display: inline-block; width: 60px;">当前位置</span></a> <a href="javascript:;"><i class="icon-list"></i><span style=" display: inline-block; width: 60px;">选择地点</span></a></div>
        </div>

        <div class="row">
            <div class="pull-right">
                <span>找到1024个沙发主，顺序方式：</span>
                <ul class="nav nav-pills" style="display: inline-block; margin: 0;">
                    <li class="dropdown">
                        <a style="padding: 0; margin-bottom: -2px;" href="#" data-toggle="dropdown" class="dropdown-toggle">相关性<span class="caret"></span></a>
                        <ul class="dropdown-menu" id="menu1">
                            <li><a href="#" style="color: #999;">相关性</a></li>
                            <li><a href="#">信誉</a></li>
                            <li><a href="#">经验值</a></li>
                            <li id="active" href="/demo/downvoted/"><a href="#" id="current"><span class="active">最近登录</span></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <ul class="unstyled">
                <?php
                for ($i = 0; $i < 5; $i++) {
                    ?>
                    <li>
                        <div style="position: relative; border-bottom: 1px solid #CCC; padding-top: 5px; margin-top: 5px;">

                            <div style="position: absolute; left: 0;">
                                <img src="http://hdn.xnimg.cn/photos/hdn321/20110308/0000/h_large_ZQ5y_66950000a8092f74.jpg">
                            </div>

                            <div style="margin-left: 120px; padding-right: 10px;">
                                <div>
                                    <h4>小傻逼<small>厦门</small></h4>
                                    <ul class="unstyled">
                                        <li style="display: inline-block;">朋友<span class="badge badge-info">1024</span></li>
                                        <li style="display: inline-block;">评价<span class="badge badge-info">128</span></li>
                                        <li style="display: inline-block;">照片<span class="badge badge-info">512</span></li>
                                        <li style="display: inline-block;">回复率<span class="badge badge-info">80%</span></li>
                                    </ul>
                                </div>
                                <div style="/*border-top: 1px solid #EEE;*/ margin-top: 10px;">
                                    <dl class="dl-horizontal" style="margin-left: -100px;">
                                        <dt>基本信息</dt>
                                        <dd>女, 19岁，大学生</dd>
                                        <dt>介绍</dt>
                                        <dd>傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊傻逼啊</dd>
                                        <dt>宣言</dt>
                                        <dd>接客10000人</dd>
                                    </dl>
                                </div>
                            </div>

                        </div>
                    </li>


                <?php } ?>

            </ul>
        </div>
    </div>


    <div class="span4" style="position: relative;">
        <div id="right-float-side" style="position: fixed; top: 70px;">
            <div class="clearfix">
                <div id="map" style="width:300px;height:240px"></div>
                <div style="position: absolute; right: 0; bottom: 0; background-color: white; border-top-left-radius: 5px; padding: 2px 5px;">
                    <label class="inline checkbox"><input type="checkbox">搜索地图区域</label>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    function initialize() {
        var mp = new BMap.Map('map');
        mp.centerAndZoom(new BMap.Point(118.103886,24.489231), 11);
        mp.enableScrollWheelZoom();
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