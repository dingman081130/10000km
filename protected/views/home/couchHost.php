<?php
Yii::import('application.models.AR.CouchSurfAR');
?>
<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'couch_host',
        ));
        ?>
    </div>

    <div class="span10 couch-surf-log">
        <div>
            <h4>我是沙发主</h4>
            类型：
            <select name="type" class="span2">
                <option value="">所有</option>
                <option value="<?php echo CouchSurfAR::TYPE_SURF_REQUEST_HOST; ?>" <?php if ($type == CouchSurfAR::TYPE_SURF_REQUEST_HOST) echo 'selected'; ?>>我收到的申请</option>
                <option value="<?php echo CouchSurfAR::TYPE_HOST_INVITE_SURF; ?>" <?php if ($type == CouchSurfAR::TYPE_HOST_INVITE_SURF) echo 'selected'; ?>>我发出的邀请</option>
            </select>
            
            &nbsp;&nbsp;&nbsp;状态：
            <select name="status" class="span2">
                <option value="">所有</option>
                <option value="<?php echo CouchSurfAR::STATUS_PEDDING; ?>" <?php if ($status == CouchSurfAR::STATUS_PEDDING) echo 'selected'; ?>>等待中</option>
                <option value="<?php echo CouchSurfAR::STATUS_ACCEPED; ?>" <?php if ($status == CouchSurfAR::STATUS_ACCEPED) echo 'selected'; ?>>已经接受</option>
                <option value="<?php echo CouchSurfAR::STATUS_CANCELED; ?>" <?php if ($status == CouchSurfAR::STATUS_CANCELED) echo 'selected'; ?>>已经取消</option>
                <option value="<?php echo CouchSurfAR::STATUS_REJECTED; ?>" <?php if ($status == CouchSurfAR::STATUS_REJECTED) echo 'selected'; ?>>已经拒绝</option>
            </select>
        </div>
        
        <div class="header">
            <span class="host">沙发主</span>
            <span class="title">标题</span>
            <span class="arrive-date">到达时间</span>
            <span class="leave-date">离开时间</span>
            <span class="couch-number">人数</span>
            <span class="status">状态</span>
        </div>
        
        <ul class="unstyled couch-surf-list">
            <?php
            foreach ($hosts as $host) {
                $type = $host['type'];
                $status = $host['status'];
            ?>
            <li class="couch-surf-item" style="margin-top: 10px; cursor: pointer;" csid="<?php echo $host['id']; ?>">
                <div>
                    <span class="host"><a href="/user/<?php echo $host['surf_id']; ?>"><?php echo CHtml::encode($host['surf']['name']); ?></a></span>
                    <span class="title"><?php echo CHtml::encode($host['title']); ?></span>
                    <span class="arrive-date"><?php echo CHtml::encode($host['arrive_date']); ?></span>
                    <span class="leave-date"><?php echo CHtml::encode($host['leave_date']); ?></span>
                    <span class="couch-number"><?php echo $host['couch_number']; ?></span>
                    <span class="status">
                        <?php
                        if ($type == CouchSurfAR::TYPE_HOST_INVITE_SURF) {
                            if ($status == CouchSurfAR::STATUS_PEDDING) {
                                echo '<span class="label label-warning">已经发送邀请，等待沙发客接受</span>';
                            } else if ($status == CouchSurfAR::STATUS_ACCEPED) {
                                echo '<span class="label label-success">沙发客已经接受你的邀请</span>';
                            } else if ($status == CouchSurfAR::STATUS_CANCELED) {
                                echo '<span class="label">我已经取消邀请</span>';
                            } else if ($status == CouchSurfAR::STATUS_REJECTED) {
                                echo '<span class="label label-important">沙发客已经拒绝您的邀请</span>';
                            }
                        } else if ($type == CouchSurfAR::TYPE_SURF_REQUEST_HOST) {
                            if ($status == CouchSurfAR::STATUS_PEDDING) {
                                echo '<span class="label label-warning">等待我接受</span>';
                            } else if ($status == CouchSurfAR::STATUS_ACCEPED) {
                                echo '<span class="label label-success">我已经接受</span>';
                            } else if ($status == CouchSurfAR::STATUS_REJECTED) {
                                echo '<span class="label label-important">已经拒绝</span>';
                            } else if ($status == CouchSurfAR::STATUS_CANCELED) {
                                echo '<span class="label">沙发客已经取消申请</span>';
                            }
                        }
                        ?>
                    </span>
                </div>
                <div class="hide well detail" style="margin-top:10px;">
                    <p>发送时间：<?php echo CHtml::encode($host['create_time']); ?></p>
                    <p>附加内容：<?php echo CHtml::encode($host['content']); ?></p>
                    
                    <div class="hide reason-box">
                        <p>拒绝理由：</p>
                        <textarea style="width: 97%;" class="reason-input"></textarea>
                    </div>
                        
                    <div class="clearfix action-box">
                        <ul class="unstyled pull-right inline">
                            <?php if ($type == CouchSurfAR::TYPE_HOST_INVITE_SURF && $status == CouchSurfAR::STATUS_PEDDING) {?>
                            <li><button class="btn btn-primary btn-small cancel-btn">取消</button></li>
                            <?php } ?>
                            <?php if ($type == CouchSurfAR::TYPE_SURF_REQUEST_HOST && $status == CouchSurfAR::STATUS_PEDDING) { ?>
                            <li><button class="btn btn-primary btn-small accept-btn">接受</button></li>
                            <li><button class="btn btn-danger btn-small reject-btn">拒绝</button></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </li>
            <?php } ?>
        </ul>
    </div>

</div>

<script>
    $(function() {
       CouchManage.hostManage($('.couch-surf-log'));
    });
</script>
