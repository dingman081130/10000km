<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'couch_search',
        ));
        ?>
    </div>

    <div class="span10" id="couch-search-box">
        <div class="row">
            <div class="clearfix span5">
                <div class="pull-left">
                    <h4>我的求沙发信息</h4>
                </div>
                <div class="pull-left" style="margin-left: 20px">
                    <button class="btn btn-small btn-info" data-toggle="modal" data-target="#new-couch-request-dlg" style="margin-top: 7px;">发布求沙发信息</button>
                </div>
            </div>


            <div class="modal hide fade" id="new-couch-request-dlg">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>发布求沙发信息</h3>
                </div>
                <div class="modal-body">
                    <div class="alert hide result-text">
                    </div>
                    <form class="form-horizontal couch-search-form" method="post" style="margin-left: -60px;">
                        <?php echo Helpers::csrfInput(); ?>
                        <div class="control-group">
                            <label class="control-label">城市</label>
                            <div class="controls">
                                <select style="width: 100px;" id="city1">
                                    <option value=""></option>
                                    <?php foreach ($provinces as $province) { ?>
                                        <option value="<?php echo $province['id']; ?>"><?php echo CHtml::encode($province['name']); ?></option>
                                    <?php } ?>
                                </select>
                                <select style="width: 100px;" id="city2" name="city_id">
                                    <option value=""></option>
                                </select>
                                <span class="help-inline"></span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">到达日期</label>
                            <div class="controls">
                                <input class="span2" type="text" name="arrive_date" autocomplete="off">
                                <span class="help-inline"></span>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">离开日期</label>
                            <div class="controls">
                                <input class="span2" type="text" name="leave_date" autocomplete="off">
                                <span class="help-inline"></span>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">人数</label>
                            <div class="controls">
                                <select class="span1" name="number">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6+</option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">附加信息</label>
                            <div class="controls">
                                <textarea rows="5" style="width: 300px;" name="detail"></textarea>
                                <span class="help-inline"></span>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-primary publish-btn">发布</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 30px;">城市</th>
                    <th style="width: 30px;">发布时间</th>
                    <th style="width: 30px;">到达时间</th>
                    <th style="width: 30px;">离开时间</th>
                    <th style="width: 30px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($couch_search as $one) {
                    ?>    

                    <tr>
                        <td><?php echo CHtml::encode(Helpers::cityName($one['city_id'])); ?></td>
                        <td><?php echo Helpers::friendlyTime($one['create_time']); ?></td>
                        <td><?php echo $one['arrive_date']; ?></td>
                        <td><?php echo $one['leave_date']; ?></td>
                        <td><button class="btn btn-link btn-small del-btn" cid="<?php echo $one['id']; ?>">删除</button></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>


<script>
    $(function() {
        CouchManage.CouchSearch($('#couch-search-box'));
    });
    
</script>
