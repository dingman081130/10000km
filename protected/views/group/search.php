<?php
Yii::import('application.models.User.User');
Yii::import('application.models.Group.Topic');
foreach ( $groups as &$group ) {
    $group['a_url'] = ImageUrlHelper::imgUrl(ImageUrlHelper::GROUP_IMAGE, $group['image']);
    $group['creator'] = User::getUser($group['creator_id']);
}
?>

<div class="row">
    <div class="span12">
        <div class="group-search-bar" style="text-align: center;margin-top: 30px">
            <h3>还有哪些小组 ?</h3>
            <input id="group-search-text" placeholder=" 搜一下吧" style="width: 300px;height: 26px" value="<?php echo CHtml::encode($kw); ?>">
            <i id="group-search-btn" class="icon-search" style="position: relative;left: -25px;cursor: pointer"></i>
        </div>
        
        <div class="clearfix" style="margin-top: 50px">
            <?php foreach ( $groups as $group ) { ?>
                <div style="float:left;width: 800px;padding-bottom: 10px;margin-right: 10px;margin-top: 10px;border-bottom:  grey solid 1px;">
                    <div class="avatar pull-left" style="width:60px;height: 60px">
                        <a href="/group/<?php echo CHtml::encode($group['id']); ?>">
                        <img src="<?php echo CHtml::encode($group['a_url']); ?>">
                        </a>
                    </div>
                    <div class="data" style="margin-left:65px;">
                        <div class="header">
                            <a href="/group/<?php echo CHtml::encode($group['id']); ?>"><?php echo CHtml::encode(Utils::tripDescStriper($group['name'], 30)); ?></a>
                            <small class="pull-right muted" style="margin-left:10px;">
                                <?php echo CHtml::encode($group['user_count']); ?>个成员
                            </small>
                            <small class="pull-right" style="margin-left:10px;">
                                <a href="/group/<?php echo CHtml::encode($group['id']); ?>">
                                <?php echo CHtml::encode($group['topic_count']); ?>个话题
                                </a>
                            </small>
                        </div>
                        <div class="body">
                            <small><?php echo CHtml::encode(Utils::tripDescStriper($group['description'], 1000)); ?></small>
                        </div>
                        <div class="footer">
                            <?php
                                $cid = $group['category_id'];
                                $cname = isset($categories[$cid]) ? $categories[$cid] : '';
                                if($cname){
                            ?>
                                <span cid="<?php echo CHtml::encode($cid); ?>" class="label label-info"><?php echo $cname; ?></span>
                            <?php } ?>
                                <small class="pull-right muted" style="margin-left: 10px;"><?php echo Helpers::friendlyTime($group['create_time']); ?></small>
                                <small class="pull-right" style="margin-left: 10px;">
                                    <a href="/user/<?php echo $group['creator']['id']; ?>">
                                        <?php echo CHtml::encode($group['creator']['name']); ?>
                                    </a>
                                </small>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php
            $this->widget('PaginationWidget', array(
                'pagination' => $pagination, 
                'base_url' => '/group/search/?&kw=' . $kw
                ));
        ?>
    </div>
</div>

<script>
    $(function(){
        $('#group-search-btn').click(function(){
            var kw = $.trim($('#group-search-text').val());
            /*
            if(!kw){
                return false;
            }*/
            window.location.href = "/group/search?kw="+kw;
        });
        $('#group-search-text').keydown(function(e) {
            if (e.keyCode == 13) {
                $('#group-search-btn').trigger('click');
            }
        });
    });
</script>