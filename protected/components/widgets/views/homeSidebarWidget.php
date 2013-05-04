<div class="sidebar-nav affix" style="width: 140px;">
    <div style="width:100%; padding: 8px 0;">
        <ul class="nav nav-list"> 
            <li class="nav-header"></li>        
            <li class="<?php if ($tab == 'index') echo 'active'; ?>"><a href="/home"><i class="icon icon-home"></i> 用户中心</a></li>
            <!-- <li class="<?php if ($tab == 'blog') echo 'active'; ?>"><a href="#"><i class="icon-pencil"></i> 游记</a></li> -->
            <li class="<?php if ($tab == 'album') echo 'active'; ?>"><a href="/home/album"><i class="icon-picture"></i> 相册</a></li>
            <li class="<?php if ($tab == 'trip') echo 'active'; ?>"><a href="/home/trip"><i class="icon-road"></i> 旅行</a></li>
            <li class="">
                <a href="/home/couch" id="couch-btn"><i class="icon-leaf"></i> 沙发 <span class="badge badge-info"><?php if ($couch_request_count + $couch_invite_count) echo $couch_request_count + $couch_invite_count; ?></span></a>
                <ul class="nav nav-list" style="padding-right: 0; margin: 0 -15px; <?php if (!in_array($tab, array('couch_host', 'couch_surf', 'couch_search', 'couch_provide'))) echo 'display: none;' ?>">
                    <li class="<?php if ($tab == 'couch_host') echo 'active'; ?>"><a href="/home/couchHost" style="padding-left: 40px; padding-right: 0; margin-right: 0; font-size: 13px;">我是沙发主 <span class="badge badge-info"><?php if ($couch_request_count) echo $couch_request_count; ?></span></a></li>
                    <li class="<?php if ($tab == 'couch_surf') echo 'active'; ?>"><a href="/home/couchSurf" style="padding-left: 40px; padding-right: 0; margin-right: 0; font-size: 13px;">我是沙发客 <span class="badge badge-info"><?php if ($couch_invite_count) echo $couch_invite_count; ?></span></a></li>
                    <li class="<?php if ($tab == 'couch_search') echo 'active'; ?>"><a href="/home/couchSearch" style="padding-left: 40px; padding-right: 0; margin-right: 0; font-size: 13px;">求沙发</a></li>
                    <li class="<?php if ($tab == 'couch_provide') echo 'active'; ?>"><a href="/home/couchProvide" style="padding-left: 40px; padding-right: 0; margin-right: 0; font-size: 13px;">我的沙发</a></li>
                </ul>
            </li>
            <li class="">
                <a href="/home/myfollow" id="friend-btn"><i class="icon-heart"></i> 朋友</a>
                <ul class="nav nav-list" style="margin: 0 -15px; <?php if (!in_array($tab, array('myfollow', 'myfans'))) echo 'display: none;' ?>">
                    <li class="<?php if ($tab == 'myfollow') echo 'active'; ?>"><a href="/home/myfollow" style="padding-left: 40px; font-size: 13px;"> 我的关注</a></li>
                    <li class="<?php if ($tab == 'myfans') echo 'active'; ?>"><a href="/home/myfans" style="padding-left: 40px; font-size: 13px;"> 我的粉丝</a></li>
                </ul>
            </li>

            <li class="divider"></li>
            <li class="<?php if ($tab == 'message') echo 'active'; ?>">
                <a href="/home/message"><i class="icon-envelope"></i> 消息 <span class="badge badge-info"><?php echo $unread_cnt ?></span></a>
            </li>
            <li class="<?php if ($tab == 'personality') echo 'active'; ?>"><a href="/home/personality"><i class="icon-tags"></i> 个性设置</a></li>
            <li class="<?php if ($tab == 'settings') echo 'active'; ?>"><a href="/home/profile"><i class="icon-wrench"></i> 个人资料</a></li>
        </ul>
    </div>
</div>

<script>
    $('#couch-btn, #friend-btn').click(function() {
        $(this).next('ul').toggle();
        return false;
    });
    
</script>