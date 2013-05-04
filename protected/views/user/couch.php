<?php
$this->widget('UserPageHeaderWidget', array(
    'user' => $user,
)); 
$uid = intval($user['id']);
$has_couch = $couch && isset($couch['available']) && $couch['available'] == 1;

?>

<div class="row" style="margin-top: 10px;">
    <div class="span7" style="position: relative;">
        <div style="position: absolute; top: -70px;" id="tab"></div>
        <?php $this->renderPartial('userTabs', array('uid' => $uid, 'active' => 'couch')); ?>
        
        <?php if (!$has_couch) { ?>
            <h3>该用户暂未提供沙发</h3>
        <?php } else { ?>
            <p>
                <span class="muted">可容纳人数</span>
                <span class="badge badge-success"><?php echo $couch['capacity']; ?></span>
                <span class="muted">性别</span>&nbsp;<span class="badge badge-success"><?php if ($couch['guest_sex'] == 0) echo '女'; elseif ($couch['guest_sex'] == 1) echo '男'; else echo '不限'; ?></span>
                <span class="muted">是否介意吸烟</span>&nbsp;<span class="badge badge-success"><?php echo $couch['no_smoke'] ? '介意' : '不介意'; ?></span>
                <?php if (!Yii::app()->user->id) { ?>
                    <span class="muted pull-right">您还未登陆，无法申请此沙发</span>
                <?php } else if($has_pending_couch) { ?>
                    <a class="pull-right" href="/home/couchSurf">你已申请，点击查看</a>
                <?php } else if ($user['id'] == Yii::app()->user->id) {} else { ?>    
                    <button id="apply-couch-btn" style="margin-top: -10px;margin-bottom: 0px" class="btn btn-info pull-right">申请沙发</button>
                <?php } ?>
            </p>
            <?php if ( Yii::app()->user->id && !$has_pending_couch && $user['id'] != Yii::app()->user->id) { ?>
                <form id="apply-couch-form" class="form-horizontal clearfix well" style="display: none;margin-top: 20px;margin-left: 0px;" method="post" action="/user/couchRequest">
                    <input id="csrf-input" type="hidden" name="<?php echo Yii::app()->request->csrfTokenName ?>" value="<?php echo Yii::app()->request->csrfToken ?>">
                    <input type="hidden" name="uid" value="<?php echo $user['id'] ?>">
                    <div class="control-group">
                        <label class="control-label">标题:</label>
                        <div class="controls">
                            <input id="apply-couch-title" type="text" class="span5" name="title" cname="标题">
                            <p class="help help-inline" style="display:none"></p>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">附加信息:</label>
                        <div class="controls">
                            <textarea id="apply-couch-content" name="content" class="span5" rows="5" cname="内容"></textarea>
                            <p class="help help-inline" style="display:none"></p>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">到达时间:</label>
                        <div class="controls date">
                            <input id="apply-couch-arrive_date" type="text" class="input-small pull-left" name="arrive_date" cname="到达时间"> 
                            <span class="help help-inline" style="display:none"></span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">离开时间:</label>
                        <div class="controls date">
                            <input id="apply-couch-leave_date" type="text" class="input-small pull-left" name="leave_date" cname="离开时间">
                            <span class="help help-inline" style="display:none"></span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">沙发数:</label>
                        <div class="controls">
                            <select id="apply-couch-couch_number"  class="span1" name="couch_number" cname="沙发数">
                                <?php for ($i = 1; $i <= $couch['capacity']; $i++) {
                                    echo "<option value='$i'>$i</option>";
                                } ?>
                            </select>
                            <span class="help help-inline" style="display:none"></span>
                        </div>
                    </div>
                    <button id="apply-couch-submit" class="btn btn-success pull-right"          style="position:relative;top:-56px;margin-left:5px;">提交</button>
                    <button id="apply-couch-cancel" class="btn btn-danger btn-mini pull-right"  style="position:relative;top:-48px;">取消</button>
                </form>
            <?php } ?>
            <p>
                <span class="muted">沙发描述</span>&nbsp;
            </p>
            <p style="word-break: break-all">
                <?php 
                    if($couch['description']) {
                        echo CHtml::encode($couch['description']); 
                    } else {
                        echo '暂无';
                    }
                ?> 
            </p>
            <p>
                <?php if (isset($couch['album_id']) && $couch['album_id']) { ?>
                    <a href="/album/<?php echo $couch['album_id'] ?>">沙发相册</a>
                <?php } else { ?>
                    <span class="muted">沙发相册</span>
                <?php } ?>
            </p>
            <div>
                <?php 
                    if (count($photos) ==  0) {
                        echo '沙发相册暂无照片';
                    } else {
                        $i = 0;
                        foreach($photos as $photo) {
                            echo '<img style="height: 100px;max-height: 100px" src="'.$photo['surl'].'">';
                            if (++$i >= 5)
                                break;
                        }
                    }
                ?>
            </div>
        <?php } ?>
    </div>
</div>

<script>
$(function(){
    ApplyCouchForm.init($('#apply-couch-form'), $('#apply-couch-btn'));
});
</script>

<style>
    
    #apply-couch-form .control-label{
        width: 100px;
    }
    #apply-couch-form .controls{
        margin-left: 120px;
    }
</style>