<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.GroupAR');
Yii::import('application.models.Group.Group');
Yii::import('application.models.Group.GroupUser');
Yii::import('application.models.Group.Topic');
Yii::import('application.models.Group.GroupCategory');
Yii::import('application.models.City.City');
Yii::import('application.models.Form.GroupImageForm');
Yii::import('application.models.Pagination');
Yii::import('application.models.AR.GroupCategoryAR');
Yii::import('application.models.Message.SysMessage');

/**
 * Description of GroupController
 *
 * @author yemin
 */
class GroupController extends Controller{
    //put your code here
    
    protected $defaultPageInfo = array(
        'create' => array('title' => '新建小组'),
        'myGroups' => array('title' => '我加入的小组'),
        'index' => array(
            'title' => '小组',
            'keywords' => '旅行,小组'
            ),
        'myTopics' => array('title' => '我发起的话题'),
        'myReplies' => array('title' => '我回复的话题'),
        'list' => array(
            'title' => '浏览小组',
            'keywords' => '旅行,小组',
            ),
        'search' => array('title' => '搜索小组'),
    );


    public function filters() {
        return array(
            'accessControl',
            'postOnly + join, unjoin',
        );
    }

    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('create', 'myTopics', 'myReplies', 'myGroups', 'join', 'unjoin'),
                'users' => array('?'),
            ),
        );
    }
    
    
    public function actionIndex() {
        if (!Yii::app()->user->id) $this->forward ('list');
        $gids = GroupUser::getUserGroupIds(Yii::app()->user->id);
        $topics = Topic::getTopicsByGroups($gids);
        $this->render('myGroupTopics', array(
            'topics' => $topics,
        ));
    }
    
    public function actionMyTopics() {
        $topics = Topic::getTopicsCreatedByUser(Yii::app()->user->id);
        $this->render('myTopics', array(
            'topics' => $topics,
        ));
    }
    
    public function actionMyReplies() {
        $topics = Topic::getTopicsRepliedByUser(Yii::app()->user->id);
        $this->render('myReplies', array(
            'topics' => $topics,
        ));
    }
    
    public function actionMyGroups() {
        $groups = GroupUser::getUserGroups(Yii::app()->user->id);
        $this->render('myGroups', array(
            'groups' => $groups,
        ));
    }
    
    public function actionList() {
        $page = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
        $size = Yii::app()->params['groupPageSize'];
        $offset = Pagination::getOffset($page, $size);
        $cat  = isset($_GET['cat']) && GroupCategoryAR::model()->exists( 'id=?', array(intval($_GET['cat'])) ) ? intval($_GET['cat']) : 0;
        
        $categories = GroupCategory::getAllCategories();
        $groups     = Group::search('', $cat, 0, $offset, $size);
        $cnt        = Group::searchCnt('', $cat);
        $page_cnt   = Pagination::getPageCnt($cnt, $size);
        $pagination['page_cnt'] = $page_cnt;
        $pagination['cur']      = $page;
        
        $new_groups = Group::search('', 0, 0, 0, 12);
        $hot_groups = Group::getHotGroups(12);
        
        $this->render('list', array(
            'categories' => $categories,
            'groups' => $groups,
            'pagination' => $pagination,
            'cur_category' => $cat,  //// 当前页面选择的标签类型
            'new_groups' => $new_groups,
            'hot_groups' => $hot_groups
        ));
    }
    
    /**
     * 获取组信息
     * 
     * @param string $kw    //// 按关键字检索
     * @param int $cat      //// 按类型检索
     * @param int $city     //// 按城市检索
     * @param string $page  //// 页码
     * @param string $size  //// 大小
     */
    public function actionSearch ($kw = '', $page = 0) {
        
        $size   = Yii::app()->params['groupPageSize'];
        $page <= 0 && $page = 1;
        
        $offset = Pagination::getOffset($page, $size);
        
        $categories = GroupCategory::getAllCategories();
        
        $groups = Group::search($kw, 0, 0, $offset, $size);
        $cnt        = Group::searchCnt($kw);
        $page_cnt   = Pagination::getPageCnt($cnt, $size);
        $pagination['page_cnt'] = $page_cnt;
        $pagination['cur']      = $page;
        
        $this->render('search', array(
            'kw' => $kw,
            'groups' => $groups,
            'categories' => $categories,
            'pagination' => $pagination,
        ));
    }
    
    public function actionView($id, $page=1) {
        $page_size = 10;
        $group = Group::getGroup($id);
        if (!$group) 
            throw new CHttpException(404);
        
        $this->pageTitle = $group['name'];
        $this->pageDescription = Helpers::substr(strip_tags($group['description']), 80);
        $topics = Topic::getTopicsByGroup($id, ($page-1)*$page_size, $page_size);
        $topics_count = Topic::getTopicsCountByGroup($id);
        $this->render('view', array(
            'group' => $group,
            'topics' => $topics,
            'users' => GroupUser::getGroupUsers($id),
            'page' => intval($page),
            'page_count' => Pagination::getPageCnt($topics_count, $page_size),
        ));
    }
    
    
    public function actionCreate() {
        $model = new GroupAR();
        
        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST;
            $model->uploaded_image = CUploadedFile::getInstanceByName('image');
            if ($model->save()) {
                EventListener::getListener()->run(array(
                    'user_id' => Yii::app()->user->id,
                    'type' => Event::PGRP,
                    'content' => CJSON::encode(array('grp_id' => $model->id)),
                ));
                GroupUser::joinGroup(Yii::app()->user->id, $model['id']);
                return $this->redirect('/group/'.$model['id']);
            } 
        }
        
        $this->render('create', array(
            'categories' => GroupCategory::getAllCategories(),
            'form' => $model,
            'provinces' => City::getChildCities(0),
        ));
    }
    
    
    public function actionJoin($id) {
        $ret = GroupUser::joinGroup(Yii::app()->user->id, $id);
        if ($ret) {
            SysMessage::saveJGrpMsg(Yii::app()->user->id, $id);
            $user = User::getUser(Yii::app()->user->id);
            $html = $this->renderPartial('groupMember', array('user' => $user), TRUE);
            $this->returnJson(array(
                'code' => 0,
                'msg' => '已加入小组',
                'data' => array(
                    'user_html' => $html,
                )
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => '加入小组失败',
            ));
        }
    }
    
    public function actionUnjoin($id) {
        $ret = GroupUser::unjoinGroup(Yii::app()->user->id, $id);
        if ($ret) {
            $this->returnJson(array(
                'code' => 0,
                'msg' => '已退出小组',
                'data' => array('user' => Yii::app()->user->id),    // 谁退出了小组
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => '退出小组失败',
            ));
        }
    }
    
    
    public function actionUsers($id) {
        $group = Group::getGroup($id);
        if (!$group)
            throw new CHttpException(404);
        
        $this->pageTitle = $group['name'].'的成员';
        $users = GroupUser::getGroupUsers($id);
        $this->render('users', array(
            'group' => $group,
            'users' => $users,
        ));
    }
    
    
    public function actionFeed($id) {
        Yii::import('ext.EFeed.*');
        $group = Group::getGroup($id);
        if (!$group)
            throw new CHttpException(404);
        
        $feed = new EFeed();
        $feed->title = $group['name'];
        $feed->description = Helpers::substr($group['description'], 200);
        $topics = Topic::getTopicsByGroup($id);
        if (empty($topics)) return;
        
        $base_url = Yii::app()->getBaseUrl(true);
        foreach ($topics as $topic) {
            $item = $feed->createNewItem();
            $item->title = $topic['title'];
            $item->link = "$base_url/topic/{$topic['id']}";
            $item->date = $topic['create_time'];
            $item->description = Helpers::substr(strip_tags($topic['content']), 200);
            $feed->addItem($item);
        }
        $feed->generateFeed();
    }
}

?>
