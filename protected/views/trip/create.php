<div class="row">
    <div class="span10 create-trip-box" style="position: relative;">
        <h3>发起旅行计划</h3>
        <div class="trip-cover-upload">
            <form method="post" action="/trip/uploadCover" id="trip-cover-form" enctype="multipart/form-data">
                <h4>封面图片</h4>
                <div class="thumbnail">
                    <img src="http://10000km.oss.aliyuncs.com/image/photo/s/no_photo.png" id="cover-img">
                </div>
                <input type="hidden" name="<?php echo Yii::app()->request->csrfTokenName; ?>" value="<?php echo Yii::app()->request->csrfToken; ?>">

                <div class="upload-btn clearfix">
                    <span class="btn btn-success fileinput-button">
                        <span>上传封面图片</span>
                        <input type="file" name="cover" id="trip-cover-input">
                    </span>
                </div>
            </form>
        </div>

        <form class="trip-create-form" method="post">
            <?php echo Helpers::csrfInput(); ?>

            <div class="row">
                <div class="span6">
                    <label><h4>标题</h4></label>


                    <div class="control-group">
                        <div class="controls">
                            <input id="title" type="text" class="span5" name="title" autocomplete="off">
                            <span class="help-inline"></span>
                        </div>
                    </div>

                    <label><h4>出发地</h4></label>
                    <div class="control-group">
                        <div class="controls">
                            <input type="hidden" id="from-city-input" name="from_city" value="">
                            <select class="span2" id="city1">
                                <option value="">--不限--</option>
                                <?php
                                foreach ($provinces as $province) {
                                    ?>
                                    <option value="<?php echo intval($province['id']); ?>"><?php echo CHtml::encode($province['name']); ?></option>
                                <?php } ?>
                            </select>

                            <select class="span2" id="city2">
                                <option value="">--不限--</option>
                            </select>
                            <span class="help-inline"></span>
                        </div>
                    </div>

                    <label><h4>时间</h4></label>
                    <div style="position: relative;">
                        <div class="control-group">
                            <div class="controls">
                                <input autocomplete="off" type="text" class="input-small" id="start-date" name="start_date" placeholder="不限"> - <input autocomplete="off" type="text" class="input-small" id="end-date" name="end_date" placeholder="不限">
                                <span class="help-inline"></span>
                            </div>
                        </div>
                    </div>
                    <div style="position: relative">
                        <label><h4>目的地</h4></label>
                        <div class="control-group">
                            <div class="controls">
                                <input id="dst-input" type="text" name="dsts">
                                <span class="help-inline"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="row">
                <div class="span6">
                    <label><h4>活动</h4></label>
                    <div class="control-group">
                        <div class="controls">
                            <ul class="unstyled trip-way-list">
                                <?php
                                foreach ($ways as $way) {
                                    ?>
                                    <li><label class="checkbox inline"><input type="checkbox" name="trip_way[]" value="<?php echo CHtml::encode($way['id']); ?>"><?php echo CHtml::encode($way['name']); ?></label></li>
                                <?php } ?>
                            </ul>
                            <span class="help-inline"></span>
                        </div>
                    </div>
                </div>
                <div class="trip-level span3">
                    <label><h4>旅行评估</h4></label>
                    <ul class="unstyled">
                        <li class="clearfix"><label>难度</label><input type="hidden" name="difficulty_level" value="3"><div class="trip-level-stars"><i class="star-full"></i><i class="star-full"></i><i class="star-full"></i><i class="star-empty"></i><i class="star-empty"></i></div></li>
                        <li class="clearfix"><label>路程</label><input type="hidden" name="remote_level" value="3"><div class="trip-level-stars"><i class="star-full"></i><i class="star-full"></i><i class="star-full"></i><i class="star-empty"></i><i class="star-empty"></i></div></li>
                        <li class="clearfix"><label>危险度</label><input type="hidden" name="risk_level" value="3"><div class="trip-level-stars"><i class="star-full"></i><i class="star-full"></i><i class="star-full"></i><i class="star-empty"></i><i class="star-empty"></i></div></li>
                        <li class="clearfix"><label>民俗文化</label><input type="hidden" name="culture_level" value="3"><div class="trip-level-stars"><i class="star-full"></i><i class="star-full"></i><i class="star-full"></i><i class="star-empty"></i><i class="star-empty"></i></div></li>
                    </ul>
                </div>
            </div>

            <label><h4>旅行描述</h4></label>
            <textarea id="editor1" name="content"></textarea>

            <button id="submit" type="submit" class="btn btn-primary btn-large submit-btn">发起旅行计划！</button>
        </form>
    </div>
</div>



<script>
    
    $(function() {
        CreateTrip.init($('.create-trip-box'));
    });
</script>