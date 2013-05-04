<?php
Yii::import('application.models.User.User');
Yii::import('application.models.Group.TopicComment');
Yii::import('application.models.User.User');

$group    = Group::getGroup($topic['group_id']);
$comments = TopicComment::getComments($topic['id']);
$topics   = Topic::getTopicsByGroup($topic['group_id']);
$user = User::getUser($topic['author_id']);

$canedit = Yii::app()->user->id == $topic['author_id'];
?>
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
                            'name' => $group['name'],
                            'url'  => array('/group/'.$group['id']),
                        ),
                        array(
                            'name' => $topic['title'],
                            'url' => array('/topic/'.$topic['id']),
                        ),
                    )));
            ?>
        <div class="topic-story" tid ="<?php echo CHtml::encode($topic['id']) ?>">
            <div id="title-bar" class="clearfix">
                <h3><?php echo CHtml::encode($topic['title']); ?></h3>
            </div>
            
            <div class="clearfix">
                <small class="pull-left">
                    <a href="/user/<?php echo CHtml::encode($user['id']); ?>"><?php echo CHtml::encode($user['name']); ?></a>
                    <?php if (empty($topic['edit_time'])) echo ' 发布于 '. CHtml::encode(Helpers::friendlyTime($topic['create_time']));  else echo ' 更新于 ' . CHtml::encode(Helpers::friendlyTime($topic['edit_time']));?>
                </small>
                
            <!-- JiaThis Button BEGIN -->
            <div class="jiathis_style" style="margin-left: 20px;float: left">
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
                    hideMore:false
            }
            </script>
            <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js?uid=1736140" charset="utf-8"></script>
            <!-- JiaThis Button END -->
            
                <?php if (Yii::app()->user->id == $topic['author_id']) { ?><small class="pull-right"><a href="/topic/edit/<?php echo $topic['id']; ?>"><i class="icon-edit"></i> 编辑</a></small><?php }?>
            </div>
            <div class="clearfix" style="margin:10px 0 20px 0;background-color: whitesmoke;padding: 10px 10px;" id="redactor_content">
                <?php echo $topic['content']; ?>
            </div>
            
            <?php if(Yii::app()->user->id){ ?>
            <div class="replay clearfix">
                <div class="thumbnail pull-left">
                    <img src="<?php echo ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_SMALL, Yii::app()->user->attrs['avatar']); ?>">
                </div>
                <div style="margin-left: 80px;">
                    <textarea class="reply-text" rows="4" placeholder="发表评论" style="width:98%;resize:vertical"></textarea>
                    <div class="clearfix">
                        <span class="reply-emotion pull-left"></span>
                        <button class="reply-button btn btn-small btn-primary pull-right" cid="0">回复</button>
                    </div>
                </div>
            </div>
            <?php }?>
            <div class="comment-list clearfix" page_size="<?php echo Yii::app()->params['topicCommentPageSize']; ?>" style="border-top: #d7d8d7 dashed 1px;margin-top:10px">
                <?php 
                    foreach ($comments as $comment ){
                        $this->renderPartial('commentItem', array('comment' => $comment));
                    }
                ?>
                
                <?php if (count($comments) >= Yii::app()->params['commentPageSize']) {?>
                    <button class="btn comments_load_more" hasmore ="yes" style="margin-top: 5px;width: 100%;text-align: center;"><small>加载更多</small></button>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="span4">
        <p class="text-success" style="margin-top:10px;">最新话题...</p>
        <ul class="unstyled">
            <?php
                foreach($topics as $one){?>
                    <?php $user = User::getUser($one['author_id']);  ?>
                    <li style="border-top:  #d7d8d7 dashed 0px;">
                        <a href="/topic/<?php echo CHtml::encode($one['id']); ?>"><?php echo CHtml::encode(Utils::tripDescStriper($one['title'],30)); ?></a>
                        <span class="muted" style="margin-left:10px;"><small>回复<?php echo CHtml::encode(TopicComment::getCommentsCnt($one['id'])) ?></small></span>
                        <span class="pull-right muted"><small><?php echo CHtml::encode(Helpers::friendlyTime($one['create_time'])); ?></small></span>
                    </li>
            <?php }?>
        </ul>
    </div>
</div>

<style>
</style>
<script>
    $(function(){
        TopicStory.init($('.topic-story'));
        
    });
</script>