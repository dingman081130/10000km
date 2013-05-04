<?php
Yii::import('application.models.User.User');
Yii::import('application.models.Group.Group');
?>
<div class="row">
    <div class="span8">
        <h3>我的旅行小组</h3>

        <ul class="nav nav-tabs">
            <li><a href="/group">我的小组话题</a></li>
            <li><a href="/group/myTopics">我发起的话题</a></li>
            <li class="active"><a href="/group/myReplies">我回复的话题</a></li>
            <li><a href="/group/myGroups">我加入的小组</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>标题</th>
                            <th>回复时间</th>
                            <th>小组</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php foreach ($topics as $topic) {
                            $author = User::getUser($topic['author_id']);
                            $group = Group::getGroup($topic['group_id']);
                        ?>
                        <tr>
                            <td><a href="/topic/<?php echo $topic['id'];?>"><?php echo CHtml::encode($topic['title']); ?></a></td>
                            <td><?php echo CHtml::encode(Helpers::timeDelta($topic['reply_time'])); ?></td>
                            <td><a href="/group/<?php echo $group['id']; ?>"><?php echo CHtml::encode($group['name']);?></a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="span4">
        <?php
        $this->renderPartial('mySidebar');
        ?>
    </div>
</div>
