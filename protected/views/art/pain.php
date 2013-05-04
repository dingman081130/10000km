<?php

    $colors = array(
        'center',
        'slateblue',
        'seagreen',
        'sienna',
        'deeppink',
        'turquoise',
        'tomato',
        'palegreen',
        'teal',
        'goldenrod'
        
    );

?>
<div class="row">
    <div class="span8">
        <canvas id="canvas" width="620" height="600" style="border: 1px #CCC solid;cursor: auto"></canvas>
        <canvas id="cursor" style="position:absolute"></canvas>
    </div>
    
    <div class="span4">
        <div class="color-box">
        <?php foreach($colors as $color) { ?>
        <button class="btn btn-color" style="margin-top: 5px;background: <?php echo $color; ?>" title="<?php echo $color; ?>"></button>
        <?php } ?>
        </div>
        
        <div style="margin-top:10px" id="h-slider" style="height:10px;" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" aria-disabled="false">
            <div class="ui-slider-range ui-widget-header ui-slider-range-min" style="height: 60%;"></div>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="bottom: 60%;"></a>
        </div>
        
        <div class="operator-box" style="margin-top:10px;">
        <button class="btn btn-primary btn-mini btn-undo">undo</button>
        <button class="btn btn-primary btn-mini btn-submit">提交</button>
        <button class="btn btn-primary btn-mini btn-save">保存到本地</button>
        
        </div>
    
    </div>
    
</div>

<style>
    .btn-color {
        width: 40px;
        height:40px;
        border-width: 2px;
        border-color: #444;
    }
    
    #canvas {
    }
</style>

<script src="/js/sketch.js"></script>
<script>
    
$(function(){
    
    
    
    $('.btn-undo').click(function(){
        var frame = frames.pop();
        if(frame){
            context.putImageData( frame, 0, 0 );
        }
    });
    
    var is_mouse_down = false;
    var x = 0, y = 0;
    var canvas = $('canvas')[0];
    var context= canvas.getContext("2d");
    var frames = new Array();
    
    $("#h-slider").slider({
        orientation: "horizontal",
        range: "min",
        min: 1,
        max: 100,
        value: 1,
        slide: function (event, ui) {
            context.lineWidth = ui.value;
        }
    });
  
    $('.btn-color').click(function(){
        context.strokeStyle = $(this).attr('title');
        context.fillStyle = $(this).attr('title');
        context.save();
        
    });
    
    $('.btn-save').click(function(){
        var data =canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
        window.location.href = data;
    });
    
    $('.btn-submit').click(function(){
        var canvasData = canvas.toDataURL("image/png");//jpg等格式类似
        $.post('/art/submit', {
            'data' : canvasData,
            csrf_token : $('meta[name=csrf_token_value]').attr('content')
        });
    });
    
    canvas.onmousemove = function(event){
        
        if (is_mouse_down) {
            var iX = event.clientX - canvas.offsetLeft+ (window.pageXOffset || document.body.scrollLeft|| document.documentElement.scrollLeft);
            var iY = event.clientY - canvas.offsetTop + (window.pageYOffset || document.body.scrollTop || document.documentElement.scrollTop);
            context.beginPath();
            context.lineJoin = "round";
            context.lineCap = "round";
            context.moveTo( x, y );
            context.lineTo(iX, iY);
            context.fill();
            context.stroke();
            context.closePath();
            x = iX;
            y = iY;
        }
    };
    
    canvas.onmousedown = function(event){
        is_mouse_down = true;
        x = event.clientX - canvas.offsetLeft+ (window.pageXOffset || document.body.scrollLeft|| document.documentElement.scrollLeft);
        y = event.clientY - canvas.offsetTop + (window.pageYOffset || document.body.scrollTop || document.documentElement.scrollTop);
        frames.push(context.getImageData( 0, 0, canvas.width, canvas.height ));
        //console.log(frames);
        
    };
    
    canvas.onmouseup = function(){
        is_mouse_down = false;
        x = y = 0;
    };
    
    painCursor( 10, 10, 20 );
    
});

function painCursor(x, y, radius){
    var cursor = document.getElementById('cursor');
    var cctx = cursor.getContext('2d');
    
    //console.log(cursor);
    //return false;
    
    var left = x;
    var top  = y;
    
    //alert(left);
    //cursor.left = left;
    //cursor.top = top;
    cursor.offsetLeft = left;
    cursor.offsetTop = top;
    
    alert(top);
    
    cursor.width = 100;
    cursor.height = 200;
    
    
    /*
    var cx = cursor.width / 2;
    cctx.beginPath();
    cctx.arc(cx,cx,cx,0, Math.PI * 2, true);
    cctx.stroke();
    cctx.closePath();*/
}
</script>
