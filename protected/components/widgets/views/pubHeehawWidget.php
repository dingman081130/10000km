<div style="margin-left: 10px; margin-bottom: 10px; width: 225px; height: 22px; background-image: url('http://img.t.sinajs.cn/t5/skin/diy/diy001/images/all_iconbtn.png?id=1348554801578'); background-position: 0 -275px;"></div>
<div style="margin: 0;border: 1px solid #CCC; border-radius: 5px; margin-left: 10px;" >
    <input id="csrf" type="hidden" name="<?php echo CHtml::encode(Yii::app()->request->csrfTokenName); ?>" value="<?php echo CHtml::encode(Yii::app()->request->csrfToken); ?>">
    <textarea name="content" id="desc" style="border: none; box-shadow: none; width: 500px; resize: vertical"></textarea>

    <div style="border-top: 1px dashed #C6CDD6; padding: 5px;" class="clearfix">
        <a title="添加表情" style="float: left; display: block; cursor: pointer;" id="emotion-btn"><span style="display: block; width: 32px; height: 32px; background: url(/img/emotion-icon.png) no-repeat;"></span></a>
        <button id="submit" class="btn btn-primary pull-right">驴叫一声</button>
    </div>
</div>