<?php

foreach ( $groups as &$group1 ) {
    $group1['a_url'] = ImageUrlHelper::imgUrl(ImageUrlHelper::GROUP_IMAGE, $group1['image']);
}
unset($group1);
foreach ( $new_groups as &$group2 ) {
    $group2['a_url'] = ImageUrlHelper::imgUrl(ImageUrlHelper::GROUP_IMAGE, $group2['image']);
}
unset($group2);
foreach ( $hot_groups as &$group3 ) {
    $group3['a_url'] = ImageUrlHelper::imgUrl(ImageUrlHelper::GROUP_IMAGE, $group3['image']);
}
unset($group3);

?>
<div class="row">
    <div class="span8">
        <div id="group-story" class="clearfix">
            <h3>小组标签</h3>
            <ul id="tags" class="unstyled clearfix">
                <?php $label_type = $cur_category == 0 ? 'success' : 'info'; ?>
                    <li cid="0" class="label label-<?php echo $label_type; ?>" style="cursor:pointer; display: block; float: left; margin: 0 10px 8px 0;">
                        <a style="color:whitesmoke" href="/group/list?cat=0&page=1">
                        <?php echo '全部' ?>
                        </a>
                    </li>
                <?php
                    foreach ($categories as $id => $name) { 
                        $label_type = $cur_category == $id ? 'success' : 'info';
                ?>
                        <li cid="<?php echo CHtml::encode($id); ?>" class="label label-<?php echo $label_type; ?>" style="cursor:pointer; display: block; float: left; margin: 0 10px 8px 0;">
                            <a style="color:whitesmoke" href="/group/list?cat=<?php echo $id; ?>&page=1">
                            <?php echo CHtml::encode($name); ?>
                            </a>
                        </li>
                <?php } ?>
            </ul>
            
            <div id="group-list" class="clearfix">
                <?php 
                    foreach ( $groups as $group ) { 
                        $this->renderPartial('groupItem', array('group' => $group, 'categories' => $categories));
                    }
                ?>
            </div>
            <?php
                $this->widget('PaginationWidget', array(
                    'pagination' => $pagination, 
                    'base_url' => '/group/list?cat=' . $cur_category
                    ));
            ?>
        </div>
    </div>
    
    <div class="span4">
        <input id="group-search-text" type="text" style="margin-top: 10px;" placeholder="搜索小组">
        <i id="group-search-btn" class="icon-search" style="position: relative;left: -25px;cursor: pointer"></i>
        
        <div class="tableable">
            <ul class="nav nav-tabs" style="font-size:12px;">
                <li class="active"><a href="#new-group" data-toggle="tab">最新小组</a></li>
                <li><a href="#hot-group" data-toggle="tab">最热小组</a></li>
            </ul>

            <div class="tab-content">
                <div id="new-group" class="tab-pane active">
                    <?php foreach ($new_groups as $group) { ?>
                    <div style="float:left;width: 100px;height: 80px;text-align: center;overflow: hidden" title="<?php echo CHtml::encode($group['name']); ?>">
                        <a href="/group/<?php echo $group['id'] ?>">
                        <img style="width:60px;height: 60px;" src="<?php echo $group['a_url'] ?>">
                        <p style="text-align: center"><small><?php echo CHtml::encode(Helpers::substr($group['name'], 7)); ?></small></p>
                        </a>
                    </div>
                    <?php } ?>
                </div>
                <div id="hot-group" class="tab-pane">
                    <?php foreach ($hot_groups as $group) { ?>
                    <div style="float:left;width: 100px;height: 80px;text-align: center;overflow: hidden" title="<?php echo CHtml::encode($group['name']); ?>">
                        <a href="/group/<?php echo $group['id'] ?>">
                        <img style="width:60px;height: 60px;" src="<?php echo $group['a_url'] ?>">
                        <p style="text-align: center"><small><?php echo CHtml::encode(Helpers::substr($group['name'], 7)); ?></small></p>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('#group-search-btn').click(function(){
            var kw = $.trim($('#group-search-text').val());
            if(!kw){
                return false;
            }
            window.location.href = "/group/search?kw="+kw;
        });
        $('#group-search-text').keydown(function(e) {
            if (e.keyCode == 13) {
                $('#group-search-btn').trigger('click');
            }
        });
    });
</script>