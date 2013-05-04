<?php
Yii::import('application.models.User.UserFollow');
?>
<div class="row">
    <div class="span8 trip">

        <h3><?php echo CHtml::encode($trip['title']); ?></h3>

        <div class="trip-head">
            <p class="muted">发布时间: <time datetime="<?php echo CHtml::encode($trip['create_time']); ?>"><?php echo Helpers::friendlyTime($trip['create_time']); ?></time></p> 

            <div style="position: absolute; right: 0; top: 0;">

                <!-- JiaThis Button BEGIN -->
                <div class="jiathis_style">
                <a class="jiathis_button_qzone"></a>
                <a class="jiathis_button_tsina"></a>
                <a class="jiathis_button_tqq"></a>
                <a class="jiathis_button_renren"></a>
                <a class="jiathis_button_kaixin001"></a>
                <a href="http://www.jiathis.com/share?uid=1736140" class="jiathis jiathis_txt jiathis_separator jtico jtico_jiathis" target="_blank"></a>
                <a class="jiathis_counter_style"></a>
                </div>
                <script type="text/javascript" >
                var jiathis_config={
                        data_track_clickback:true,
                        summary:"",
                        ralateuid:{
                                "tsina":"3168359687"
                        },
                        appkey:{
                                "tsina":"174356130",
                                "tqq":"100353422"
                        },
                        hideMore:false,
                        pic: <?php echo json_encode($cover); ?>
                }
                </script>
                <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js?uid=1736140" charset="utf-8"></script>
                <!-- JiaThis Button END -->
            </div>
        </div>


        <div class="row trip-detail">
            <div class="span3">
                <img src="<?php echo CHtml::encode($cover); ?>">
            </div>
            <div class="span5">
                <dl class="dl-horizontal">
                    <dt>旅行时间：</dt>
                    <dd><?php echo $trip['start_date'] == '0000-00-00' ? '不限':Helpers::friendlyDate($trip['start_date']); ?> 至 <?php echo $trip['end_date'] == '0000-00-00' ? '不限':Helpers::friendlyDate($trip['start_date']); ?></dd>

                    <dt>出发地：</dt>
                    <dd>
                        <?php
                        if (!$from_city) {
                            echo '不限';
                        } else {
                            if ($from_city['up_city'])
                                echo $from_city['up_city']['name'] . ' ';
                            echo $from_city['name'];
                        }
                        ?>
                    </dd>

                    <dt>目的地：</dt>
                    <dd>
                        <?php
                        foreach ($trip_dsts as $dst) {
                            echo CHtml::encode($dst);
                            echo '&nbsp';
                        }
                        ?>
                    </dd>

                    <dt>旅行活动：</dt>
                    <?php
                    $str = '';
                    foreach ($trip_ways as $way_id => $way_name) {
                        $str .= $way_name . ' ';
                    }
                    ?>
                    <dd title="<?php echo CHtml::encode($str); ?>">
                        <?php
                        echo CHtml::encode(Helpers::substr($str, 24, true));
                        ?>
                    </dd>


                </dl>

            </div>
            <div>
                <ul class="unstyled pull-right trip-action">
                    <li style="display: inline-block"><a tid="<?php echo $trip['id']; ?>" href="#" class="btn follow-trip-btn <?php if (!Yii::app()->user->id || $ifollow) echo 'hide'; ?>">+关注</a></li>
                    <li style="display: inline-block"><a tid="<?php echo $trip['id']; ?>" href="#" class="btn unfollow-trip-btn <?php if (!Yii::app()->user->id || !$ifollow) echo 'hide'; ?>">取消关注</a></li>
                    <li style="display: inline-block"><a tid="<?php echo $trip['id']; ?>" href="#" class="btn btn-primary join-trip-btn <?php if (!Yii::app()->user->id || $ijoin) echo 'hide'; ?>">我要参加！</a></li>
                    <li style="display: inline-block"><a tid="<?php echo $trip['id']; ?>" href="#" class="btn unjoin-trip-btn <?php if (!Yii::app()->user->id || !$ijoin) echo 'hide'; ?>">取消参加</a></li>
                </ul>
            </div>

        </div>


        <div class="trip-desc" style="margin-top: 20px;">
            <h4>旅行描述</h4>
            <div style="word-break: break-all;">
                <?php echo $trip['content']; ?>
            </div>
        </div>

        <div class="trip-comment" tid="<?php echo $trip['id']; ?>">
            <h4 class="comment-head"><?php echo $threaded_comments->getCount(); ?>条评论</h4>
            <?php if (Yii::app()->user->id) { ?>
                <div class="post-box">
                    <div class="avatar">
                        <img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_TINY, Yii::app()->user->attrs['avatar']); ?>">
                    </div>

                    <div class="content-area">
                        <form action="/trip/comment/<?php echo intval($trip['id']); ?>" method="post">
                            <input type="hidden" name="<?php echo CHtml::encode(Yii::app()->request->csrfTokenName); ?>" value="<?php echo CHtml::encode(Yii::app()->request->csrfToken); ?>">
                            <input type="hidden" name="parent_id" value="0">
                            <textarea name="content"></textarea>

                            <div class="action-bar clearfix">
                                <a title="添加表情" class="emotion-btn">
                                    <span></span>
                                </a>
                                <button class="btn btn-primary pull-right">发 表</button>
                            </div>
                        </form>
                    </div>

                </div>
            <?php } ?>

            <div class="reply-box">
                <form action="/trip/comment/<?php echo intval($trip['id']); ?>" method="post">
                    <input type="hidden" name="<?php echo CHtml::encode(Yii::app()->request->csrfTokenName); ?>" value="<?php echo CHtml::encode(Yii::app()->request->csrfToken); ?>">
                    <input type="hidden" name="parent_id" value="0">
                    <textarea name="content"></textarea>

                    <div class="action-bar clearfix">
                        <a title="添加表情" class="emotion-btn">
                            <span></span>
                        </a>
                        <button class="btn btn-primary pull-right">发 表</button>
                    </div>
                </form>
            </div>

            <ul class="unstyled comment-list">
                <?php
                $parents = $threaded_comments->getParents();
                $parents_id = array_keys($parents);
                rsort($parents_id);
                $s = 0;
                foreach ($parents_id as $parent_id) {
                    if ($s == $comments_limit)
                        break;

                    if ($comments_prev != 0 && $parent_id >= $comments_prev)
                        continue;

                    $this->renderPartial('commentItem', array(
                        'comment' => $parents[$parent_id],
                        'threaded_comments' => $threaded_comments,
                    ));
                    $s++;
                }
                ?>
            </ul>

            <div>
                <button class="btn load-more-btn" style="width: 100%;">载入更多</button>
            </div>
        </div>

    </div>

    <div class="span4">

        <div>

            <div class="clearfix">
                <h4 style="margin-top: 20px; border-bottom: 1px solid #DDD; padding-bottom: 6px; ">组织者</h4>
                <img style="border: 1px solid #666; height: 60px;" class="pull-left" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, $creator['avatar']); ?>">
                <div style="margin-left: 80px;">
                    <p style="margin-bottom: 0;"><a href="/user/<?php echo intval($creator['id']); ?>"><?php echo CHtml::encode($creator['name']); ?></a></p>
                    <p style="margin-bottom: 0;">粉丝:<span class="badge badge-info"><?php echo UserFollow::getUserFansCount($creator['id']); ?></span></p>
                    <p style="margin-bottom: 0;">关注:<span class="badge badge-info"><?php echo UserFollow::getUserFollowCount($creator['id']); ?></span></p>

                </div>
            </div>

            <div class="trip-level">
                <h4 style="margin-top: 20px; border-bottom: 1px solid #DDD; padding-bottom: 6px; ">旅行评估</h4>
                <ul class="unstyled">
                    <li class="clearfix"><label>难度</label><div class="trip-level-stars"><i class="<?php if ($trip['difficulty_level'] >= 1) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['difficulty_level'] >= 2) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['difficulty_level'] >= 3) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['difficulty_level'] >= 4) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['difficulty_level'] >= 5) echo 'star-full'; else echo 'star-empty'; ?>"></i></div></li>
                    <li class="clearfix"><label>路程</label><div class="trip-level-stars"><i class="<?php if ($trip['remote_level'] >= 1) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['remote_level'] >= 2) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['remote_level'] >= 3) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['remote_level'] >= 4) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['remote_level'] >= 5) echo 'star-full'; else echo 'star-empty'; ?>"></i></div></li>
                    <li class="clearfix"><label>危险度</label><div class="trip-level-stars"><i class="<?php if ($trip['risk_level'] >= 1) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['risk_level'] >= 2) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['risk_level'] >= 3) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['risk_level'] >= 4) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['risk_level'] >= 5) echo 'star-full'; else echo 'star-empty'; ?>"></i></div></li>
                    <li class="clearfix"><label>民俗文化</label><div class="trip-level-stars"><i class="<?php if ($trip['culture_level'] >= 1) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['culture_level'] >= 2) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['culture_level'] >= 3) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['culture_level'] >= 4) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['culture_level'] >= 5) echo 'star-full'; else echo 'star-empty'; ?>"></i></div></li>
                </ul>
            </div>

            <h4 id="participant" style="margin-top: 20px; border-bottom: 1px solid #DDD; padding-bottom: 6px; ">参与者</h4>
            
            <?php $this->renderPartial('tripParticipant', array('participants' => $participants)); ?>
            
        </div>

    </div>
</div>

<script>
    $(function() {
        Trip.init($('div.trip'));
    });
</script>