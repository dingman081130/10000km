(function($){
    var target;
    $.fn.emotion = function(options){
        var defaults = {
            target: $(this).parent('form').find('textarea'),
            source: 'https://api.weibo.com/2/emotions.json?source=1362404091'
        };
        options = $.extend({}, defaults, options);
        var emotion_dlg;
        var cat_current;
        var cat_page;
        var emotions = new Array();
        var categorys = new Array();
        $(this).click(function(event){
            event.stopPropagation();
            if(!$(this).nextAll('.emotion-dlg')[0]){
                $(this).parent().append('<div class="emotion-dlg"></div>');
            }
            emotion_dlg = $(this).nextAll('.emotion-dlg');
            emotion_dlg.css({
                top: $(this)[0].offsetTop + $(this).height() + 8, 
                left: $(this)[0].offsetLeft
                });
            target = options.target;
            if(emotion_dlg.find('.emotion-categories')[0]){
                emotion_dlg.toggle();
                return;
            }
            emotion_dlg.html('<div>正在加载，请稍候...</div>');
            emotion_dlg.click(function(event){
                event.stopPropagation();
            });
            $.ajax({
                dataType: 'jsonp',
                url: options.source,
                beforeSend: function(){},
                error: function(request){
                    emotion_dlg.html('<div>加载失败</div>');
                },
                success: function(data){
                    emotion_dlg.html('<div style="float:right"><a href="javascript:void(0);" id="prev">&laquo;</a><a href="javascript:void(0);" id="next">&raquo;</a></div><div class="emotion-categories"></div><div class="emotion-container"></div><div class="emotion-page"></div>');
                    for(var i in data){
                        if(data[i].category == ''){
                            data[i].category = '默认';
                        }
                        if(emotions[data[i].category] == undefined){
                            emotions[data[i].category] = new Array();
                            categorys.push(data[i].category);
                        }
                        emotions[data[i].category].push({
                            name: data[i].phrase, 
                            url: data[i].url
                            });
                    }
                    emotion_dlg.find('#prev').click(function(){
                        showCategorys(cat_page - 1);
                    });
                    emotion_dlg.find('#next').click(function(){
                        showCategorys(cat_page + 1);
                    });
                    showCategorys();
                    showEmotions();
                }
            });
        });
        $('body').click(function(){
            $('.emotion-dlg').hide();
        });
        $.fn.insertText = function(text){
            this.each(function() {
                if(this.tagName !== 'INPUT' && this.tagName !== 'TEXTAREA') {
                    return;
                }
                if (document.selection) {
                    this.focus();
                    var cr = document.selection.createRange();
                    cr.text = text;
                    cr.collapse();
                    cr.select();
                }else if (this.selectionStart || this.selectionStart == '0') {
                    var 
                    start = this.selectionStart,
                    end = this.selectionEnd;
                    this.value = this.value.substring(0, start)+ text+ this.value.substring(end, this.value.length);
                    this.selectionStart = this.selectionEnd = start+text.length;
                }else {
                    this.value += text;
                }
            });        
            return this;
        }
        function showCategorys(){
            var page = arguments[0]?arguments[0]:0;
            if(page < 0 || page >= categorys.length / 5){
                return;
            }
            emotion_dlg.find('.emotion-categories').html('');
            cat_page = page;
            for(var i = page * 5; i < (page + 1) * 5 && i < categorys.length; ++i){
                emotion_dlg.find('.emotion-categories').append($('<a href="javascript:void(0);">' + categorys[i] + '</a>'));
            }
            emotion_dlg.find('.emotion-categories a').click(function(){
                showEmotions($(this).text());
            });
            emotion_dlg.find('.emotion-categories a').each(function(){
                if($(this).text() == cat_current){
                    $(this).addClass('current');
                }
            });
        }
        function showEmotions(){
            var category = arguments[0]?arguments[0]:'默认';
            var page = arguments[1]?arguments[1] - 1:0;
            emotion_dlg.find('.emotion-container').html('');
            emotion_dlg.find('.emotion-page').html('');
            cat_current = category;
            for(var i = page * 72; i < (page + 1) * 72 && i < emotions[category].length; ++i){
                emotion_dlg.find('.emotion-container').append($('<a href="javascript:void(0);" title="' + emotions[category][i].name + '"><img src="' + emotions[category][i].url + '" alt="' + emotions[category][i].name + '" width="22" height="22" /></a>'));
            }
            emotion_dlg.find('.emotion-container a').click(function(){
                target.insertText($(this).attr('title'));
                emotion_dlg.hide();
            });
            for(var i = 1; i < emotions[category].length / 72 + 1; ++i){
                emotion_dlg.find('.emotion-page').append($('<a href="javascript:void(0);"' + (i == page + 1?' class="current"':'') + '>' + i + '</a>'));
            }
            emotion_dlg.find('.emotion-page a').click(function(){
                showEmotions(category, $(this).text());
            });
            emotion_dlg.find('.emotion-categories a.current').removeClass('current');
            emotion_dlg.find('.emotion-categories a').each(function(){
                if($(this).text() == category){
                    $(this).addClass('current');
                }
            });
        }
    }
})(jQuery);