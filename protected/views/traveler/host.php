<div class="row">
    <form action="/couch/search" method="get">
        <h3>找沙发</h3>
        <h4>你要去哪里？</h4>
        <input type="text" class="span6" id="location-input">
        <div><a href="javascript:;"><i class="icon-globe"></i><span style=" display: inline-block; width: 50px;">随意</span></a><a href="javascript:;"><i class="icon-map-marker"></i><span style=" display: inline-block; width: 60px;">当前位置</span></a> <a href="javascript:;"><i class="icon-list"></i><span style=" display: inline-block; width: 60px;">选择地点</span></a></div>
        <div>沙发主可以提供
            <select style="width: 70px;">
                <option>任意</option>
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
                <option>6+</option>
            </select>
            个沙发
        </div>

        <div>
            <a href="javascript:;" id="more-condition-btn">更多沙发主条件▾</a>
            <div style="display: none;" class="row">
                <div class="span3">
                    <h5>年龄、性别及关键字</h5>
                    年龄：<select style="width: 60px;">
                        <option>18</option>
                    </select>到
                    <select style="width: 60px;">
                        <option>18</option>
                    </select>
                    <p>
                        <label class="checkbox inline"><input type="checkbox">男</label>
                        <label class="checkbox inline"><input type="checkbox">女</label>
                        <label class="checkbox inline"><input type="checkbox">多人住</label>
                    </p>
                    <label>关键字</label>
                    <input type="text">
                </div>

                <div class="span3">
                    <h5>信誉</h5>
                    <label class="checkbox"><input type="checkbox">有照片</label>
                </div>
            </div>
        </div>

        <button class="btn btn-primary">查找</button>
    </form>
</div>

<script src="http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js"></script>

<script>
    $('#location-input').val(remote_ip_info.country + ',' + remote_ip_info.province + ',' + remote_ip_info.city);
    $('#more-condition-btn').click(function(){
        $(this).next('div').show();
    });
</script>