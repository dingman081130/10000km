<?php
Yii::import('application.models.User.User');
Yii::import('application.models.Group.GroupUser');

$ijoin = GroupUser::isUserJoinGroup(Yii::app()->user->id, $group['id']);
$creator = User::getUser($group['creator_id']);
$user_count = count($users);

?>
<div class="row">
    <div class="span8">
        <?php 
        $this->widget('BreadCrumbWidget', 
                array('crumbs' => array(
                    array(
                        'name'=> '小组',
                        'url' => array('/group'),
                    ),
                    array(
                        'name' => $group['name'],
                        'url'  => array('/group/'.$group['id']),
                    ),
                )));
        ?>
        <div style="position: relative;">
            <h4><?php echo CHtml::encode($group['name']); ?> </h4>
            <a style="position: absolute; right: 0; top: 0;" href="/group/feed/<?php echo $group['id']; ?>"><img src="/img/rss-icon.png"></a>
        </div>

        <div class="well clearfix">
            <p><a href="/user/<?php echo $creator['id']; ?>"><?php echo CHtml::encode($creator['name']); ?></a> 创建于 <?php echo Helpers::friendlyDate($group['create_time']); ?></p>
            <p>
                <?php echo CHtml::encode($group['description']); ?>
            </p>
            <?php if (Yii::app()->user->id) { ?>
                <?php if (Yii::app()->user->id != $group['creator_id']) { ?>
                    <button class="btn btn-small pull-right unjoin-btn <?php if (!$ijoin) echo 'hide'; ?>" gid="<?php echo $group['id']; ?>">退出小组</button>
                <?php } ?>
            <button class="btn btn-small btn-info pull-right join-btn <?php if ($ijoin) echo 'hide'; ?>" gid="<?php echo $group['id']; ?>">加入小组</button>
            <?php } ?>
        </div>

        <table  class="table table-hover">
            <thead>
                <tr>
                    <th>标题</th>
                    <th>作者</th>
                    <th>回复数</th>
                    <th>发布时间</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($topics as $topic) {
                    $author = User::getUser($topic['author_id']);
                    ?>
                    <tr>
                        <td><a href="/topic/<?php echo $topic['id']; ?>"><?php echo CHtml::encode($topic['title']); ?></a></td>
                        <td><a href="/user/<?php echo $author['id']; ?>"><?php echo CHtml::encode($author['name']); ?></a></td>
                        <td><?php echo $topic['reply_count']; ?></td>
                        <td><?php echo Helpers::friendlyTime($topic['create_time']); ?></td>
                    </tr>
<?php } ?>
            </tbody>
        </table>
        
        <div>
            <?php
            $pagination = array(
                'cur' => $page,
                'page_cnt' => $page_count,
            );
            $this->widget('PaginationWidget', array(
                'pagination' => $pagination,
                'base_url' => '/group/'.$group['id'],
            ));
            ?>
        </div>
    </div>

    <div class="span4">
        <div style="margin: 10px 0;">
            <?php if(Yii::app()->user->id) {?>
            <a class="create-topic-btn btn btn-primary <?php if (!$ijoin) echo 'hide'; ?>" gid="<?php echo $group['id']; ?>" href="/topic/create/<?php echo intval($group['id']); ?>"><i class="icon-plus icon-white"></i> 发表新话题</a>
            <?php }?>
        </div>
<!--
        <div class="input-append">
            <input type="text" name="" placeholder="搜索本小组的话题"><span class="add-on"><i class="icon-search"></i></span>
        </div>
-->
        <div>
            <h5>小组成员(<a href="/group/users/<?php echo $group['id']; ?>"><?php echo $user_count; ?></a>)</h5>
            <ul id="member-list" class="unstyled inline">
                <?php
                foreach ($users as $user) { 
                    $this->renderPartial('groupMember', array('user' => $user));
                }
                ?>
            </ul>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('.join-btn').click(function() {
            var id = $(this).attr('gid');
            var that = $(this);
            $.post('/group/join/'+id, {
                csrf_token: $('meta[name=csrf_token_value]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    $('.unjoin-btn').show();
                    that.hide();
                    $('.create-topic-btn').show();
                    var new_member = $(data.data.user_html);
                    $('#member-list').prepend(new_member);
                    
                } else {
                    alert(data.msg);
                }
            });
        });
        $('.unjoin-btn').click(function() {
            var id = $(this).attr('gid');
            var that = $(this);
            $.post('/group/unjoin/'+id, {
                csrf_token: $('meta[name=csrf_token_value]').attr('content')
            }, function(data) {
                if (data.code == 0) {
                    $('.join-btn').show();
                    that.hide();
                    $('.create-topic-btn').hide();
                    $('li[uid="' + data.data.user + '"]').remove();
                } else {
                    alert(data.msg);
                }
            });
        });
    });
</script>