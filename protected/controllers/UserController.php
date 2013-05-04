<?php
Yii::import('application.models.User.User');
Yii::import('application.models.Album.Album');
Yii::import('application.models.User.UserFollow');
Yii::import('application.models.Couch.Couch');
Yii::import('application.models.User.Message');
Yii::import('application.models.User.UserComment');
Yii::import('application.models.Fresh.Fresh');
Yii::import('application.models.Message.SysMessage');

class UserController extends Controller {
    
    protected $defaultPageInfo = array(
    );

    public function filters() {
        return array(
            'accessControl',
            'postOnly + follow, couchRequest, couchInvite, message',
        );
    }

    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('folloe', 'unfollow', 'commentModal', 'couchRequestModal', 'message', 'messageModal'),
                'users' => array('?'),
            ),
        );
    }

    public function actionProfile($id) {
        $user = User::getUser($id);
        if (!$user)
            throw new CHttpException(404);

        $this->pageTitle = $user['name'] . '的个人资料';
        $this->render('profile', array(
            'user' => $user,
        ));
    }
    
    /**
     * 个人主页的沙发页面
     * @param int $id 用户id
     */
    public function actionCouch($id) {
        $user = User::getUser($id);
        if (!$user)
            throw new CHttpException(404);
        $couch = Couch::getUserCouch($id);
        $photos = Album::getAlbumPhotos($couch['album_id']);
        
        $this->pageTitle = $user['name'] . '的沙发';
        $this->render('couch', array(
            'user' => $user,
            'couch' => $couch,
            'photos'=> $photos,
            'has_pending_couch' => Couch::isPendingCouchExists($id, Yii::app()->user->id),
        ));
    }

    /**
     * 渲染某个用户的个人主页
     * @param type $id
     * @throws CHttpException
     */
    public function actionView($id) {
        $user = User::getUser($id);
        if (!$user)
            throw new CHttpException(404);

        $this->pageTitle = $user['name'] . '的个人主页';
        
        $follows = UserFollow::getUserFollow($user['id']);
        $freshes = Fresh::getFreshListOut($user['id']);
        //var_dump($events);die;
        $this->render('home', array(
            'user' => $user,
            'follows' => $follows,
            'freshes' => $freshes,
        ));
    }

    public function actionAlbum($id) {
        $user = User::getUser($id);
        if (!$user)
            throw new CHttpException(404);

        $this->pageTitle = $user['name'] . '的相册';
        $albums = Album::getUserAlbums($user['id']);
        $this->render('album', array(
            'user' => $user,
            'albums' => $albums,
        ));
    }

    public function actionPlace($id) {
        $user = User::getUser($id);
        if (!$user)
            throw new CHttpException(404);
        
        $this->pageTitle = $user['name'] . '去过的地方';
        $this->render('place', array(
            'user' => $user,
        ));
    }

    public function actionComment($id) {
        $user = User::getUser($id);
        if (!$user)
            throw new CHttpException(404);

        $this->pageTitle = $user['name'] . '的评价';
        if (Yii::app()->request->isPostRequest) {
            $params = $_POST;
            $params['user_id'] = $user['id'];
            $comment = UserComment::saveComment($params);
            if ($comment) {
                return $this->returnJson(array(
                            'code' => 0,
                            'html' => $this->widget('UserCommentWidget', array(
                                'comment' => $comment,
                                    ), true),
                        ));
            }
            return $this->returnJson(array(
                        'code' => -1,
                        'msg' => '评论失败',
                    ));
        } else { // not POST
            $comment_count = UserComment::getCommentCount($id);
            $this->render('comment', array(
                'user' => $user,
                'comment_count' => $comment_count,
            ));
        }
    }

    public function actionCommentModal($id) {
        $user = User::getUser($id);
        if ($user) {
            $html = $this->renderPartial('commentModal', array(
                'user' => $user,
                    ), true);
            return $this->returnJson(array(
                        'code' => 0,
                        'html' => $html,
                    ));
        }
        $this->returnJson(array(
            'code' => -1,
            'msg' => 'failed',
        ));
    }

    public function actionGetComment($id, $offset = 0, $type = 0, $from = null) {
        $size = 10;
        $comments = UserComment::getUserComment($id, $type, $from, $offset, $size);
        $html = '';
        foreach ($comments as $comment) {
            $html .= $this->widget('UserCommentWidget', array(
                'comment' => $comment,
                    ), true);
        }
        $this->returnJson(array(
            'code' => 0,
            'html' => $html,
        ));
    }

    public function actionSummary($id) {
        $user = User::getUser($id);
        if ($user) {
            $this->renderPartial('summary', array(
                'user' => $user,
                'follow_count' => count(UserFollow::getUserFollowIds($id)),
                'fans_count' => count(UserFollow::getUserFansIds($id)),
            ));
        }
    }

    public function actionFollow() {
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : null;
        if (UserFollow::followUser(Yii::app()->user->id, $uid)) {
            SysMessage::saveFollowMsg($uid, Yii::app()->user->id);
            $this->returnJson(array(
                'code' => 0,
                'msg' => '已关注',
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => '关注失败',
            ));
        }
    }

    public function actionUnfollow() {
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : null;
        if (UserFollow::unfollowUser(Yii::app()->user->id, $uid)) {
            $this->returnJson(array(
                'code' => 0,
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => '关注失败',
            ));
        }
    }

    public function actionCouchRequest() {
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : null;
        $params = $_POST;
        
        if (Couch::isPendingCouchExists($uid, Yii::app()->user->id))
            $this->returnJson(array('code' => -1, 'msg' => '你曾经对此沙发的申请仍在审批中'));
        if (($ret = Couch::requestCouch($uid, Yii::app()->user->id, $params) ) === TRUE)
            $this->returnJson(array('code' => 0,));
        $this->returnJson(array('code' => -1, 'data' => $ret));
    }

    public function actionCouchRequestModal($id) {
        $html = $this->renderPartial('couchRequestModal', array(
            'uid' => $id,
                ), true);
        $this->returnJson(array(
            'code' => 0,
            'html' => $html,
        ));
    }

    public function actionMessage() {
        if (isset($_POST['content']) && isset($_POST['uid'])) {
            $ret = Message::saveMsg($_POST['uid'], Yii::app()->user->id, $_POST['content']);
            if (!empty($ret)) {
                $this->returnJson(array(
                    'code' => 0,
                    'msg' => '发送成功',
                ));
            }
        }

        $this->returnJson(array(
            'code' => -1,
            'msg' => '发送失败',
        ));
    }

    public function actionMessageModal($id) {
        $user = User::getUser($id);
        if ($user) {
            $html = $this->renderPartial('messageModal', array(
                'user' => $user,
                    ), true);
            $this->returnJson(array(
                'code' => 0,
                'html' => $html,
            ));
        }
    }

    public function actionCouchInvite($id) {
        $params = $_POST;
        if (Couch::inviteCouch($id, Yii::app()->user->id, $params)) {
            $this->returnJson(array(
                'code' => 0,
            ));
        } else {
            $this->returnJson(array(
                'code' => 0,
                'msg' => 'failed',
            ));
        }
    }
    
    /**
     * 获取某人产生的新鲜事
     *
     * @echo json 输出一段新鲜事的html代码，供前端新鲜事列表动态加载之用
     */
    public function actionFreshHtml() {
        $success = true;$msg = 'ok';$data = array();
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        if(!isset($_GET['uid'])){
            $success = false; $msg = '该用户没有更多新鲜事了';
            return $this->returnJson(array('success' => $success,'msg' => $msg,'data' => $data));
        }
        $uid = intval($_GET['uid']);
        $freshes = Fresh::getFreshListOut(intval($uid), intval($offset));
        if($freshes){
            $html = $this->renderView(array('fresh', 'freshList'), array('freshes' => $freshes), TRUE);
            $data = array('code'=>$html);
        }
        return $this->returnJson(array('success' => $success,'msg' => $msg,'data' => $data));
        
    }

}
