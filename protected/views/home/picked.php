<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'picked',
        ));
        ?>
    </div>
    <div class="span6">
        <h4>求被捡</h4>
        <?php 
            $this->widget('PickedWidget', array());
        ?>
    </div>
    <div class="span4">
        <div class="pull-left" style="margin-left: -20px">
            <h4>你可能会感兴趣的旅行</h4>
        </div>
        <div class="trip-list1 pull-left" id="con-trip">
            <!--预留由js填充-->
        </div>
    </div>
</div>
<script src="<?php //echo Yii::app()->params['staticBaseUrl'];?>/js/jquery.tagsinput.js"></script>
<script src="<?php //echo Yii::app()->params['staticBaseUrl'];?>/js/zebra_datepicker.js"></script>
<script>
$(function(){
    $('#start-date').Zebra_DatePicker({
        direction : true,  // dates can be selected only in the future
        readonly_element : false,
        first_day_of_week : 0,
        //onChange : function(){ dynamicLoad();},
        onSelect : function(){ dynamicLoad();},
        offset : [10, 140],
        pair : $('#end-date')
        //format : 'Y-M-D'
    });
    $('#end-date').Zebra_DatePicker({
        direction : true,  // dates can be selected only in the future
        readonly_element : false,
        first_day_of_week : 0,
        onSelect : function(){ dynamicLoad();},
        offset : [10, 140]
        //format : 'Y-M-D'
    });
    $('#dst-input').tagsInput({
        autocomplete_url:'/api/dstAutocomplete',
        defaultText: '在此处添加地点',
        height: '26px',
        width: '400px',
        interactive: true,
        minChars: 2,
        maxChars: 50,
        removeWithBackspace: true,
        //onAddTag: function(){dynamicLoad();},
        onChange: function(){dynamicLoad();},
        placeholderColor: '#666666'
    });
    $('#submit').click(function(){
        submitPicked();
    });
    dynamicLoad();
    listenScroll();
});

function listenScroll(){
    //Dynamic Loading trip
    //alert($('.fresh-item').length);
    var neg_step = false; // used to judge whether negtive step happens
    var has_fresh = true;  // if has another fresh messages
    // 监听窗口滚动条
    $(window).scroll(function (){
        // 计算被卷去的高度
        var roll = document.body.scrollTop || document.documentElement.scrollTop;
        var doc = $(document).height();  // 页面高度 暂时没用到
        var view = $(window).height();    // 可视区高
        var rest = doc - roll - view;

        //当滚动到底部时
        if( rest <= 0 ){
            if( !neg_step && has_fresh ){  // 如果是第一次超出则响应，执行waterfall的append逻辑
                neg_step = true;
                //alert('neg_step');
                // add your append logic here
                has_fresh = dynamicLoad();
            }
        }else{
            neg_step = false;
        }
    });
}

function dynamicLoad(){
    var dst = $('#dst-input').val();
    var start = $('#start-date').val();
    var end = $('#end-date').val();
    var offset = $('.trip-item1').length;
    
    var has_fresh = true;
    $.post('/trip/searchhtml', {
        dsts : dst,
        start_date : start,
        end_date : end,
        offset : offset,
        csrf_token : $('meta[name=csrf_token_value]').attr('content')
    }, function(data){
        if(!data.success){
            alert(data.msg);
            has_fresh = false;
        }else if(data.data.code){
            $('#con-trip').append(data.data.code);
            has_fresh = true;
        }else{
            has_fresh = false;
        }
    }, 'json');
    
    return has_fresh;
}

/**
 * 提交求捡信息 
 */
function submitPicked(){
    var dst   = $('#dst-input').val();
    var start = $('#start-date').val();
    var end   = $('#end-date').val();
    var desc  = $('#desc').val();
    var tmp  = $('#push_heehaw').attr("checked");
    var heehaw = tmp == 'checked' ? true : false;
    
    if(!dst.replace(/(^\s*)|(\s*$)/g, '')){
        alert("请填写想去的你想去的地方~");
        return false;
    }
    
    if(heehaw && !desc.replace(/(^\s*)|(\s*$)/g, '')){
        alert("如果要驴叫，请填写叫声～");
        return false;
    }
    
    $.post('/home/updatePick', {
        dsts : dst,
        start_date : start,
        end_date : end,
        desc : desc,
        heehaw : heehaw,
        csrf_token : $('meta[name=csrf_token_value]').attr('content')
    }, function(data){
        if(!data.success){
            alert(data.msg);
            return false;
        }
    }, 'json');   
}
</script>