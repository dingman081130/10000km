<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'couch_surf',
        ));
        ?>
    </div>

    <div class="span10 couch-surf-log">
        <h4>我是沙发客</h4>
        
        类型：
        <select name="type" class="span2">
            <option value="">所有</option>
            <option value="<?php echo CouchSurfAR::TYPE_SURF_REQUEST_HOST; ?>" <?php if ($type == CouchSurfAR::TYPE_SURF_REQUEST_HOST) echo 'selected'; ?>>我申请的沙发</option>
            <option value="<?php echo CouchSurfAR::TYPE_HOST_INVITE_SURF; ?>" <?php if ($type == CouchSurfAR::TYPE_HOST_INVITE_SURF) echo 'selected'; ?>>我收到的邀请</option>
        </select>

        &nbsp;&nbsp;&nbsp;状态：
        <select name="status" class="span2">
            <option value="">所有</option>
            <option value="<?php echo CouchSurfAR::STATUS_PEDDING; ?>" <?php if ($status == CouchSurfAR::STATUS_PEDDING) echo 'selected'; ?>>等待中</option>
            <option value="<?php echo CouchSurfAR::STATUS_ACCEPED; ?>" <?php if ($status == CouchSurfAR::STATUS_ACCEPED) echo 'selected'; ?>>已经接受</option>
            <option value="<?php echo CouchSurfAR::STATUS_CANCELED; ?>" <?php if ($status == CouchSurfAR::STATUS_CANCELED) echo 'selected'; ?>>已经取消</option>
            <option value="<?php echo CouchSurfAR::STATUS_REJECTED; ?>" <?php if ($status == CouchSurfAR::STATUS_REJECTED) echo 'selected'; ?>>已经拒绝</option>
        </select>

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
            foreach ($surfs as $surf) {
                $type = $surf['type'];
                $status = $surf['status'];
            ?>
            <li class="couch-surf-item" style="margin-top: 10px; cursor: pointer;" csid="<?php echo $surf['id']; ?>">
                <div>
                    <span class="host"><a href="/user/<?php echo $surf['host_id']; ?>"><?php echo CHtml::encode($surf['host']['name']); ?></a></span>
                    <span class="title"><?php echo CHtml::encode($surf['title']); ?></span>
                    <span class="arrive-date"><?php echo CHtml::encode($surf['arrive_date']); ?></span>
                    <span class="leave-date"><?php echo CHtml::encode($surf['leave_date']); ?></span>
                    <span class="couch-number"><?php echo $surf['couch_number']; ?></span>
                    <span class="status">
                        <?php
                        if ($type == CouchSurfAR::TYPE_HOST_INVITE_SURF) {
                            if ($status == CouchSurfAR::STATUS_PEDDING) {
                                echo '<span class="label label-warning">等待我接受</span>';
                            } else if ($status == CouchSurfAR::STATUS_ACCEPED) {
                                echo '<span class="label label-success">我已经接受</span>';
                            } else if ($status == CouchSurfAR::STATUS_CANCELED) {
                                echo '<span class="label">沙发主已经取消邀请</span>';
                            } else if ($status == CouchSurfAR::STATUS_REJECTED) {
                                echo '<span class="label label-important">我已经拒绝沙发主的邀请</span>';
                            }
                        } else if ($type == CouchSurfAR::TYPE_SURF_REQUEST_HOST) {
                            if ($status == CouchSurfAR::STATUS_PEDDING) {
                                echo '<span class="label label-warning">等待沙发主接受</span>';
                            } else if ($status == CouchSurfAR::STATUS_ACCEPED) {
                                echo '<span class="label label-success">沙发主已经接受</span>';
                            } else if ($status == CouchSurfAR::STATUS_REJECTED) {
                                echo '<span class="label label-important">沙发主已经拒绝</span>';
                            } else if ($status == CouchSurfAR::STATUS_CANCELED) {
                                echo '<span class="label">我已经取消申请</span>';
                            }
                        }
                        ?>
                    </span>
                </div>
                <div class="hide well detail" style="margin-top:10px;">
                    <p>发送时间：<?php echo CHtml::encode($surf['create_time']); ?></p>
                    <p>附加内容：<?php echo CHtml::encode($surf['content']); ?></p>
                    
                    <div class="hide reason-box">
                        <p>拒绝理由：</p>
                        <textarea style="width: 97%;" class="reason-input"></textarea>
                    </div>
                        
                    <div class="clearfix action-box">
                        <ul class="unstyled pull-right inline">
                            <?php if ($type == CouchSurfAR::TYPE_SURF_REQUEST_HOST && $status == CouchSurfAR::STATUS_PEDDING) {?>
                            <li><button class="btn btn-primary btn-small cancel-btn">取消</button></li>
                            <?php } ?>
                            <?php if ($type == CouchSurfAR::TYPE_HOST_INVITE_SURF && $status == CouchSurfAR::STATUS_PEDDING) { ?>
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
        CouchManage.surfManage($('.couch-surf-log'));
    });
</script>
