<?php

Yii::import('application.models.Fresh.Fresh');
Yii::import('application.models.User.User');
Yii::import('application.models.User.UserSuggest');
Yii::import('application.models.User.Message');
Yii::import('application.models.City.City');
Yii::import('application.models.Form.UserProfileForm');
Yii::import('application.models.Album.Album');
Yii::import('application.models.User.UserFollow');
Yii::import('application.models.Couch.Couch');
Yii::import('application.models.Message.Heehaw');
Yii::import('application.models.AR.CouchAR');

class HomeController extends Controller {
    
    protected $defaultPageInfo = array(
        'index' => array('title' =>'用户中心'),
        'album' => array('title' =>'我的相册'),
        'trip' => array('title' =>'我的旅行'),
        'couchHost' => array('title' =>'我是沙发主'),
        'couchSurf' => array('title' =>'我是沙发客'),
        'couchSearch' => array('title' =>'求沙发'),
        'couchProvide' => array('title' =>'我的沙发'),
        'myfans' => array('title' =>'我的粉丝'),
        'myfollow' => array('title' =>'我的关注'),
        'message' => array('title' =>'消息'),
        'personality' => array('title' =>'个性设置'),
        'profile' => array('title' =>'个人资料'),
        'socialAccount' => array('title' =>'账号绑定'),
        'password' => array('title' =>'修改密码'),
    );

    public function filters() {
        return array(
            'accessControl',
            'postOnly + updatePick, pubHeehaw, markSessionRead, pubMsg',
        );
    }

    public function accessRules() {
        return array(
            array(
                'deny',
                'users' => array('?'),
            ),
        );
    }

    /**
     * render home template
     */
    public function actionIndex() {
        $freshes = Fresh::getFreshList(Yii::app()->user->id);    // 获取用户关心的新鲜事
        $user = User::getBasicById(Yii::app()->user->id);     // 用户的基本信息
        $suggest_users = UserSuggest::suggestUser(Yii::app()->user->id, 0, 6);
        $this->render('index', array(
            // 用户的基本资料信息加在这里
            'freshes' => $freshes,
            'user' => $user,
            'suggest_users' => $suggest_users,
        ));
    }

    public function actionAlbum() {
        $albums = Album::getUserAlbums(Yii::app()->user->id);

        $this->render('album', array(
            'albums' => $albums,
        ));
    }

    public function actionAvatar() {
        Yii::import('application.models.User.Avatar');
        $avatar = CUploadedFile::getInstanceByName('avatar');
        if ($avatar) {
            $filename = Avatar::updateAvatarFromUploadedfile($avatar);
            if ($filename) {
                return $this->returnJson(array(
                            'code' => 0,
                            'url' => ImageUrlHelper::imgUrl(ImageUrlHelper::AVATAR_LARGE, $filename),
                        ));
            }
        }
        return $this->returnJson(array(
                    'code' => -1,
                    'msg' => '更新头像失败',
                ));
    }

    /**
     * 将一个会话标记为已读
     */
    public function actionMarkSessionRead(){
        $success = true;$msg = 'ok';$data = array();
        if( !isset( $_POST['id'] ) ){
            $success = false;$msg = '请选择您需要标记为已读的会话';
            $this->returnJson( array( 'success' => $success, 'msg' => $msg, 'data' => $data ));
        }
        Message::setSessionsRead(Yii::app()->user->id, explode(',', $_POST['id']));
        $this->returnJson( array( 'success' => $success, 'msg' => $msg, 'data' => $data ));
    }

    public function actionProfile() {
        $form = new UserProfileForm;
        $user = User::getUser(Yii::app()->user->id);

        $form->attributes = $user;

        $provinces = City::getChildCities(0);


        if (Yii::app()->request->isPostRequest) {
            $form->attributes = $_POST;
            if ($form->updateProfile()) {
                User::updateMyCacheProfile();
                return $this->redirect('/home/profile');
            }
        }

        $live_city = City::getCity($form['live_city_id']);

        $cities = is_array($live_city) && $live_city['up_city'] ? City::getChildCities($live_city['up_city']['id']) : (is_array($live_city) ? array($live_city) : array(/* $provinces[0] */));

        $this->render('profile', array(
            'user' => $user,
            'form' => $form,
            'live_city' => $live_city,
            'provinces' => $provinces,
            'cities' => $cities,
        ));
    }

    public function actionSocialAccount() {
        $this->render('socialAccount');
    }

    public function actionMessage($id = null) {
        if ($id === NULL) {
            $this->render('message', array(
                'message' => Message::getSession(Yii::app()->user->id),
            ));
        } else {
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $this->render('messageThread', array(
                'message' => Message::getMsgByRecerSender(Yii::app()->user->id, intval($id), $page),
                'sender' => User::getUser($id),
            ));
        }
    }

    /**
     * 发布一条消息
     */
    public function actionPubMsg() {
        $success = true;$message = 'ok';$data = array();$code = '';
        $data = Message::saveMsg($_POST['recipient'], Yii::app()->user->id, $_POST['msg']);
        if (empty($data)) {
            $success = false;$message = '提交失败';
        }else{
            $code = $this->renderPartial('messageItem', array('msg' => $data), true);
        }
        $this->returnJson(
            array(
                'success' => $success,
                'msg' => $message,
                'data' => array('code' => $code)
        ));
    }

    /**
     * change the message status read
     * 
     *
      public function actionChangeMessageRead(){
      echo json_encode( array(
      'success' => true,
      'message' => 'ok')
      );
      } */

    /**
     * 我请求的沙发
     */
    public function actionCouchSurf($type=0, $status=0) {
        $surfs = Couch::getSurfCouchLog(Yii::app()->user->id, $type, $status);
        for ($i = 0; $i < count($surfs); $i++) {
            $surfs[$i]['host'] = User::getUser($surfs[$i]['host_id']);
        }
        $this->render('couchSurf', array(
            'surfs' => $surfs,
            'type' => $type,
            'status' => $status,
        ));
    }

    /**
     * 求沙发
     */
    public function actionCouchSearch() {
        if (Yii::app()->request->isPostRequest) {
            try{
                Couch::saveCouchSearch($_POST);
                $this->returnJson(array(
                    'code' => 0,
                ));
            }catch(CException $e){
                $this->returnJson(array(
                    'code' => -1,
                    'msg' => $e->getMessage(),
                ));
            }
        } else {
            $provinces = City::getChildCities(0);
            $couch_search = Couch::getUserCouchSearch(Yii::app()->user->id);
            $this->render('couchSearch', array(
                'provinces' => $provinces,
                'couch_search' => $couch_search,
            ));
        }
    }
    
    public function actionDelCouchSearch() {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        if (Couch::delCouchSearch(Yii::app()->user->id, $id)) {
            return $this->returnJson(array(
                'code' => 0,
                'msg' => '',
            ));
        }
        return $this->returnJson(array(
            'code' => -1,
            'msg' => '删除失败',
        ));
    }

    /**
     * 我提供的沙发
     */
    public function actionCouchHost($type=0, $status=0) {
        $hosts = Couch::getHostCouchLog(Yii::app()->user->id, $type, $status);
        for ($i = 0; $i < count($hosts); $i++) {
            $hosts[$i]['surf'] = User::getUser($hosts[$i]['surf_id']);
        }
        $this->render('couchHost', array(
            'hosts' => $hosts,
            'type' => $type,
            'status' => $status,
        ));
    }

    /**
     * 发布沙发
     */
    public function actionCouchProvide() {
        $form = CouchAR::model()->findByAttributes(array('user_id' => Yii::app()->user->id));
        if (!$form) $form = new CouchAR ();
        
        $albums = Album::getUserAlbums(Yii::app()->user->id);
        $msg = null;
        
        if (Yii::app()->request->isPostRequest) {
            $form->attributes = $_POST;
            if ($form->save()) $msg = '更新沙发信息成功';
        }
        //var_dump($form->attributes);die;
        $this->render('couchProvide', array(
            'form' => $form,
            'albums' => $albums,
            'msg' => $msg,
        ));
    }

    public function actionMyfans() {
        $fans = UserFollow::getUserFans(Yii::app()->user->id);

        $this->render('myfans', array(
            'fans' => $fans,
        ));
    }

    public function actionMyfollow() {
        $follows = UserFollow::getUserFollow(Yii::app()->user->id);

        $this->render('myfollow', array(
            'follows' => $follows,
        ));
    }

    /**
     * 获取推送给当前用户的
     *
     * @echo json 输出一段新鲜事的html代码，供前端新鲜事列表动态加载之用
     */
    public function actionFreshHtml($offset) {
        $success = true;$msg = 'ok';$data = array();
        $freshes = Fresh::getFreshList(Yii::app()->user->id, intval($offset));// 获取用户关心的新鲜事
        if($freshes){
            $html = $this->renderView(array('fresh', 'freshList'), array('freshes' => $freshes), TRUE);
            $data = array('code'=>$html);
        }
        $this->returnJson(array(
            'success' => $success,
            'msg' => $msg,
            'data' => $data
        ));
    }

    public function actionPassword() {
        Yii::import('application.models.Form.ChangePasswordForm');
        $form = new ChangePasswordForm();
        $msg = null;

        if (Yii::app()->request->isPostRequest) {
            $form->attributes = $_POST;
            if ($form->updatePassword()) {
                $msg = '密码修改成功';
            }
        }

        $this->render('password', array(
            'form' => $form,
            'msg' => $msg,
        ));
    }

    /**
     * 更新求捡状态
     */
    public function actionUpdatePick(){
        $success = true;$msg = 'ok';$data = array();
        $success = User::updatePick(
                Yii::app()->user->id, 
                $_POST['dsts'], $_POST['start_date'], $_POST['end_date'], $_POST['desc'], $_POST['heehaw']);
        if(!$success){
            $msg = '提交失败';
        }

        $this->returnJson(array(
            'success' => $success,
            'msg' => $msg,
            'data' => $data
        ));
    }

    /**
     * 求被捡
     */
    public function actionPicked() {
        $this->render('picked', array());
    }

    /**
     * 发布驴叫
     */
    public function actionPubHeehaw(){
        $success = FALSE;$msg = 'ok';$data = array();
        $event = Heehaw::pubHeehaw(Yii::app()->user->id, trim($_POST['content']));
        if (!$event) {
            $msg = '驴叫失败';
            return $this->returnJson(array('success' => $success, 'msg' => $msg, 'data' => $data));
        }
        $success = TRUE;
        $data['code'] = $this->renderView( array('fresh','freshItem'), array('fresh' => $event), TRUE);
        return $this->returnJson(array('success' => $success, 'msg' => $msg, 'data' => $data));
    }
    
    
    public function actionPersonality() {
        Yii::import('application.models.User.UserProfile');
        if (Yii::app()->request->isPostRequest) {
            UserProfile::updatePersonality($_POST);
        } else {
            $this->render('personality');
        }
        
    }
    
    public function actionTrip() {
        Yii::import('application.models.Trip.Trip');
        $create_trips = Trip::getUserCreatedTrips(Yii::app()->user->id);
        $join_trips = Trip::getUserJoinedTrips(Yii::app()->user->id);
        $this->render('trip', array(
            'create_trips' => $create_trips,
            'join_trips' => $join_trips,
        ));
    }

}
