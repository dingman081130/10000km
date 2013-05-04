<div class="tab-content">
    <div class="tab-pane active">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 35%">标题</th>
                    <th style="width: 10%">回复数</th>
                    <th style="width: 15%">最后回复</th>
                    <th style="width: 25%">小组</th>
                    <th style="width: 15%">作者</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($topics as $topic) {
                    $author = User::getUser($topic['author_id']);
                    $group = Group::getGroup($topic['group_id']);
                ?>
                <tr>
                    <td><a href="/topic/<?php echo $topic['id'];?>"><?php echo CHtml::encode(Helpers::substr($topic['title'], 30)); ?></a></td>
                    <td><?php echo CHtml::encode($topic['reply_count']); ?></td>
                    <td><?php if ($topic['last_reply_time']) echo CHtml::encode(Helpers::timeDelta($topic['last_reply_time'])); else echo '-' ?></td>
                    <td><a href="/group/<?php echo $group['id']; ?>"><?php echo CHtml::encode($group['name']);?></a></td>
                    <td><a href="/user/<?php echo $author['id']; ?>"><?php echo CHtml::encode($author['name']); ?></a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>