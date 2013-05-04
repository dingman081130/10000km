<span class="badge badge-info" type="button">想去哪里？</span>
<p>   
    <div style="border:medium none;box-shadow: 0 0 0 0 #808080;width: 400px">
        <text id="dst-input" type="text" name="dsts" style="border: 0" ></text>
    </div>
</p>
<p>
    <div style="position: relative">
        <span class="badge badge-info">想去的时间？</span>
        从 <input style="position: relative" autocomplete="off" type="text" class="input-small" id="start-date" name="start_date" placeholder="不限"> 
        到 <input style="position: relative" autocomplete="off" type="text" class="input-small" id="end-date" name="end_date" placeholder="不限">
    </div>
</p>

<textarea id="desc" name="content" 
    style="resize: vertical; border: medium 1px; box-shadow: 0 0 0 0 #808080; width: 400px; height: 70px; min-height: 35px"></textarea>
<div class="clearfix" style="border-top: 1px dashed #C6CDD6; padding: 5px; margin-right: 50px">
    <div class="pull-left">
        <span class="badge badge-info">随便说点什么吧～</span>
    </div>
    <div>
        <a title="添加表情" style="float: left; display: block; cursor: pointer;" id="emotion-btn">
            <span style="display: block; width: 32px; height: 32px; background: url(/img/emotion-icon.png) no-repeat;">        
            </span>
        </a>
    </div>
    <div class="pull-right">
        <input type="checkbox" id="push_heehaw">&nbsp;&nbsp;同时驴叫~&nbsp;&nbsp;</input> 
        <button id="submit" class="btn btn-primary pull-right">提交</button>
    </div>
</div>

<script src="/js/jquery.emotion.js"></script>
<script>
    $(function() {
        $('#emotion-btn').emotion({
            target: $('#desc'),
            source: '/api/emotions'
        });
    });
</script>