<?php
Yii::import('application.models.AR.SocialAccountAR');
?>
<div class="row">
    <h4>今日注册用户数：<?php echo count($users); ?>  用户总数：<?php echo intval($total);?></h4>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>用户名</th>
                <th>注册时间</th>
                <th>注册来源</th>
            </tr>
        </thead>
        
        <tbody>
            <?php foreach ($users as $user) { ?>
            <tr>
                <td><a target="_blank" href="/user/<?php echo $user['id']; ?>"><?php echo CHtml::encode($user['name']); ?></a></td>
                <td><?php echo $user['register_time'];?></td>
                <td>
                    <?php 
                        $sa = SocialAccountAR::model()->findByAttributes(array(
                            'user_id' => $user['id'],
                        ));
                        if ($sa) {
                            $type = $sa['type'];
                            if ($type == SocialAccountAR::TYPE_RENREN) echo '人人网';
                            if ($type == SocialAccountAR::TYPE_WEIBO) echo '新浪微博';
                            if ($type == SocialAccountAR::TYPE_QQ) echo 'QQ';
                            if ($type == SocialAccountAR::TYPE_DOUBAN) echo '豆瓣';
                        }
                    ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>