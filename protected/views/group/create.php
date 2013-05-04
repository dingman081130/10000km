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
                        'name' => '新建小组',
                    ),
                )));
        ?>
        <form class="form-horizontal create-group-form" method="post" enctype="multipart/form-data">
            <legend>新建小组</legend>
            <?php echo Helpers::csrfInput(); ?>
            <div class="control-group <?php if ($form->getError('name')) echo 'error'; ?>">
                <label class="control-label">名称</label>
                <div class="controls">
                    <input type="text" name="name" value="<?php echo CHtml::encode($form['name']); ?>">
                    <span class="help-inline"><?php echo CHtml::encode($form->getError('name')); ?></span>
                </div>
            </div>
            <div class="control-group <?php if ($form->getError('category_id')) echo 'error'; ?>">
                <label class="control-label">类别</label>
                <div class="controls">
                    <select name="category_id">
                        <option value=""></option>
                        <?php
                        foreach ($categories as $id => $name) {
                            ?>
                            <option value="<?php echo intval($id); ?>" <?php if ($form['category_id'] == $id) echo 'selected'; ?>><?php echo CHtml::encode($name); ?></option>
                        <?php } ?>
                    </select>
                    <span class="help-inline"><?php echo CHtml::encode($form->getError('category_id')); ?></span>
                </div>
            </div>
                
            <?php
            $city_id = $form['city_id'];
            $up_city_id = null;
            $cities = null;
            if ($city_id) {
                $city = City::getCity($city_id);
                if ($city) {
                    $up_city_id = $city['upid'];
                    $cities = City::getChildCities($up_city_id);
                }
            }
            ?>
            <div class="control-group <?php if ($form->getError('city_id')) echo 'error'; ?>">
                <label class="control-label">所属城市</label>
                <div class="controls">
                    <select class="span2" id="city1">
                        <option value=""></option>
                        <?php
                        foreach ($provinces as $province) {
                            ?>
                            <option value="<?php echo intval($province['id']); ?>" <?php if ($up_city_id == $province['id']) echo 'selected'; ?>><?php echo CHtml::encode($province['name']); ?></option>
                        <?php } ?>
                    </select>
                    <select class="span2" id="city2" name="city_id">
                        <option value=""></option>
                        <?php if ($cities) {
                            foreach ($cities as $c) {
                         ?>
                        <option value="<?php echo $c['id']; ?>" <?php if ($c['id'] == $city_id) echo 'selected'; ?>><?php echo CHtml::encode($c['name']); ?></option>
                        <?php }} ?>
                    </select>
                    <span class="help-inline"><?php echo CHtml::encode($form->getError('city_id')); ?></span>
                </div>
            </div>
            
            <div class="control-group <?php if ($form->getError('uploaded_image')) echo 'error'; ?>">
                <label class="control-label">小组图片</label>
                <div class="controls">
                    <input type="file" name="image">
                    <span class="help-inline"><?php echo CHtml::encode($form->getError('uploaded_image')); ?></span>
                </div>
            </div>
                
            <div class="control-group <?php if ($form->getError('description')) echo 'error'; ?>">
                <label class="control-label">小组介绍</label>
                <div class="controls">
                    <textarea name="description" rows="5" class="span5"><?php echo CHtml::encode($form['description']); ?></textarea>
                    <span class="help-block"><?php echo CHtml::encode($form->getError('description')); ?></span>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">创建小组</button>
                </div>
            </div>
        </form>
    </div>
    
</div>

<script>
    $(function() {
        
        $('#city1').change(function() {
            var id = $(this).val();
            if (id == '') {
                $('#city2 > option:gt(0)').remove();
                return;
            }
            $.get('/api/getChildCities', {
                id: id
            }, function(data) {
                $('#city2 > option:gt(0)').remove();
                if (data.length == 0) {
                    $('#city2').append($('<option></option>').val($('#city1').val()).text($('#city1 option:selected').text()));
                    $('#city2 option:eq(1)').attr('selected', true);
                    return;
                }
                
                for (var i = 0; i < data.length; i++) {
                    $('#city2').append($('<option></option>').val(data[i].id).text(data[i].name));
                }
            });
        });
        
        
        $('.create-group-form').submit(function() {
            var name_input = $(this).find('input[name="name"]');
            if (!name_input.val()) {
                name_input.closest('.control-group').addClass('error').find('span.help-inline').text('请填写小组名称');
                return false;
            }
            
            var cat_select = $(this).find('select[name="category_id"]');
            if (!cat_select.val()) {
                cat_select.closest('.control-group').addClass('error').find('span.help-inline').text('请选择小组类别');
                return false;
            }
            
            var desc_input = $(this).find('textarea[name="description"]');
            if (desc_input.val().length < 20) {
                desc_input.closest('.control-group').addClass('error').find('span.help-block').text('小组介绍字数太少');
                return false;
            }
        });
        
        $('.create-group-form input, .create-group-form textarea, .create-group-form select').focus(function() {
            $(this).closest('.control-group').removeClass('error').find('span.help-inline, span.help-block').text('');
        });
    });
</script>