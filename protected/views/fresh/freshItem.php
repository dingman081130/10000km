 <?php
/**
 * @file class 渲染单条新鲜事
 * @package application.components.widgets
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-14
 * @version
 */

$user = User::getUser($fresh['user_id']);
!isset($fresh['uid'])    && $fresh['uid']    = $fresh['user_id'];
!isset($fresh['uname'])  && $fresh['uname']  = $user['name'];
!isset($fresh['usex'])   && $fresh['usex']   = $user['sex'];
!isset($fresh['avatar']) && $fresh['avatar'] = ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $user['avatar']);
!isset($fresh['ftime'])  && $fresh['ftime']  = Helpers::friendlyTime($fresh['create_time']);

?>
<li class="fresh-item clearfix" ftype="<?php echo CHtml::encode($fresh['type']);?>">
    <div class="fresh-avatar">
        <a uid="<?php echo $fresh['user_id'] ?>" href="/user/<?php echo $fresh['user_id']; ?>">
            <img class="img-polaroid" src="<?php echo $fresh['avatar']; ?>">
        </a>
    </div>
    <div class="fresh-data clearfix">
        <?php
            $view_name = 'freshData'.ucfirst($fresh['type']);

            $this->renderView(array('fresh',$view_name), array('fresh' => $fresh));
        ?>
    </div>
</li>
