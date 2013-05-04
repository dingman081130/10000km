<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.User.Account');
Yii::import('application.models.User.User');
Yii::import('application.models.AR.SocialAccountAR');
Yii::import('application.models.Form.*');
Yii::import('application.models.User.Avatar');
Yii::import('application.models.User.Invitation');
Yii::import('application.models.Place.Place');
Yii::import('application.models.AR.UserProfileAR');
Yii::import('application.models.User.SocialAccount');
Yii::import('application.models.Message.SysMessage');

/**
 * Description of AccountController
 *
 * @author yemin
 */
class AccountController extends Controller {

    //put your code here
    
    protected $defaultPageInfo = array(
        'forget' => array('title' => '找回密码'),
        'reset' => array('title' => '重置密码'),
        'signup' => array('title' => '注册新账号'),
        'bindAccount' => array('title' => '绑定到账号'),
        'signupFinished' => array('title' => '注册成功'),
    );

    public function actionForget() {
        $this->pageTitle = '找回密码';
        if (Yii::app()->request->isPostRequest) {
            $email = isset($_POST['login_email']) ? $_POST['login_email'] : null;
            if (empty($email)) {
                return $this->returnJson(array(
                            'code' => -1,
                            'msg' => '邮箱不存在',
                        ));
            }

            $ret = Account::forgetPassword($email);
            $this->returnJson($ret);
        } else {
            $this->render('forget');
        }
    }

    public function actionReset($id, $code) {
        if (Yii::app()->user->id)
            $this->redirect('/home');

        $this->pageTitle = '重置密码';
        if (Yii::app()->request->isPostRequest) {
            $new_pwd = isset($_POST['password']) ? $_POST['password'] : null;
            if (empty($new_pwd)) {
                return $this->returnJson(array(
                            'code' => -1,
                            'msg' => '请输入密码',
                        ));
            }

            if (Account::resetPassword($id, $code, $new_pwd)) {
                return $this->returnJson(array(
                            'code' => 0,
                            'msg' => '您的密码已经重置，你现在可以使用新密码登录',
                        ));
            }
            return $this->returnJson(array(
                        'code' => -1,
                        'msg' => '重置密码失败，请重新找回密码',
                    ));
        } else {
            $this->render('reset');
        }
    }

    public function actionActivateEmail() {
        Account::activateEmail(Yii::app()->user->id);
    }

    public function actionActivate($id, $code) {
        $this->render('active', array(
            'result' => Account::activateVerify($id, $code),
        ));
    }
    
    public function actionInvitationAvailable($invitation) {
        $this->returnJson(array('code'=>0, 'msg'=>'验证码可用'));   // debug
        try{
            Invitation::isValid($invitation);
            $this->returnJson(array('code'=>0, 'msg'=>'验证码可用'));
        }catch(CException $e){
            $this->returnJson(array('code'=>-1, 'msg'=>$e->getMessage()));
        }
    }

    public function actionNameAvailable($name) {
        $ret = Account::usernameAvailable(Yii::app()->user->id, $name);
        $this->returnJson($ret);
    }

    public function actionEmailExist($email) {
        if (Account::emailAvailable($email)) {
            $this->returnJson(array('code' => 0));
        } else {
            $this->returnJson(array(
                'code' => -1,
                'msg' => '该Email已经被注册',
            ));
        }
    }

    public function actionLogout($reurl = null) {
        Yii::app()->user->logout();
        $this->redirect($reurl ? $reurl : '/');
    }

    public function actionSignup() {
        if (Yii::app()->user->id)
            return $this->redirect('/account/bindAccount');

        $social_account = Yii::app()->session['social_account'];
        if (!isset($social_account['account_id']) || !isset($social_account['account_type']))
            return $this->redirect('/');

        $form = new NewAccountForm();
        $form->name = $social_account['name'];

        if (Yii::app()->request->isPostRequest) {
            $form->attributes = $_POST;
            if ($form->validate()) {
                $user = new UserAR();
                $user->name = $form->name;
                $user->login_email = $form->login_email;
                $user->password = $form->password;
                if ($user->save()) {
                    $profile = UserProfileAR::model()->findByPk($user['id']);
                    if (isset($social_account['sex'])) {
                        if ($social_account['sex'] == 'm' || $social_account['sex'] == '男')
                            $profile->sex = 1;
                    }
                    $profile->live_city_id = Place::getClientCity();
                    $profile->save();
                    
                    $sa = new SocialAccountAR();
                    $sa->open_id = $social_account['account_id'];
                    $sa->type = $social_account['account_type'];
                    $sa->user_id = $user->id;
                    $sa->save();

                    $identity = new UserIdentity('dummy', 'dummy');
                    $identity->authenticateUser($user->id);
                    if ($identity->errorCode == UserIdentity::ERROR_NONE)
                        Yii::app()->user->login($identity);

                    if (!empty($form->share) && isset($social_account['account_id']) && isset($social_account['account_type']) && isset($social_account['access_token'])) {
                        SocialAccount::registerShare($social_account['account_type'], $social_account['account_id'], $social_account['access_token']);
                    }
                    
                    //Invitation::addCnt($form->invitation);
                    SysMessage::saveSingup($user->id);
                    Account::activateEmail($user->id);
                    Avatar::updateAvatarFromUrl($social_account['avatar']);
                    $this->redirect('/account/signupFinished');
                } else {
                    
                }
            }
        }


        $this->render('signup', array(
            'form' => $form,
            'account_type' => $social_account['account_type'],
            'account_id' => $social_account['account_id'],
            'avatar' => $social_account['avatar'],
        ));
    }

    public function actionBindAccount() {
        $social_account = Yii::app()->session['social_account'];
        if (!isset($social_account['account_id']) || !isset($social_account['account_type']))
            return $this->redirect('/');

        if (Yii::app()->user->id) {
            Account::bindSocialAccount(Yii::app()->user->id, Yii::app()->session['social_account']['account_id'], Yii::app()->session['social_account']['account_type']);
            return $this->redirect('/home/socialAccount');
        }



        $form = new BindAccountForm();

        if (Yii::app()->request->isPostRequest) {
            $form->attributes = $_POST;
            if ($form->validate()) {
                Account::bindSocialAccount($form->getUserId(), Yii::app()->session['social_account']['account_id'], Yii::app()->session['social_account']['account_type']);

                $identity = new UserIdentity('dummy', 'dummy');
                $identity->authenticateUser($form->getUserId());
                if ($identity->errorCode == UserIdentity::ERROR_NONE)
                    Yii::app()->user->login($identity);
                $this->redirect('/home');
            }
        }
        $this->render('bindAccount', array(
            'form' => $form,
            'account_type' => $social_account['account_type'],
            'account_id' => $social_account['account_id'],
            'avatar' => $social_account['avatar'],
        ));
    }
    
    
    public function actionUnbindAccount() {
        $type = isset($_POST['type']) ? $_POST['type'] : null;
        $ret = Account::unbindSocialAccount(Yii::app()->user->id, $type);
        if ($ret) {
            $this->returnJson(array(
                'code' => 0,
            ));
        } else {
            $this->returnJson(array(
                'code' => -1,
            ));
        }
    }
    
    
    
    public function actionSignupFinished() {
        $this->render('signupFinished');
    }

}

?>
