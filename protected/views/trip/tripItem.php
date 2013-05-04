<?php
/**
 * @param $trip
 */

Yii::import('application.models.User.User');
Yii::import('application.models.City.City');
Yii::import('application.models.Trip.Trip');

$user = User::getBasicById($trip['creator_id']);
$participant_count = Trip::getTripParticipantsCount($trip['id']);
$follower_count = Trip::getTripFollowersCount($trip['id']);
$comment_count = Trip::getTripCommentsCount($trip['id']);
$from_city = City::getCity($trip['from_city']);
$trip_dsts = Trip::getTripDsts($trip['id']);
$trip_ways = Trip::getTripWays($trip['id']);
$cover = ImageUrlHelper::imgUrl(ImageUrlHelper::TRIP_COVER_SMALL, $trip['cover']);
$trip_id = $trip['id'];
?>
<li class="clearfix trip-item">
    <div class="clearfix">
        <div class="pull-left"><h4><a href="/trip/<?php echo $trip_id; ?>" target="_blank"><?php echo CHtml::encode($trip['title']); ?></a></h4></div>
    </div>

    <div class="row">
        <div class="span6">
            <div class="trip-content clearfix">
                <a href="/trip/<?php echo intval($trip['id']); ?>" target="_blank"><img class="pull-left" src="<?php echo CHtml::encode($cover); ?>" class="pull-left"></a>
                <div class="trip-detail">
                    <ul class="unstyled">
                        <li>出发地：<?php 
                            if (!$from_city) {
                                echo '不限'; 
                            } else {
                                if (!isset($from_city['pinyin'])) {
                                    echo CHtml::encode($from_city['name']); 
                                } else {
                                    echo CHtml::link($from_city['name'], '/place/'.$from_city['pinyin']);
                                }
                            }
                            ?>
                        </li>
                        <?php
                        $str = '';
                            foreach ($trip_dsts as $id => $name) {
                                $str .= $name . ' ';
                            }
                        ?>
                        <li title="<?php echo CHtml::encode($str); ?>">目的地：
                            <?php
                                echo CHtml::encode(Helpers::substr($str, 18, true));
                            ?>
                        </li>
                        <?php
                        $str = '';
                        foreach ($trip_ways as $id => $name) {
                            $str .= $name . ' ';
                        }
                        ?>
                        <li title="<?php echo CHtml::encode($str); ?>">旅行类型：
                            <?php
                            echo CHtml::encode(Helpers::substr($str, 20, true));
                            ?>
                        </li>
                        <li>出发日期: <?php if ($trip['start_date'] == '0000-00-00') echo '不限'; else echo Helpers::friendlyDate($trip['start_date']); ?> </li>
                        <li>结束日期: <?php if ($trip['end_date'] == '0000-00-00') echo '不限'; else echo Helpers::friendlyDate($trip['end_date']); ?></li>
                    </ul>
                </div>

            </div>
            <div class="clearfix">
                <!--<p><span>时间: <?php echo CHtml::encode($trip['start_date']); ?> 至 <?php echo CHtml::encode($trip['end_date']); ?></a></span><span style="margin-left: 10px;">参与者: <a href="#">13</a>人</span></p>-->
                <p>

                    <span><a class="username" uid="<?php echo intval($user['id']); ?>" href="/user/<?php echo intval($user['id']); ?>"><img style="height: 18px;" src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_TINY, $user['avatar']); ?>"> <?php echo CHtml::encode($user['name']); ?></a></span> 发布于 <time datetime="<?php echo $trip['create_time']; ?>"><?php echo Helpers::friendlyTime($trip['create_time'], false); ?></time>

                    <span class="pull-right">  评论(<a href="/trip/<?php echo $trip_id; ?>#comments" target="_blank"><?php echo $comment_count; ?></a>)
                        |  关注(<a href="/trip/<?php echo $trip_id; ?>#comments" target="_blank"><?php echo $follower_count; ?></a>)
                        |  参加(<a href="/trip/<?php echo $trip_id; ?>#participant" target="_blank"><?php echo $participant_count; ?></a>)</span>
                    <!--
                    <i class="icon-user"></i>作者 <i class="icon-time"></i> 3小时前 <i class="icon-comment"></i> 12评论 <i class="icon-eye-open"></i> 33关注 <i class="icon-eye-open"></i> 22参与
                    -->
                </p>
            </div>
        </div>

        <div class="span2 trip-level">
            <h5 style="margin-top: 0; text-align: center;">旅行评估</h5>
            <ul class="unstyled">
                <li class="clearfix"><label>难度</label><div class="trip-level-stars"><i class="<?php if ($trip['difficulty_level'] >= 1) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['difficulty_level'] >= 2) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['difficulty_level'] >= 3) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['difficulty_level'] >= 4) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['difficulty_level'] >= 5) echo 'star-full'; else echo 'star-empty'; ?>"></i></div></li>
                <li class="clearfix"><label>路程</label><div class="trip-level-stars"><i class="<?php if ($trip['remote_level'] >= 1) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['remote_level'] >= 2) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['remote_level'] >= 3) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['remote_level'] >= 4) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['remote_level'] >= 5) echo 'star-full'; else echo 'star-empty'; ?>"></i></div></li>
                <li class="clearfix"><label>危险度</label><div class="trip-level-stars"><i class="<?php if ($trip['risk_level'] >= 1) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['risk_level'] >= 2) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['risk_level'] >= 3) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['risk_level'] >= 4) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['risk_level'] >= 5) echo 'star-full'; else echo 'star-empty'; ?>"></i></div></li>
                <li class="clearfix"><label>民俗文化</label><div class="trip-level-stars"><i class="<?php if ($trip['culture_level'] >= 1) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['culture_level'] >= 2) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['culture_level'] >= 3) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['culture_level'] >= 4) echo 'star-full'; else echo 'star-empty'; ?>"></i><i class="<?php if ($trip['culture_level'] >= 5) echo 'star-full'; else echo 'star-empty'; ?>"></i></div></li>
            </ul>
        </div>
    </div>
</li>
