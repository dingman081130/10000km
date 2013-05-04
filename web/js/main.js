var ApplyCouchForm = {
    init : function( form, btn ){
        var f = form, b = btn;
        btn.click(function(){
            f.slideToggle('fast');
            if ($(this).html() == '申请沙发')
                $(this).html('取消申请');
            else
                $(this).html('申请沙发');
        });
        
        f.find('#apply-couch-cancel').click(function(){
            f.slideUp('fast');
            return false;
        });
        
        f.find('#apply-couch-submit').click(function(){
            f.ajaxForm({
                complete: function(xhr){
                    var data = $.parseJSON(xhr.responseText);
                    if (data.code == 0) {
                        alert('申请已发出');
                        b.replaceWith('<a class="pull-right" href="/home/couchSurf">你已申请，点击查看</a>');
                        f.slideUp('fast', function(){
                            f.remove();
                        });
                    } else if( data.msg != undefined){
                        alert(data.msg);
                    } else {
                        for(var one in data.data) {
                            var id = 'apply-couch-' + data.data[one].attribute;
                            var error = data.data[one].error;
                            var attribute = f.find("#" + id);
                            attribute.parent().parent().addClass('error');
                            attribute.next('.help').text(error).show();    
                        }
                    }
                }
            }).submit();
            return false;
            
        });
        
        f.find('#apply-couch-title, #apply-couch-content,#apply-couch-arrive_date,#apply-couch-leave_date,#apply-couch-couch_number').focus(function(){
            
            $(this).parent().parent().removeClass('error');
            $(this).next('.help').text('');
            $(this).next('.help').hide();
        }).blur(function(){
            if (!$.trim($(this).val())) {
                $(this).parent().parent().addClass('error');
                $(this).next('.help').text("请输入" + $(this).attr('cname')).show();
            }
        });
        
        f.find('#apply-couch-arrive_date,#apply-couch-leave_date').change(function(){
            if (!$.trim($(this).val())) {
                $(this).parent().parent().addClass('error');
                $(this).next('.help').text("请输入" + $(this).attr('cname')).show();
            } else {
                $(this).parent().parent().removeClass('error');
                $(this).next('.help').text('').hide();
            }
        });
        
        
        
        f.find('#apply-couch-leave_date').datepicker({dateFormat: 'yy-mm-dd'});
        f.find('#apply-couch-arrive_date').datepicker({dateFormat: 'yy-mm-dd'});
        
    }
}

var TipManager = {
    getTipObj : function() {
        //return $('#ttips');
    },
    
    getTips : function(){
        //TipManager.getTipObj().slideToggle(2000);
    }
}

var TopicStory = {
    
    init : function(topicstory){
        var ts = topicstory;
        ts.find('.reply-emotion').emotion({
            target: ts.find('.reply-text'),
            source: '/api/emotions'
        });
        
        TopicStory.initReply(ts);
        TopicStory.initCommentReply(ts);
        TopicStory.initLoadMoreComments(ts);
        TopicStory.initContentEdit(ts);
        TopicStory.initContentSave(ts);
        TopicStory.initTitleEdit(ts);
        
    },
    
    initContentEdit : function (topicstory) {
        var ts = topicstory;
        ts.find('.content-edit').click(function(){
            $('#redactor_content').redactor({ focus: true });
        });
    },
    
    initTitleEdit : function (topicstory) {
        var ts = topicstory;
        ts.find('.pencil-icon').click(function() {
            ts.find('.edit_able').trigger('click');
        });
        ts.find('.edit_able').click(function() {
            var title = $(this).html();
            
            var t_text = $('<textarea style="width:50%" rows="2">');
            t_text.val(title);
            ts.find('#title-bar').hide();
            ts.prepend(t_text);
            t_text.focus();
            
            t_text.blur(function() {
                var tid = ts.attr('tid');
                var title = $.trim($(this).val());
                if(!title){
                    alert('标题不能为空');
                    return false;
                }
                $.post('/topic/setTitle',{
                    tid : tid,
                    title : title,
                    csrf_token : $('meta[name=csrf_token_value]').attr('content')
                }, function(data){
                    if (data.code != 0) {
                        alert(data.msg);
                        return false;
                    }
                    t_text.remove();
                    ts.find('.edit_able').html(title);
                    ts.find('#title-bar').show();
                }, 'json');
                
            });
        });
    },
    
    initContentSave : function (topicstory) {
        var ts = topicstory;
        
        ts.find('.content-save').click(function(){
            var content = $.trim(ts.find('#redactor_content').getCode());
            var tid     = ts.attr('tid');
            
            if(!content){
                alert('请填写内容');
                return false;
            }
            $.post('/topic/setContent', {
                tid : tid,
                content : content,
                csrf_token : $('meta[name=csrf_token_value]').attr('content')
            }, function(data){
                if (data.code != 0) {
                    alert(data.msg);
                    return false;
                }
                ts.find('#redactor_content').destroyEditor();
                return true;
            }, 'json');
            
        });
    },
    
    initCommentReply : function(topicstory){
        var ts = topicstory;
        ts.find('.comment-reply').click(function(){
            var uname = $(this).closest('.comment-item').attr('uname');
            var cid = $(this).closest('.comment-item').attr('cid');
            var reply_text = ts.find('.reply-text');
            reply_text.val("回复"+uname+':').focus().setCursorLastPosition();
            var reply_button = ts.find('.reply-button');
            reply_button.attr('cid', cid);
            return false;
        });
    },
    
    //// 初始化载入更多评论
    initLoadMoreComments : function(topicstory){
        var ts = topicstory;
        ts.find('.comments_load_more').click(function(){
            var tid = ts.attr('tid');
            var offset = ts.find('.comment-item').length;
            var hasmore = $(this).attr('hasmore');
            if(hasmore != 'yes'){
                return true;
            }
            $.get('/topic/CommentHtml', {
                'tid':tid,
                'offset':offset
            }, function(html){
                var h = $(html);
                var cnt = 0;
                h.hide();
                ts.find('.comment-item:last').append(h);
                h.slideDown('3000');
                TopicStory.initCommentReply(ts);
                if(!html){
                    ts.find('.comments_load_more').attr('hasmore', 'no');
                    ts.find('.comments_load_more').find('small').html('已经加载完毕啦 bye!');
                    ts.find('.comments_load_more').slideUp(2000);
                }
            }, 'html');
        });
    },
    
    initReply : function(topicstory){
        var ts = topicstory;
        ts.find('.reply-button').click(function(){
            var tid = ts.attr("tid");
            var cid = $(this).attr('cid');
            var content = $.trim(ts.find('.reply-text').val());
            content = content.replace(/回复[^:]+:/, '');
            if(!content){
                ts.find('.reply-text').focus();
                alert("请填写内容");
                return false;
            }
            $.post('/Topic/Comment',{
                csrf_token : $('meta[name=csrf_token_value]').attr('content'),
                cid : cid,
                content : content,
                tid : tid
            }, function(data){
                if(data.code != 0){
                    alert(data.msg);
                }else{
                    var commentitem = $(data.data.code);
                    ts.find('.comment-list').prepend(commentitem);
                    ts.find('.reply-text').val('');
                    ts.find('.reply-button').attr('cid', 0);
                    TopicStory.initCommentReply(ts);
                }
            },'json');
        });
    }
    
}

//// 驴叫
var Heehaw = {
    init : function(heehaw){
        var h = heehaw;
        h.find('#emotion-btn').emotion({
            target: h.find('#desc'),
            source: '/api/emotions'
        });
        h.find('#submit').click(function(){
            Heehaw.pubHeehaw(h);
        });
    },
    //// 发布驴叫
    pubHeehaw : function(heehaw){
        var h = heehaw;
        var desc  = h.find('#desc').val();

        if(!desc.replace(/(^\s*)|(\s*$)/g, '')){
            alert("亲，还木有输入哦～");
            return false;
        }

        $.post('/home/pubHeehaw', {
            content : desc,
            csrf_token : $('meta[name=csrf_token_value]').attr('content')
        }, function(data){
            if(!data.success){
                alert(data.msg);
                return false;
            }else{
                h.find('#desc').attr('value', '');
                var fi = $(data.data.code);
                $('.fresh-list:first').prepend(fi);
                return true;
            }
        }, 'json');   
    }
}

//// 上传图片
var FileUpload = {
    init : function(fileupload){
        var f = fileupload;
        f.fileupload({
            dataType: 'json',
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .bar').css('width', progress + '%');
            },
            filesContainer: $('#upload_files_container'),
            uploadTemplateId: null,
            uploadTemplate: function (o) {
                var rows = $();
                $.each(o.files, function (index, file) {
                    var row = $('<tr class="template-upload fade">' +
                        '<td class="preview"><span class="fade"></span></td>' +
                        '<td class="name"></td>' +
                        '<td class="size"></td>' +
                        (file.error ? '<td class="error" colspan="2"></td>' :
                            '<td><div class="progress">' +
                            '<div class="bar" style="width:0%;"></div></div></td>' +
                            '<td class="start"><button class="btn btn-primary"><i class="icon-upload icon-white"></i><span> 上传</span></button></td>'
                            ) + '<td class="cancel"><button class="btn btn-warning"><i class="icon-ban-circle icon-white"></i><span> 取消</span></button></td></tr>');
                    row.find('.name').text(file.name);
                    row.find('.size').text(o.formatFileSize(file.size));
                    if (file.error) {
                        row.find('.error').text(
                            locale.fileupload.errors[file.error] || file.error
                            );
                    }
                    rows = rows.add(row);
                });
                return rows;
            }
        });
    }
}

//// 相片修改按钮群
var PhotoSetBtns = {
    getPhotoStory : function(){
        return $('.photo-story');
    },
    //// 初始化
    init : function(psb){
        psb.find('.set-cover').click(function(){
            PhotoSetBtns.setCover();
        });
        psb.find('.set-delete').click(function(){
            if(confirm('确认删除此张图片?')){
                PhotoSetBtns.delPhoto();
            }
        });
    },
    //// 删除一张图片
    delPhoto : function(){
        var p = PhotoSetBtns.getPhotoStory();
        var pid = p.attr('pid');
        $.post('/photo/delete', {
            pid:pid,
            csrf_token : $('meta[name=csrf_token_value]').attr('content')
        }, function(data){
            //// 如果请求执行失败
            if(!data.success){
                alert(data.msg);
                return false;
            }
            AlbumPreviewer.delClip(PhotoStory.getAlbumPreviewer(), pid);
            if(data.data.photo.id != undefined){
                PhotoStory.loadStory(p, data);
            }else{
                p.remove();
            }
            if(data.data.album.cover_surl != undefined){
                AlbumHeader.setCover(PhotoStory.getAlbumHeader(), data.data.album.cover_surl, data.data.album.cover);
            }else{
                AlbumHeader.setCover(PhotoStory.getAlbumHeader(), 'http://10000km.oss.aliyuncs.com/image/photo/s/no_photo.png', 0);
            }
        }, 'json');
    },
    //// 设置封面
    setCover : function(){
        var p = PhotoSetBtns.getPhotoStory();
        $.post('/album/setCover', {
            pid:p.attr('pid'),
            csrf_token : $('meta[name=csrf_token_value]').attr('content')
        }, function(data){
            if(!data.success){
                alert(data.msg);
                return false;
            }
            AlbumHeader.setCover(PhotoStory.getAlbumHeader(), data.data.photo.surl, data.data.photo.id);
            return true;
        }, 'json');
        
    }
}

//// 相册头信息
var AlbumHeader = {
    
    setCover : function(albumheader, src, pid){
        var a = albumheader;
        a.find('.album-cover').find('.img-polaroid').attr('src', src);
        a.find('.album-cover').attr('pid', pid);
    },
    //// 获取封面的id
    getCoverId : function(albumheader){
        var a = albumheader;
        return a.find('.album-cover').attr('pid');
    }
}

//// 相册预览
var AlbumPreviewer = {
    
    //// 调整相册预览的大小
    resize : function(albumpreviewer, apheight, size){
        var width  = size.width;
        var height = size.height;
        var a = albumpreviewer;
        a.css({
            'height':apheight+'px'
        });
        a.find('.photo-clip').css({
            'width':width+'px',
            'min-width':width+'px',
            'max-width':width+'px',
            'height': height+'px',
            'max-height': height+'px',
            'min-height': height+'px',
            'line-height': height+'px'
        });
        var iwidth  = width - 12;
        var iheight = height - 12;
        a.find('.photo-clip').find('img').css({
            'with':iwidth+'px',
            'max-width': iwidth+'px',
            'max-height': iheight+'px'
        });
        a.show();
    },
    
    //// 相册预览生效瀑布墙功能
    waterfall : function(albumpreviewer){},
    
    //// 相册预览绑定照片story
    bindStory : function(albumpreviewer, photostory){
        var a = albumpreviewer;
        var p = photostory;
        if(p.length <= 0){
            return true;
        }
        a.find('.photo-clip').click(function(){
            var pid = $(this).attr('pid');
            $.get('/photo/getphoto/'+pid, function(data){
                PhotoStory.loadStory(p, data);
            }, 'json');
        });
        AlbumPreviewer.changeAactive(a, p.attr('pid'));
        return true;
    },
    //// 改变相册预览当前照片
    changeAactive : function(albumpreviewer, pid){
        var a = albumpreviewer;
        a.find('.photo-clip div.sel').removeClass('sel');
        var cur = a.find('.photo-clip[pid='+pid+']');
        var ul = cur.parent('ul');
        cur.find('div').addClass('sel');
        var diff = cur.offset().top - ul.offset().top;
        ul.animate({scrollTop: $(ul).scrollTop() + diff - cur.height()}, 'fast');
        
    },
    //// 删除一个图片
    delClip : function(albumpreviewer, pid){
        var a = albumpreviewer;
        a.find('.photo-clip[pid='+pid+']').remove();
        return true;
    }
}  

//// 照片story
var PhotoStory = {
    //// 通过url定位符生成一个photostory
    boom : function(){
        var pid = window.location.hash.split('#');
        pid = pid[1];
        if(pid==undefined){
            pid = AlbumHeader.getCoverId(PhotoStory.getAlbumHeader());
            if(pid==undefined || pid == 0){
                pid = PhotoStory.getAlbumPreviewer().find('.photo-clip:first').attr('pid');
            }
        }
        $.get('/photo/photoStoryHtml/'+pid, function(photostory){
            //alert(photostory);
            var p = $(photostory);
            if(!p){
                return false;
            }
            PhotoStory.getAlbumHeader().after(p);
            PhotoStory.initPhotoStory(p);
            AlbumPreviewer.bindStory(PhotoStory.getAlbumPreviewer(), p);
            return true;
        }, 'html');
    },
    //// 获取文档范围内的albumpreviewer
    getAlbumPreviewer : function(){
        return $('.album-previewer');
    },
    //// 获取文档范围内的方面对象
    getAlbumHeader : function(){
        return $('.album-header');
    },
    //// 初始化放大图标效果
    initZoomOutIcon : function(photostory){
        var p = photostory;
        p.find('.photo-area').mousemove(function(e){
            $(this).css({
                cursor:"pointer"
            });
        });
    },
    //// 点击照片跳转到照片详情页
    initClickJump : function(photostory){
        var p = photostory;
        var aid = p.attr('aid');
        var pid = p.attr('pid');
        p.find('.photo-area').click(function(){
            window.location.href = '/photo/'+aid+'#'+pid;
        });
    },
    //// 初始化照片点击切换
    initClickCyc : function(photostory){
        var p = photostory;
        p.find('.photo-area .nav-btn.prev').click(function(){
            PhotoStory.cycPrev(p);
        });
        p.find('.photo-area .nav-btn.next').click(function() {
            PhotoStory.cycNext(p);
        })    
    },
    //// 初始化照片按方向键切换
    initPressCyc : function(photostory){
        var p = photostory;
        $(document.body).keydown(function(e){
            if(e.target == document.body) {
                if(e.which == 37){
                    PhotoStory.cycPrev(p);
                }else if(e.which == 39){
                    PhotoStory.cycNext(p);
                }
            }
        });
    },
    //// 初始化回复框的行为
    initReplyText : function(photostory){
    },
    //// 初始化评论项中的回复按钮的点击行为
    initCommentItemReplyBtn : function(photostory){
        var p = photostory;
        p.find('.reply').click(function(){
            var uname = $(this).attr('uname');
            var uid   = $(this).attr('uid');
            p.find('.photo-comment-reply-button').attr('ruid', uid);
            p.find('.photo-comment-reply-text')
                .val('回复'+uname+':').focus()
                .setCursorLastPosition();
            return false;
        }); 
    },
    //// 初始化评论提交按钮的行为
    initCommentReplyBtn : function(photostory){
        var p = photostory;
        p.find('.photo-comment-reply-button').click(function(){
            PhotoStory.postComment(p);
        });
    },
    initCommentEmotion : function(photostory){
        var p = photostory;
        p.find('.photo-comment-reply-emotion').emotion({
            target: p.find('.photo-comment-reply-text'),
            source: '/api/emotions'
        });
    },
    //// 初始化回复框
    initPhotoTitleText : function(p){
        p.find('.photo-desc .edit_area').editable('/photo/setTitle', {
            type: 'textarea',
            tooltip: '点击编辑照片标题',
            placeholder: '点击编辑照片标题',
            width: 300,
            rows: 5,
            onblur: 'submit',
            submitdata: function() {
                return {
                    csrf_token: $('meta[name="csrf_token_value"]').attr('content'),
                    pid: p.attr('pid')
                }
            }
        });
        p.find('.photo-desc .pencil-icon').click(function() {
            $(this).prev('div').trigger('click');
        });
    },
    //// 初始化载入更多评论
    initLoadMoreComments : function(photostory){
        var p = photostory;
        p.find('#comments_load_more').click(function(){
            var pid = p.attr('pid');
            var offset = p.find('.photo-comment-item').length;
            var hasmore = $(this).attr('hasmore');
            if(hasmore != 'yes'){
                return true;
            }
            $.get('/photo/CommentHtml', {
                'pid':pid,
                'offset':offset
            }, function(html){
                var h = $(html);
                h.hide();
                p.find('.photo-comment-item:last').append(h);
                h.slideDown('3000');
                PhotoStory.initCommentItemReplyBtn(p);
                
                if(!html || h.length < p.find('.photo-comment-list').attr('page_size')){
                    p.find('#comments_load_more').attr('hasmore', 'no');
                    p.find('#comments_load_more').find('small').html('已经加载完毕啦 bye!');
                    p.find('#comments_load_more').slideUp(2000);
                }
            }, 'html');
        });
    },
    //// 初始化PhotoStory的一些通用行为
    initCommon : function(photostory){
        var p = photostory;
        PhotoStory.initReplyText(p);
        PhotoStory.initCommentItemReplyBtn(p);
        PhotoStory.initCommentReplyBtn(p);
        PhotoStory.initCommentEmotion(p);
        PhotoStory.initLoadMoreComments(p);
        
    },
    //// 初始化fresh模式下的photo-story
    initPhotoStoryInFreshMode : function(photostory){
        var p = photostory;
        PhotoStory.initCommon(p);
        PhotoStory.initZoomOutIcon(p);
        PhotoStory.initClickJump(p);
    },
    //// 初始化photo-story
    initPhotoStory : function(photostory){
        var p = photostory;
        PhotoStory.initCommon(p);
        PhotoStory.initClickCyc(p);
        PhotoStory.initPressCyc(p);
        PhotoStory.initPhotoTitleText(p);
    },
    //// 向下循环
    cycNext : function(photostory){
        var p = photostory;
        var pid = p.attr('pid');
        $.get('/photo/next/'+pid, function(data){
            PhotoStory.loadStory(p, data);
        }, 'json');
    },
    //// 向上循环
    cycPrev : function(photostory){
        var p = photostory;
        var pid = p.attr('pid');
        $.get('/photo/prev/'+pid, function(data){
            PhotoStory.loadStory(p, data);
        }, 'json');
    },
    //// 加载一张图片到story
    loadStory : function(photostory, data){
        var ps = photostory;
        //// 从服务器端获取的图片信息不正确，请求出错
        if(!data.success){
            return false;
        }
        var p  = data.data.photo;
        //// 如果没有下一张图片
        if(p.length <= 0){
            return false;
        }
        var c  = data.data.commenthtml;
        ps.find('.photo-comment-reply-button').attr('ruid', 0);
        ps.find('.photo-comment-reply-text').val('');
            
        ps.find('.photo-article').attr('src', p.ourl);
        ps.attr('pid', p.id);
        ps.find('.photo-desc .edit_able, .photo-desc > p').text(p.title).attr('pid', p.id);
        var pcl = ps.find('.photo-comment-list');
        pcl.replaceWith(c);
        PhotoStory.initCommentItemReplyBtn(ps);
        //alert(p.id);
        AlbumPreviewer.changeAactive(PhotoStory.getAlbumPreviewer(), p.id);
        window.location.hash = '#' + p.id;
        return data.data;
    },
    alertSuccess : function(photostory, msg){
        var p = photostory;
        p.find('.alert-success').html(msg).slideDown('2000'); //// 滑动显示提示信息
        setTimeout("$('.alert-success').slideUp('2000')",3000); //// 定时滑动隐藏信息
    },
    alertError : function(photostory, msg){
        var p = photostory;
        p.find('.alert-error').html(msg).slideDown('2000'); //// 滑动显示提示信息
        setTimeout("$('.alert-error').slideUp('2000')",5000); //// 定时滑动隐藏信息
    },
    //// 发布评论
    postComment : function(photostory){
        var p = photostory;
        var comment = $.trim(p.find('.photo-comment-reply-text').val());
        var ruid = p.find('.photo-comment-reply-button').attr('ruid');
        if(!comment){
            alert("请先填写回复信息");
            return false;
        }
        var reg = /^回复[^:]+:$/;
        if(reg.test(comment)){
            alert("请先填写回复信息");
            return false;
        }
        var pid = p.attr('pid');
        $.post('/photo/comment', {
            pid:pid,
            ruid:ruid,
            comment:comment,
            csrf_token : $('meta[name=csrf_token_value]').attr('content')
        }, function(data){
            if(!data.success){
                PhotoStory.alertError(p, data.msg);
            }else{
                p.find('.photo-comment-reply-text').val('');
                var comments = $(data.data.code);
                p.find('.photo-comment-list').prepend(comments);
                PhotoStory.initCommentItemReplyBtn(p);
            }
        }, 'json');
        return true;
    }
}
//// 新鲜事
var Fresh = {
    //// 新鲜事瀑布墙获取推给我的新鲜事
    waterfall : function(){
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
                    var offset = $('.fresh-item').length;
                    // add your append logic here
                    $.get('/home/freshhtml', {
                        offset:offset
                    }, function(data){
                        if(!data.success){
                            alert(data.msg);
                        }else if(data.data.length!=0){
                            var freshlist = $(data.data.code);
                            freshlist.hide();
                            $('.fresh-list:last').after(freshlist);
                            Fresh.initFreshList(freshlist);
                            freshlist.slideDown('3000');
                        }else{
                            has_fresh = false;
                        }
                    }, 'json');
                }
            }else{
                neg_step = false;
            }
        });
    },
    //// 新鲜事瀑布墙获取某人产生的新鲜事
    waterfallSB : function(){
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
                    var offset = $('.fresh-item').length;
                    var uid = $('.fresh-item:first').find('a').attr('uid');
                    // add your append logic here
                    $.get('/user/freshhtml', {
                        offset:offset,
                        uid:uid
                    }, function(data){
                        if(!data.success){
                            //alert(data.msg);
                        }else if(data.data.length!=0){
                            var freshlist = $(data.data.code);
                            freshlist.hide();
                            $('.fresh-list:last').after(freshlist);
                            Fresh.initFreshList(freshlist);
                            freshlist.slideDown('3000');
                        }else{
                            has_fresh = false;
                        }
                    }, 'json');
                }
            }else{
                neg_step = false;
            }
        });
    },
    //// 初始化新鲜事列表
    initFreshList : function(freshlist){
        var f = freshlist;
        f.find('.photo-publish').each(function(){
            Fresh.initPhotoPublish($(this));
        });
        f.find('.photo-reply').each(function(){
            Fresh.initPhotoReply($(this));
        });
        f.find('.trip-publish').each(function(){
            Fresh.initTripPublish($(this));
        });
    },
    initTripPublish : function(trippublish){
        
        var t = trippublish;
        t.next('.fresh-footer').find('.follow-trip-btn').click(function(){
            $.post('/trip/follow', {
                id : $(this).attr('tid'),
                csrf_token : $('meta[name=csrf_token_value]').attr('content')
            }, function(data){
                if(data.code != 0){
                    alert(data.msg);
                }else{
                    t.next('.fresh-footer').find('.follow-trip-btn').replaceWith('已关注');
                }
            }, 'json')
            return false;
        });
        t.next('.fresh-footer').find('.join-trip-btn').click(function(){
            $.post('/trip/join', {
                id : $(this).attr('tid'),
                csrf_token : $('meta[name=csrf_token_value]').attr('content')
            }, function(data){
                if(data.code != 0){
                    alert("参加失败");
                }else{
                    t.next('.fresh-footer').find('.join-trip-btn').replaceWith('已参加');
                }
            }, 'json')
            return false;
        });
    },
    //// 初始化新鲜事列表中的照片回复组件
    initPhotoReply : function(photoreplay){
        var p = photoreplay;
        PhotoStory.initPhotoStoryInFreshMode(p.find('.photo-story'));
        
    },
    //// 初始化新鲜事列表中的发布照片组件
    initPhotoPublish : function(photopublish){
        var p = photopublish;
        //// 当图片列表中的一个图片项被点动时
        p.find('.photo-item').click(function(){
            var pid  = $(this).attr('pid');
            $.get('/photo/photoStoryHtml/'+pid+'?mode=fresh', function(photostory){
                var ps = $(photostory);
                if(!ps){
                    return false;
                }
                p.find('.photo-list').after(ps);
                PhotoStory.initPhotoStoryInFreshMode(ps);
                ps.find('.photo-article').click(function(){
                    ps.remove();
                    p.find('.photo-list').show();
                });
                p.find('.photo-list').hide();  //// 隐藏图片列表
                ps.slideDown('2000');
                
                return true;
            }, 'html');
        });
    }
}

var UserComment = {
    init : function(element){
        var comment_box = element;
        var my = {};
        
        var load_more = function() {
            var type = comment_box.find('.user-comment-filter input:radio[name="type"]:checked').val();
            var from = comment_box.find('.user-comment-filter input:radio[name="from"]:checked').val();
            $.get('/user/'+comment_box.attr('uid')+'/getComment', {
                type: type,
                from: from,
                offset: comment_box.find('.user-comment-list > li').size()
            }, function(data) {
                if (data.code == 0) {
                    comment_box.find('.user-comment-list').append($(data.html));
                }
            });
        };
        
        var bind_event = function() {
            comment_box.find('.user-comment-filter input:radio').change(function() {
                comment_box.find('.user-comment-list > li').remove();
                load_more();
            });
            
            comment_box.find('a.user-comment-modal[data-toggle="modal"]').click(function() {
                var url = $(this).attr('href');
                $.get(url, function(data) {
                    if (data.code == 0) {
                        var modal = $(data.html).modal();
                        modal.find('.user-comment-post-btn').click(function() {
                            modal.find('form').ajaxForm({
                                complete: function(xhr) {
                                    var data = $.parseJSON(xhr.responseText);
                                    if (data.code == 0) {
                                        modal.modal('hide');
                                        $(data.html).prependTo(comment_box.find('.user-comment-list'));
                                    } else {
                                        alert(data.msg);
                                    }
                                }
                            }).submit();
                        });
                    } else {
                        alert(data.msg);
                    }
                });
        
                return false;
            });
            
            comment_box.find('.load-more-btn').click(function() {
                load_more();
                return false;
            });
        }
        
        bind_event();
        load_more();
        
        return my;
    }
};


var UserPageHead = {
    init: function (element) {
        var head_box = element;
        var my = {};
        
        head_box.find('.follow-user-btn, .unfollow-user-btn').click(function() {
            var uid = $(this).attr('uid');
            var follow_btn = $(this).closest('ul').find('.follow-user-btn');
            var unfollow_btn = $(this).closest('ul').find('.unfollow-user-btn');
            var action = 'follow';
            if ($(this).hasClass('unfollow-user-btn'))
                action = 'unfollow';
            
            
            $.post('/user/' + action, {
                uid : uid,
                csrf_token : $('meta[name=csrf_token_value]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    follow_btn.toggle();
                    unfollow_btn.toggle();
                } else {
                    alert(code.msg);
                }
            });
            
            return false;
        });
    
        return my;
    }
};



var UsernameLink = {
    init: function(element) {
        var link = element;
        var my = {};
        var uid = link.attr('uid');
        
        link.qtip({
            content: {
                text: '加载中...',
                ajax: {
                    url: '/user/summary/' + uid
                }
            },
            position: {
                at: 'bottom center', // Position the tooltip above the link
                my: 'top center',
                viewport: $(window), // Keep the tooltip on-screen at all times
                effect: false // Disable positioning animation
            },
            show: {
                solo: true
            },
            hide: {
                event: 'unfocus',
                solo: true
            },
            style: {
                classes: 'qtip-wiki qtip-light qtip-shadow'
            },
            events: {
                render: function(event, api) {
                //console.log($(this));
                }
            }
        });
        
        return my;
    }
};


var Trip = {
    init: function(element) {
        var trip_box = element;
        
            
        trip_box.find('.trip-action .follow-trip-btn, .trip-action .unfollow-trip-btn').click(function() {
            var trip_id = $(this).attr('tid');
            var action = 'follow';
            if ($(this).hasClass('unfollow-trip-btn')) {
                action = 'unfollow';
            }
            $.post('/trip/'+ action, {
                id : trip_id,
                csrf_token: $('meta[name=csrf_token_value]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    $('.trip-action .follow-trip-btn, .trip-action .unfollow-trip-btn').toggle();
                } else {
                    alert(data.msg);
                }
            });
            return false;
        });
    
        trip_box.find('.trip-action .join-trip-btn, .trip-action .unjoin-trip-btn').click(function() {
            var trip_id = $(this).attr('tid');
            var action = 'join';
            if ($(this).hasClass('unjoin-trip-btn')) {
                action = 'unjoin';
            }
            $.post('/trip/' + action, {
                id : trip_id,
                csrf_token: $('meta[name=csrf_token_value]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    $('.trip-action .join-trip-btn, .trip-action .unjoin-trip-btn').toggle();
                    var participants = $(data.data.participantsHtml);
                    $('.participant-list').replaceWith(participants);
                } else {
                    alert(data.msg);
                }
            });
            return false;
        });
        
        
        var comment_collapse_btn_click = function () {
            var this_comment = $(this).closest('li.comment-item');
            var children = this_comment.find('.child-list');
            this_comment.toggleClass('collapsed');
            
            $(this).text(this_comment.hasClass('collapsed') ? '展开' : '隐藏');
            
            this_comment.find('.comment-content, .reply-box').add(children).slideToggle('fast');
            
            return false;
        }
        
        var comment_reply_btn_click = function  () {
            var reply_box = $('.trip-comment .reply-box');
            reply_box.show().appendTo($(this).closest('.comment-data'));
            reply_box.find('input[name="parent_id"]').val($(this).closest('.comment-item').attr('cid'));
            reply_box.find('textarea').val('');
        }
        
        var comment_data_hover_over = function () {
            $(this).find('.toolbar').show();
        }
        var comment_data_hover_out = function () {
            $(this).find('.toolbar').hide();
        }
        
        var comment_box = trip_box.find('.trip-comment');
        
        comment_box.find('.comment-item .collapse-btn').click(comment_collapse_btn_click);
        comment_box.find('.comment-item .reply-btn').click(comment_reply_btn_click);
        comment_box.find('.comment-item .comment-data').hover(comment_data_hover_over, comment_data_hover_out);
        
        
        comment_box.find('.post-box .emotion-btn').emotion({
            target: $('.trip-comment .post-box textarea'),
            source: '/api/emotions'
        });
        
        comment_box.find('.reply-box .emotion-btn').emotion({
            target: $('.trip-comment .reply-box textarea'),
            source: '/api/emotions'
        });
        
        comment_box.find('.load-more-btn').click(function(){
            $.get('/trip/getComments', {
                tid: $('.trip-comment').attr('tid'),
                prev_id: $('.trip-comment .comment-list > li.comment-item:last').attr('cid')
            }, function(data) {
                var children = $(data).appendTo('.comment-list');
                children.find('.collapse-btn').click(comment_collapse_btn_click);
                children.find('.reply-btn').click(comment_reply_btn_click);
                children.find('.comment-data').hover(comment_data_hover_over, comment_data_hover_out);
            });
            return false;
        });
        
    }
};



var CreateTrip = {
    init: function(elem) {
        var trip_box = elem;
    
        //// check date format e.g. 2012-12-29
        //// only check format not judge empty
        var checkDateFormat = function (str){
            if(!str){
                return '';
            }
            var r = str.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/);
            if(r == null){
                return '输入日期不合法';
            }
            var d = new Date(r[1], r[3] - 1, r[4]);
            if(d.getFullYear() != r[1]){
                return '输入日期不合法';
            }
            if(d.getMonth() + 1 != r[3]){
                return '输入日期不合法';
            }
            if(d.getDate() != r[4]){
                return '输入日期不合法';
            }
            return '';
        }
        
        trip_box.find('#dst-input, #title, #start-date, #end-date, #dst-input, .trip-way-list').change(function() {
            $(this).closest('div.control-group').removeClass('error');
            $(this).nextAll('span.help-inline').text('');
        });
        
        var check_list = {
            '#title': function(element) {
                var title = $.trim(element.val());
                if (title.length < 5) {
                    element.closest('div.control-group').addClass('error');
                    element.nextAll('span.help-inline').text('标题至少5个字符');
                    return false;
                }
                return true;
            },
            /*
            '#city1': function(element) {
                var city1 = $.trim(element.val());
                if (!city1) {
                    element.closest('div.control-group').addClass('error');
                    element.nextAll('span.help-inline').text('请选择省份');
                    return false;
                }
                return true;
            },
            '#city2': function(element) {
                var city2 = $.trim(element.val());
                if (!city2) {
                    element.closest('div.control-group').addClass('error');
                    element.nextAll('span.help-inline').text('请选择城市');
                    return false;
                }
                return true;
            },*/
            '#start-date': function(element) {
                var ret = checkDateFormat(element.val());
                if (ret) {
                    element.closest('div.control-group').addClass('error');
                    element.nextAll('span.help-inline').text(ret);
                    return false;
                }
                return true;
            },
            '#dst-input': function(element) {
                dst = $.trim(element.val());
                if (!dst) {
                    element.closest('div.control-group').addClass('error');
                    element.nextAll('span.help-inline').text('请填写目的地');
                    return false;
                }
                return true;
            },
            '.trip-way-list': function(element) {
                if (element.find('input[type="checkbox"]:checked').size() <= 0) {
                    element.closest('div.control-group').addClass('error');
                    element.nextAll('span.help-inline').text('请选择旅行活动');
                    return false;
                }
                return true;
            }, 
            '#trip-cover-input': function(element) {
                if (!element.val()) {
                    alert('请上传封面图片');
                    return false;
                }
                return true;
            },
            '#editor1': function(element) {
                var content = $.trim(element.getCode());
                if(content.length < 30){
                    alert('为了让旅游更好的了解你的旅程，旅程描述字数需要大于30哦～');
                    return false;
                }
                return true;
            }
        };
        
        var check = function() {
            for (var k in check_list) {
                var element = trip_box.find(k);
                if (!check_list[k](element)) {
                    $(window).scrollTop(element.position().top - 20);
                    return false;
                }
            }
            return true;
        };
    

        trip_box.find('#submit').click(function(){
            return check();
        });
        
        trip_box.find('#city1').change(function() {
            var id = $(this).val();
            if (id == '') {
                trip_box.find('#city2 > option:gt(0)').remove();
                return;
            }
            $.get('/api/getChildCities', {
                id: id
            }, function(data) {
                trip_box.find('#city2 > option:gt(0)').remove();
                if (data.length == 0) {
                    trip_box.find('#city2').append($('<option></option>').val(trip_box.find('#city1').val()).text(trip_box.find('#city1 option:selected').text()));
                    trip_box.find('#city2 option:eq(1)').attr('selected', true);
                    return;
                }
                
                for (var i = 0; i < data.length; i++) {
                    trip_box.find('#city2').append($('<option></option>').val(data[i].id).text(data[i].name));
                }
            });
        });
        
        trip_box.find('#city1, #city2').change(function() {
            var id = trip_box.find('#city1').val();
            trip_box.find('#from-city-input').val(id);
            if (id == '') return;
            
            id = trip_box.find('#city2').val();
            if (id == '') return;
            trip_box.find('#from-city-input').val(id);
        });
        
        trip_box.find('#start-date, #end-date').datepicker({
            minDate: new Date(),
            dateFormat: "yy-mm-dd",
            onSelect: function() {
                trip_box.find('#end-date').datepicker('option', 'minDate',
                    trip_box.find('#start-date').datepicker('getDate')
                );
            }
        });

        trip_box.find('.trip-level-stars > i').click(function() {
            $(this).parent().prev('input').val($(this).index() + 1);
            $(this).nextAll('i').removeClass('star-full').addClass('star-empty');
            $(this).add($(this).prevAll('i')).removeClass('star-empty').addClass('star-full');
            
        });
    
        trip_box.find('#trip-cover-form').ajaxForm({
            complete: function(xhr) {
                var data = $.parseJSON(xhr.responseText);
                if (data.code == 0) {
                    trip_box.find('#cover-img').attr('src', data.url);
                    trip_box.find('#cover-img').attr('val', 'yes');
                } else {
                    alert(data.msg);
                }
            }
        });
        
        trip_box.find('#trip-cover-input').change(function() {
            trip_box.find('#trip-cover-form').submit();
        });
    
        trip_box.find('textarea[name="content"]').redactor({
            buttons: ['bold', 'italic', 'deleted', '|', 'orderedlist', '|', 'image', 'video', 'file', 'link', '|', 'horizontalrule'],
            imageUpload: '/trip/uploadImage',
            uploadFields: {csrf_token: $('meta[name="csrf_token_value"]').attr('content')},
            lang: 'zh_cn',
            imageGetJson: '/photo/photoList',
            minHeight: 150,
            fixed: true,
            fixedTop: 43,
            fixedBox: true
        });
        
        trip_box.find('#dst-input').tagsInput({
            autocomplete_url:'/api/dstAutocomplete',
            defaultText: '在此处添加地点',
            height: '26px',
            width: '400px'
        });
     
    }
};


var MessageDlg = {
    init: function(element) {
        element.click(function() {
            var uid = $(this).attr('uid');
            $.get('/user/messageModal/'+uid, function(data) {
                if (data.code == 0) {
                    var modal = $(data.html).modal();
                    modal.find('#message-post').click(function() {
                        modal.find('form').ajaxForm({
                            complete: function(xhr) {
                                var data = $.parseJSON(xhr.responseText);
                                if (data.code == 0) {
                                    modal.find('div.alert').text(data.msg).addClass('alert-success').show();
                                    setTimeout(function() {
                                        modal.modal('hide');
                                    }, 2000);
                                } else {
                                    modal.find('div.alert').text(data.msg).addClass('alert-error').show();
                                }
                            }
                        }).submit();
                    });
                }
            });
            return false;
        });
    }
}


var Album = {
    CreateAlbumDlg: function(element) {
        var form = element.find('form');
        
        form.find('input, textarea').focus(function() {
            $(this).next('span').text('');
            $(this).closest('.control-group').removeClass('error');
        });
        
        form.submit(function() {
            var name_input = $(this).find('input[name="name"]');
            var desc_input = $(this).find('textarea[name="description"]');
            if (!name_input.val()) {
                name_input.next('span').text('请输入相册名称');
                name_input.closest('.control-group').addClass('error');
                return false;
            }
            
            if (!desc_input.val()) {
                desc_input.next('span').text('请输入相册描述');
                desc_input.closest('.control-group').addClass('error');
                return false;
            }
            
            return true;
        });
        
        form.ajaxForm({
            complete: function(xhr) {
                var data = $.parseJSON(xhr.responseText);
                if (data.code == 0) {
                    window.location = '/album/' + data.album_id;
                } else {
                    alert(data.msg);
                }
            }
        });
    }
};


var CouchManage = {
    hostManage: function(element) {
        element.find('select[name="type"], select[name="status"]').change(function() {
            var type = element.find('select[name="type"]').val();
            var status = element.find('select[name="status"]').val();
            location.href = '/home/couchHost?type=' + type + '&status=' + status;
        });
        
        element.find('.title, .arrive-date, .leave-date, .couch-number, .status').click(function() {
            $(this).closest('.couch-surf-item').find('.detail').slideToggle('fast');
            return false;
        });
        
        element.find('.couch-surf-item .reject-btn').click(function() {
            var this_item = $(this).closest('.couch-surf-item');
            
            var reason = this_item.find('.reason-input').val();
            if (reason.length == 0) {
                this_item.find('.reason-box').show();
                return;
            } else if (reason.length <= 20) {
                alert('字数不能少于20');
                return;
            }

            $.post('/couch/reject', {
                id: this_item.attr('csid'),
                reason: reason,
                csrf_token: $('meta[name="csrf_token_value"]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    this_item.find('.reject-btn').text('已经拒绝');
                    this_item.find('.reject-btn, .accept-btn').attr('disabled', 1);
                } else {
                    alert(data.msg);
                }
            });
        });

        element.find('.couch-surf-item .accept-btn').click(function() {
            var this_item = $(this).closest('.couch-surf-item');
            
            $.post('/couch/accept', {
                id: this_item.attr('csid'),
                csrf_token: $('meta[name="csrf_token_value"]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    this_item.find('.accept-btn').text('已经接受');
                    this_item.find('.reject-btn, .accept-btn').attr('disabled', 1);
                } else {
                    alert(data.msg);
                }
            });
        });


        element.find('.couch-surf-item .cancel-btn').click(function() {
            var this_item = $(this).closest('.couch-surf-item');
            
            $.post('/couch/cancel', {
                id: this_item.attr('csid'),
                csrf_token: $('meta[name="csrf_token_value"]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    this_item.find('.cancel-btn').text('已经取消').attr('disabled', 1);
                }
            });
        });
    },
    CouchProvide: function(element) {
        element.find('.couch-available-input').change(function() {
            if ($(this).attr('checked')) {
                element.find('.detail').slideDown('fast');
            } else {
                element.find('.detail').slideUp('fast');
            }
        });
        
        element.find('textarea[name="description"]').focus(function() {
            $(this).closest('.control-group').removeClass('error')
            .find('span.help-block')
            .text('');
        });
        
        element.submit(function() {
            if (element.find('.couch-available-input').attr('checked')) {
                var desc = element.find('textarea[name="description"]').val();
                if (desc.length < 20) {
                    element.find('textarea[name="description"]').closest('.control-group').addClass('error')
                    .find('span.help-block')
                    .text('沙发描述至少20字符');
                    return false;
                }
            }
            return true;
        });
        
    },
    CouchSearch: function(element) {
        var new_dlg = element.find('#new-couch-request-dlg');
        
        new_dlg.find('input[name="leave_date"], input[name="arrive_date"], #city1, #city2, textarea[name="detail"]').focus(function() {
            $(this).closest('.control-group').removeClass('error').find('span.help-inline').text('');
        });
        
        new_dlg.find('input[name="arrive_date"]').datepicker({
            minDate: new Date(),
            dateFormat: "yy-mm-dd",
            onSelect: function() {
                new_dlg.find('input[name="leave_date"]').datepicker('option', 'minDate',
                    new_dlg.find('input[name="arrive_date"]').datepicker('getDate')
                );
            }
        });
        new_dlg.find('input[name="leave_date"]').datepicker({
            minDate: new Date(),
            dateFormat: "yy-mm-dd"
        });
        new_dlg.find('#city1').change(function() {
            var id = $(this).val();
            if (id == '') {
                new_dlg.find('#city2 > option').remove();
                return;
            }
            $.get('/api/getChildCities', {
                id: id
            }, function(data) {
                new_dlg.find('#city2 > option').remove();
                if (data.length == 0) {
                    new_dlg.find('#city2').append($('<option></option>').val(new_dlg.find('#city1').val()).text(new_dlg.find('#city1 option:selected').text()));
                    new_dlg.find('#city2 option:eq(0)').attr('selected', true);
                    return;
                }
                
                for (var i = 0; i < data.length; i++) {
                    new_dlg.find('#city2').append($('<option></option>').val(data[i].id).text(data[i].name));
                }
            });
        });
        
        new_dlg.find('.couch-search-form').submit(function() {
            var ret = true;
            
            var city = $(this).find('#city2');
            if (!city.val()) {
                city.next('span').text('请选择目的城市');
                city.closest('.control-group').addClass('error');
                ret = false;
            }
            
            var cur_date = Utils.getDate();
            
            var arrive_date = $(this).find('input[name="arrive_date"]');
            if (!arrive_date.val()) {
                arrive_date.closest('.control-group').addClass('error').find('span.help-inline').text('请选择到达日期');
                ret = false;
            }else{
                if (!Utils.isDate(arrive_date.val(), '-')){
                    arrive_date.closest('.control-group').addClass('error').find('span.help-inline').text('到达日期格式不合法');
                    ret = false;
                }
                if (Utils.dateCompare(cur_date, arrive_date.val(), '-') > 0){
                    arrive_date.closest('.control-group').addClass('error').find('span.help-inline').text('到达日期不应早于当前日期');
                    ret = false;
                }
            }
            var leave_date = $(this).find('input[name="leave_date"]');
            if (!leave_date.val()) {
                leave_date.closest('.control-group').addClass('error').find('span.help-inline').text('请选择离开日期');
                ret = false;
            }else{
                if (!Utils.isDate(leave_date.val(), '-')){
                    leave_date.closest('.control-group').addClass('error').find('span.help-inline').text('离开日期格式不合法');
                    ret = false;
                }
                
                if (Utils.dateCompare(cur_date, leave_date.val(), '-') > 0){
                    leave_date.closest('.control-group').addClass('error').find('span.help-inline').text('离开日期不应早于当前日期');
                    ret = false;
                }
            }
            
            if (Utils.dateCompare(arrive_date.val(), leave_date.val(), '-') > 0){
                leave_date.closest('.control-group').addClass('error').find('span.help-inline').text('离开日期不应早于达到日期');
                ret = false;
            }
            
            var detail = $(this).find('textarea[name="detail"]');
            
            if (detail.val().length < 20) {
                detail.closest('.control-group').addClass('error').find('span.help-inline').text('为方便沙发主了解您，请填写更多详细信息');
                ret = false;
            }
            
            return ret;
        });
        
        new_dlg.find('.couch-search-form').ajaxForm({
            complete: function(xhr) {
                var data = $.parseJSON(xhr.responseText);
                if (data.code == 0) {
                    new_dlg.find('.result-text').addClass('alert-success').show().text('发布成功');
                    $('#new-couch-request-dlg').modal('hide');
                } else {
                    alert(data.msg);
                }
            }
        });
        
        element.find('.del-btn').click(function() {
            var id = $(this).attr('cid');
            var that = $(this);
            if (confirm('是否删除该条求沙发记录？')) {
                $.post('/home/delCouchSearch', {
                    id: id,
                    csrf_token: $('meta[name="csrf_token_value"]').attr('content')
                }, function(data) {
                    if (data.code == 0) {
                        that.closest('tr').remove();
                    } else {
                        alert(data.msg);
                    }
                });
            }
        });
    },
    surfManage: function(element) {
        element.find('select[name="type"], select[name="status"]').change(function() {
            var type = element.find('select[name="type"]').val();
            var status = element.find('select[name="status"]').val();
            location.href = '/home/couchSurf?type=' + type + '&status=' + status;
        });
        
        element.find('.title, .arrive-date, .leave-date, .couch-number, .status').click(function() {
            $(this).closest('.couch-surf-item').find('.detail').slideToggle('fast');
            return false;
        });
        

        element.find('.couch-surf-item .cancel-btn').click(function() {
            var this_item = $(this).closest('.couch-surf-item');
            
            $.post('/couch/cancel', {
                id: this_item.attr('csid'),
                csrf_token: $('meta[name="csrf_token_value"]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    this_item.find('.cancel-btn').text('已经取消申请').attr('disabled', 1);
                } else {
                    alert(data.msg);
                }
            });
        });

        element.find('.couch-surf-item .reject-btn').click(function() {
            var this_item = $(this).closest('.couch-surf-item');
            
            var reason = this_item.find('.reason-input').val();
            if (reason.length == 0) {
                this_item.find('.reason-box').show();
                return;
            } else if (reason.length < 20) {
                alert('字数不能少于20');
                return;
            }

            $.post('/couch/reject', {
                id: this_item.attr('csid'),
                reason: reason,
                csrf_token: $('meta[name="csrf_token_value"]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    this_item.find('.accept-btn, .reject-btn').attr('disabled', 'disabled');
                    this_item.find('.reject-btn').text('已经拒绝');
                    this_item.find('.reason-box').hide();
                } else {
                    alert(data.msg);
                }
            });
        });

        element.find('.accept-btn').click(function() {
            var this_item = $(this).closest('.couch-surf-item');
            
            $.post('/couch/accept', {
                id: this_item.attr('csid'),
                csrf_token: $('meta[name="csrf_token_value"]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    this_item.find('.accept-btn').text('已经接受');
                    this_item.find('.reason-box').hide();
                    this_item.find('.accept-btn, .reject-btn').attr('disabled', 'disabled');
                } else {
                    alert(data.msg);
                }
            });
        });
    }
};


var SearchUser = {
    init: function(element) {
        var mp;
        
        function inviteBtnClick() {
            var id = $(this).attr('sid');
            $.get('/couch/inviteModal/' + id, function(data) {
                if (data.code == 0) {
                    var modal = $(data.html).modal().on('shown', function() {
                        var modal = $(this);
                        modal.find('input[name="leave_date"], input[name="arrive_date"]').datepicker({
                            minDate: new Date(),
                            dateFormat: "yy-mm-dd",
                            onSelect: function() {
                                modal.find('input[name="leave_date"]').datepicker('option', 'minDate',
                                    modal.find('input[name="arrive_date"]').datepicker('getDate')
                                );
                            }
                        });
                    }).on('hidden', function() {
                        modal.remove();
                    });

                    modal.find('.couch-invite-form').submit(function() {
                        var title = $(this).find('input[name="title"]');
                        var content = $(this).find('textarea[name="content"]');
                        var ret = true;
                        if (!title.val()) {
                            title.closest('.control-group').addClass('error').find('span.help-inline').text('请填写标题');
                            ret = false;
                        }
                        
                        if (!content.val()) {
                            content.closest('.control-group').addClass('error').find('span.help-inline').text('请填写详细信息，便于沙发客了解您');
                            ret = false;
                        }
                        
                        if (ret) {
                            modal.find('.send-invite-btn').attr('disabled', 'disabled');
                            modal.find('.result-box').text('').hide();
                        }
                        return ret;
                        
                    }).ajaxForm({
                        complete: function(xhr) {
                            var data = $.parseJSON(xhr.responseText);
                            if (data.code == 0) {
                                modal.find('.result-box').addClass('alert-success').text(data.msg).show();
                            } else {
                                modal.find('.result-box').addClass('alert-error').text(data.msg).show();
                                modal.find('.send-invite-btn').removeAttr('disabled');
                            }
                        }
                    });
                    
                    modal.find('.send-invite-btn').click(function() {
                        modal.find('.couch-invite-form').submit();
                    });
                } else {
                    alert(data.msg);
                }
            });
        }
    
        function load_more(clear) {
            if (clear) {
                element.find('.search-result-list > li').remove();
            }
            var search_type = element.find('input[name="search_type"]:checked').val();
            var ne = mp.getBounds().getNorthEast();
            var sw = mp.getBounds().getSouthWest();
            var sex = '';
            if (element.find('#sex-male').attr('checked') && !element.find('#sex-female').attr('checked'))
                sex = 1;
            if (!element.find('#sex-male').attr('checked') && element.find('#sex-female').attr('checked'))
                sex = 0;
            var start_age = element.find('#start_age').val();
            var end_age = element.find('#end_age').val();
            var photo = element.find('#have-photo').attr('checked') ? 1:0;
            var offset = element.find('.search-result-list > li').size();
        
            if (element.find('#search-map-area').attr('checked')) {
                $.get('/search/area', {
                    sw_lng: sw.lng,
                    sw_lat: sw.lat,
                    ne_lng: ne.lng,
                    ne_lat: ne.lat,
                    start_age: start_age,
                    end_age: end_age,
                    sex: sex,
                    photo: photo,
                    user_type: search_type,
                    offset: offset
                }, function(data) {
                    if (data.code == 0) {
                        element.find('#location-input').val(data.location);
                        mp.centerAndZoom(data.location);
                        $(data.html).appendTo(element.find('.search-result-list'))
                        .find('.invite-btn[data-toggle="modal"]').click(inviteBtnClick);
                        if (history && history.pushState) {
                            history.pushState({}, document.title, data.cond);
                        }
                    }
                });
            } else {
                $.get('/search/city', {
                    city_id: element.find('#city-id').val(),
                    start_age: start_age,
                    end_age: end_age,
                    sex: sex,
                    photo: photo,
                    user_type: search_type,
                    offset: offset
                }, function(data) {
                    if (data.code == 0) {
                        element.find('#location-input').val(data.location);
                        mp.centerAndZoom(data.location);
                        $(data.html).appendTo(element.find('.search-result-list'))
                        .find('.invite-btn[data-toggle="modal"]').click(inviteBtnClick);
                        if (history && history.pushState) {
                            history.pushState({}, document.title, data.cond);
                        }
                    }
                });
            }
        }
    
        function map_changed(type, target) {
            if (element.find('#search-map-area').attr('checked')) {
                load_more(true);
            }
        }
    
        function initializeMap() {
            mp = new BMap.Map('map');
            if (element.find('#city-id').val()) {
                mp.centerAndZoom(element.find('#location-input').val());
            } else {
                var area = element.find('#area-id');
                var sw_lng = area.attr('sw_lng');
                var sw_lat = area.attr('sw_lat');
                var ne_lng = area.attr('ne_lng');
                var ne_lat = area.attr('ne_lat');
                mp.setViewport(new BMap.Bounds(new BMap.Point(sw_lng, sw_lat), new BMap.Point(ne_lng, ne_lat)), {
                    margins:[0,0,0,0]
                });
            }
            mp.enableScrollWheelZoom();
            mp.addControl(new BMap.NavigationControl({
                type: BMAP_NAVIGATION_CONTROL_ZOOM
            }));
            mp.addEventListener('zoomend', map_changed);
            mp.addEventListener('moveend', map_changed);
        }
        
        element.find('#search-map-area').click(function() {
            if ($(this).attr('checked') == 'checked') {
                load_more(true);
            }
        });
        
        element.find('input[name="search_type"], #sex-female, #sex-male, #start_age, #end_age, #have-photo').change(function() {
            load_more(1);
        });
        
        element.find('.search-form').ajaxForm({
            complete: function(xhr) {
                var response = xhr.responseText;
                var data = $.parseJSON(response);
                if (data.code == 0) {
                    element.find('#city-id').val(data.city_id);
                    load_more(true);
                } else {
                    alert(data.msg);
                }
            }
        });
        
        element.find('.show-btn').click(function() {
            $(this).parent('div').next('div').show();
            return false;
        });
    
        element.find("#current-location-btn").click(function() {
            element.find('#search-map-area').attr('checked', false);
            $.get('/search/currentCity', function(data) {
                if (data.code == 0) {
                    element.find('#city-id').val(data.city_id);
                    load_more(true);
                } else {
                    alert(data.msg);
                }
            });
        });
    
    
        element.find('.select-city-btn').click(function() {
            element.find('.select-city-box').toggle();
            return false;
        });
    
        element.find('.city1').change(function() {
            var id = $(this).val();
            if (!id) return;
        
            element.find('.city2 > option').remove();
            $.get('/api/getChildCities', {
                id: id
            }, function(data) {
                if (data.length == 0) {
                    element.find('.city2').append($('<option></option>').val(element.find('.city1').val()).text(element.find('.city1 option:selected').text()));
                    element.find('.city2 option:eq(0)').attr('selected', true);
                }
                for (var i = 0; i < data.length; i++) {
                    element.find('.city2').append($('<option></option>').val(data[i].id).text(data[i].name));
                }
            });
        });
    
        element.find('.select-city-box .ok-btn').click(function() {
            element.find('#search-map-area').attr('checked', false);
            var pid = element.find('.select-city-box .city1').val();
            var cid = element.find('.select-city-box .city2').val();
        
            element.find('.select-city-box').hide();
            if (!cid)
                cid = pid;
            if (!cid)
                return;
            element.find('#city-id').val(cid);
            load_more(true);
        });
        element.find('.select-city-box .cancel-btn').click(function() {
            element.find('.select-city-box').hide();
        });

        element.find('.invite-btn[data-toggle="modal"]').click(inviteBtnClick);
        
        element.find('.load-more-btn').click(function() {
            load_more(false);
        });
        
        initializeMap();
    }
};


var Utils = {
    
    //// 获取当前时间
    getDate: function () {
        var myDate = new Date();
        var year   = myDate.getFullYear().toString();
        var month  = (myDate.getMonth() + 1).toString();
        var day    = myDate.getDate().toString();
        if(month.length == 1){
            month = '0' + month;
        }
        if(day.length == 1){
            day = '0' + day;
        }
        return year + '-' + month + '-' + day;
    },
    
    isDate: function (txtDate, separator) {
        var aoDate,           // needed for creating array and object
        ms,               // date in milliseconds
        month, day, year; // (integer) month, day and year
        // if separator is not defined then set '-'
        if (separator === undefined) {
            separator = '-';
        }
        // split input date to month, day and year
        aoDate = txtDate.split(separator);
        // array length should be exactly 3 (no more no less)
        if (aoDate.length !== 3) {
            return false;
        }
        // define month, day and year from array (expected format is m/d/yyyy)
        // subtraction will cast variables to integer implicitly
        month = aoDate[1] - 1; // because months in JS start from 0
        day = aoDate[2] - 0;
        year = aoDate[0] - 0;
        // test year range
        if (year < 1000 || year > 3000) {
            return false;
        }
        // convert input date to milliseconds
        ms = (new Date(year, month, day)).getTime();
        // initialize Date() object from milliseconds (reuse aoDate variable)
        aoDate = new Date();
        aoDate.setTime(ms);
        // compare input date and parts from Date() object
        // if difference exists then input date is not valid
        if (aoDate.getFullYear() !== year ||
            aoDate.getMonth() !== month ||
            aoDate.getDate() !== day) {
            return false;
        }
        // date is OK, return true
        return true;
    },
    dateCompare: function(date1, date2, separator) {
        if (!this.isDate(date1) || !this.isDate(date2))
            return -2;
        if (separator === undefined) {
            separator = '-';
        }
        
        var aoDate, day, month, year, n1, n2;
        aoDate = date1.split(separator);
        month = aoDate[1] - 1;
        day = aoDate[2] - 0;
        year = aoDate[0] - 0;
        n1 = parseInt(year.toString() + month.toString() + day.toString(), 10);
        
        aoDate = date2.split(separator);
        month = aoDate[1] - 1;
        day = aoDate[2] - 0;
        year = aoDate[0] - 0;
        n2 = parseInt(year.toString() + month.toString() + day.toString(), 10);
        
        return n1 == n2 ? 0 : (n1 > n2 ? 1 : -1);
    }
};

    

$(function() {
    'use strict';
    
    $('a.username').each(function() {
        UsernameLink.init($(this));
    });
    
    $('.message-btn[data-toggle="modal"]').each(function() {
        MessageDlg.init($(this));
    });
    
    $('a.auth').click(function() {
        if ($('#login-modal').size() > 0) {
            $('#login-modal').modal('show');
            document.cookie = 'reurl=' + $(this).attr('href');
            return false;
        }
        return true;
    });
    
    $('.pagination li.disabled>a, .pagination li.active>a').click(function() {
        return false;
    });
    
    //setInterval("TipManager.getTips()", 5000);

});

new function($) {
    $.fn.setCursorPosition = function(pos) {
        $(this).each(function() {
            if ($(this).get(0).setSelectionRange) {
                $(this).get(0).setSelectionRange(pos, pos);
            } else if ($(this).get(0).createTextRange) {
                var range = $(this).get(0).createTextRange();
                range.collapse(true);
                range.moveEnd('character', pos);
                range.moveStart('character', pos);
                range.select();
            }
        });
        return this;
    }
    
    $.fn.setCursorLastPosition = function() {
        $(this).each(function() {
            var pos = $(this).val().length;
            if ($(this).get(0).setSelectionRange) {
                $(this).get(0).setSelectionRange(pos, pos);
            } else if ($(this).get(0).createTextRange) {
                var range = $(this).get(0).createTextRange();
                range.collapse(true);
                range.moveEnd('character', pos);
                range.moveStart('character', pos);
                range.select();
            }
        });
        return this;
    }
}(jQuery);