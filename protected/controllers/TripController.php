<?php

Yii::import('application.models.AR.*');
Yii::import('application.models.Trip.*');
Yii::import('application.models.City.City');
Yii::import('application.models.Way.Way');
Yii::import('application.models.Pagination');
Yii::import('application.models.User.UserSuggest');
Yii::import('application.models.Message.SysMessage');

class TripController extends Controller {
    
    const TRIP_SEARCH_PAGE_SIZE = 6;
    
    protected $defaultPageInfo = array(
        'index' => array(
            'title' => '结伴旅行',
            'keywords' => '结伴,旅行,穷游,拼客,拼玩,拼途,拼吃,找驴友,一起去,有没有',
            'description' => '一万公里旅行网为您提供实时的结伴，拼客，沙发客信息；方便您快速，准确找到同样在路上的朋友。让我们开启您的一万公里吧！',
        ),
        'search' => array(
            'title' => '结伴旅行',
            'keywords' => '结伴,旅行,穷游,拼客,拼玩,拼途,拼吃,找驴友,一起去,有没有',
            'description' => '一万公里旅行网为您提供实时的结伴，拼客，沙发客信息；方便您快速，准确找到同样在路上的朋友。让我们开启您的一万公里吧！',
        ),
        'create' => array(
            'title' => '发起旅行计划',
            'keywords' => '结伴,旅行,穷游,拼客,拼玩,拼途,拼吃,找驴友,一起去,有没有',
            'description' => '一万公里旅行网为您提供实时的结伴，拼客，沙发客信息；方便您快速，准确找到同样在路上的朋友。让我们开启您的一万公里吧！',
        ),
    );

    public function filters() {
        return array(
            'accessControl',
            'postOnly + uploadCover, uploadImage, comment, join, unjoin, follow, unfollow',
        );
    }

    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('create', 'follow', 'unfollow', 'join', 'unjoin', 'uploadCover', 'uploadImage'),
                'users' => array('?'),
            ),
        );
    }

    public function actionIndex() {
        return $this->actionSearch();
    }

    public function actionView($id) {
        $trip = Trip::getTrip($id);
        if (!$trip) 
            throw new CHttpException(404);

        $creator = User::getBasicById($trip['creator_id']);
        $from_city = City::getCity($trip['from_city']);
        $trip_dsts = Trip::getTripDsts($trip['id']);
        $trip_ways = Trip::getTripWays($trip['id']);
        $comments = Trip::getTripComments($trip['id']);

        $this->pageTitle = $trip['title'];
        $this->pageDescription = Helpers::substr(strip_tags($trip['content']), 50);
        $this->pageKeywords = implode(',', $trip_dsts);
        
        $this->render('view', array(
            'trip' => $trip,
            'creator' => $creator,
            'followers' => User::getUsers(Trip::getTripFollowers($id)),
            'participants' => User::getUsers(Trip::getTripParticipants($id)),
            'ifollow' => Trip::isUserFollowTrip(Yii::app()->user->id, $id),
            'ijoin' => Trip::isUserJoinTrip(Yii::app()->user->id, $id),
            'from_city' => $from_city,
            'trip_dsts' => $trip_dsts,
            'trip_ways' => $trip_ways,
            'cover' => ImageUrlHelper::imgUrl(ImageUrlHelper::TRIP_COVER_MIDDLE, $trip['cover']),
            'threaded_comments' => new ThreadedComments($comments),
            'comments_prev' => 0,
            'comments_limit' => 5,
        ));
    }

    public function actionCreate() {
        if (!Yii::app()->request->isPostRequest) {
            return $this->render('create', array(
                        'provinces' => City::getChildCities(0),
                        'ways' => Way::getAllWays(),
                    ));
        } else {
            $cover = isset(Yii::app()->session['cover']) ? Yii::app()->session['cover'] : null;
            try {
                $trip = Trip::addTrip($_POST, $cover);
                $this->redirect('/trip/' . $trip->id);
            } catch (CException $e) {
                $this->returnJson(array(
                    'success' => false,
                    'msg' => $e->getMessage(),
                ));
            }
        }
    }

    public function actionUploadCover() {
        $cover = new TripCover();
        $file_name = $cover->saveCover(CUploadedFile::getInstanceByName('cover'));

        if ($file_name) {
            Yii::app()->session['cover'] = $file_name;
            $this->returnJson(array(
                'code' => 0,
                'url' => ImageUrlHelper::imgUrl(ImageUrlHelper::TRIP_COVER_MIDDLE, $file_name),
            ));
        } else {
            Yii::app()->session['cover'] = null;
            $this->returnJson(array(
                'code' => -1,
                'msg' => '上传文件失败',
            ));
        }
    }

    public function actionUploadImage() {
        $img = new TripImage;
        $file_name = $img->saveImage(CUploadedFile::getInstanceByName('file'));

        if ($file_name) {
            $this->returnJson(array(
                'filelink' => ImageUrlHelper::imgUrl(ImageUrlHelper::TRIP_IMAGE, $file_name),
            ));
        }
    }

    public function actionSearch() {
        $dsts = empty($_GET['dsts']) ? array() : explode(',', $_GET['dsts']);
        $start_date = empty($_GET['start_date']) ? null : $_GET['start_date'];
        $end_date = empty($_GET['end_date']) ? null : $_GET['end_date'];

        $trip_way = isset($_GET['trip_way']) && is_array($_GET['trip_way']) ? $_GET['trip_way'] : array();

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($page <= 0)
            $page = 1;

        $arr = Trip::searchTrips($dsts, $start_date, $end_date, $trip_way, ($page-1)*self::TRIP_SEARCH_PAGE_SIZE, self::TRIP_SEARCH_PAGE_SIZE);
        $trips = $arr['trips'];
        $total = $arr['total'];
        
        $suggest_users = UserSuggest::suggestTripUser(Yii::app()->user->id, $dsts);

        $this->render('search', array(
            'all_ways' => Way::getAllWays(),
            'trips' => $trips,
            'total' => $total,
            'page' => $page,
            'page_size' => self::TRIP_SEARCH_PAGE_SIZE,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'trip_way' => $trip_way,
            'dsts' => $dsts,
            'suggest_users' => $suggest_users,
        ));
    }

    public function actionComment($id) {

        $model = new TripCommentAR();
        $model->attributes = $_POST;
        $model->trip_id = $id;
        if ($model->save()) {
            SysMessage::saveRTripMsg(Yii::app()->user->id, $id);
            SysMessage::saveRTripCMsg(Yii::app()->user->id, $_POST['parent_id']);
            $this->redirect('/trip/' . $id);
        } else {
            var_dump($model->getErrors());
        }
    }

    public function actionGetComments($tid, $prev_id) {
        $comments = Trip::getTripComments($tid);
        $this->renderPartial('getComments', array(
            'threaded_comments' => new ThreadedComments($comments),
            'comments_prev' => intval($prev_id),
            'comments_limit' => 5,
        ));
    }

    public function actionFollow() {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        if (Trip::followTrip(Yii::app()->user->id, $id)) {
            SysMessage::saveFTripMsg(Yii::app()->user->id, $id);
            $this->returnJson(array('code' => 0));
        } else {
            $this->returnJson(array('code' => -1, 'msg' => '关注失败'));
        }
    }

    public function actionUnfollow() {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        if (Trip::unfollowTrip(Yii::app()->user->id, $id)) {
            $this->returnJson(array('code' => 0));
        } else {
            $this->returnJson(array('code' => -1, 'msg' => '取消关注失败'));
        }
    }

    public function actionJoin() {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        if (Trip::joinTrip(Yii::app()->user->id, $id)) {
            SysMessage::saveJTripMsg(Yii::app()->user->id, $id);
            $participants = User::getUsers(Trip::getTripParticipants($id));
            $this->returnJson(array(
                'code' => 0,
                'data' => array(
                    'participantsHtml' => $this->renderPartial('tripParticipant', array('participants' => $participants), true),
                )
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => '加入旅行失败',
            ));
        }
    }

    public function actionUnjoin() {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        if (Trip::unjoinTrip(Yii::app()->user->id, $id)) {
            $participants = User::getUsers(Trip::getTripParticipants($id));
            $this->returnJson(array(
                'code' => 0,
                'data' => array(
                    'participantsHtml' => $this->renderPartial('tripParticipant', array('participants' => $participants), true),
                )
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => '失败',
            ));
        }
    }

}
