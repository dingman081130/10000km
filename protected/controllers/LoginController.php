<?php

Yii::import('application.vendors.*');
Yii::import('application.models.AR.*');
Yii::import('application.models.Form.LoginForm');

class LoginController extends Controller {

    public function filters() {
        return array(
            'postOnly + index',
        );
    }
    
    private function redirectBack() {
        $reurl = null;
        if (isset(Yii::app()->request->cookies['reurl'])) {
            $reurl = Yii::app()->request->cookies['reurl']->value;
        }
        if (empty($reurl) && !empty(Yii::app()->session['reurl'])) {
            $reurl = Yii::app()->session['reurl'];
            unset(Yii::app()->session['reurl']);
        }
        if (empty($reurl) && !empty(Yii::app()->request->urlReferrer)) {
            $reurl = Yii::app()->request->urlReferrer;
        }
        if (empty($reurl)) {
            $reurl = '/';
        }
        unset(Yii::app()->request->cookies['reurl']);
        $this->redirect ($reurl);
    }

    public function actionIndex() {
        $form = new LoginForm();
        $form->attributes = $_POST;

        if ($form->validate() && $form->login()) {
            return $this->returnJson(array('code' => 0));
        } else {
            //var_dump($form->getErrors());
            return $this->returnJson(array('code' => -1, 'msg' => '邮箱或密码错误。'));
        }
    }

    public function actionRenren() {
        require_once 'Renren/RenrenConfig.php';
        $config = RenrenConfig::getConfig();
        
        Yii::app()->session['reurl'] = Yii::app()->request->urlReferrer;
        $this->redirect("https://graph.renren.com/oauth/authorize?client_id=" . urldecode($config->APIKey) . "&redirect_uri=" . urlencode($config->CallbackUrl) . "&response_type=code&scope=publish_feed");
    }

    public function actionRenrenCallback($code) {
        require_once 'Renren/RenrenConfig.php';
        require_once 'httpful.phar';

        $config = RenrenConfig::getConfig();
        $url = "https://graph.renren.com/oauth/token?grant_type=authorization_code&client_id=" .
                urldecode($config->APIKey) .
                "&redirect_uri=" . urlencode($config->CallbackUrl) .
                "&client_secret=" . urlencode($config->SecretKey) .
                "&code=" . urlencode($code);
        $response = \Httpful\Request::get($url)->send();
        $ret = $response->body;
        if (!isset($ret->access_token)) {
            $this->redirect('/');
            return;
        }
        
        Yii::app()->session['social_account'] = array(
            'access_token' => $ret->access_token,
            'account_type' => SocialAccountAR::TYPE_RENREN,
            'account_id' => $ret->user->id,
        );

        $record = SocialAccountAR::model()->findByAttributes(array(
            'type' => SocialAccountAR::TYPE_RENREN,
            'open_id' => $ret->user->id,
                ));

        if ($record == null) {
            Yii::app()->session['social_account'] = array_merge(Yii::app()->session['social_account'], array(
                'name' => $ret->user->name,
                'avatar' => $ret->user->avatar[3]->url,
            ));
            $this->redirect('/account/signup');
        } else {
            $identity = new UserIdentity('dummy', 'dummy');
            $identity->authenticateUser($record->user_id);
            if ($identity->errorCode == UserIdentity::ERROR_NONE)
                Yii::app()->user->login($identity);
            $this->redirectBack();
        }
    }

    public function actionWeibo() {
        require_once 'Weibo/WeiboConfig.php';
        $config = WeiboConfig::getConfig();

        $o = new SaeTOAuthV2($config->AppKey, $config->SecretKey);
        $code_url = $o->getAuthorizeURL($config->CallbackUrl);
        Yii::app()->session['reurl'] = Yii::app()->request->urlReferrer;
        $this->redirect($code_url);
    }

    public function actionWeiboCallback($code) {
        require_once 'Weibo/WeiboConfig.php';
        $config = WeiboConfig::getConfig();

        $keys = array();
        $keys['code'] = $code;
        $keys['redirect_uri'] = $config->CallbackUrl;

        $o = new SaeTOAuthV2($config->AppKey, $config->SecretKey);
        $token = null;
        try {
            $token = $o->getAccessToken('code', $keys);
        } catch (OAuthException $e) {
            return;
        }


        $client = new SaeTClientV2($config->AppKey, $config->SecretKey, $token['access_token']);

        $ret = $client->get_uid();
        $uid = $ret['uid'];
        
        Yii::app()->session['social_account'] = array(
            'access_token' => $token['access_token'],
            'account_type' => SocialAccountAR::TYPE_WEIBO,
            'account_id' => $uid,
        );

        $record = SocialAccountAR::model()->findByAttributes(array(
            'type' => SocialAccountAR::TYPE_WEIBO,
            'open_id' => $uid,
                ));

        if ($record == null) {
            $user_info = $client->show_user_by_id($uid);
            Yii::app()->session['social_account'] = array_merge(Yii::app()->session['social_account'], array(
                'name' => $user_info['screen_name'],
                'avatar' => $user_info['avatar_large'],
                'sex' => $user_info['gender'],
                'location' => $user_info['location'],
            ));
            $this->redirect('/account/signup');
        } else {
            $identity = new UserIdentity('dummy', 'dummy');
            $identity->authenticateUser($record->user_id);
            if ($identity->errorCode == UserIdentity::ERROR_NONE)
                Yii::app()->user->login($identity);
            $this->redirectBack();
        }
    }

    public function actionQQ() {
        require_once 'QQ/QQConfig.php';
        $config = QQConfig::getConfig();

        Yii::app()->session['state'] = md5(uniqid(rand(), TRUE));
        $login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
                . $config->AppKey . "&redirect_uri=" . urlencode($config->CallbackUrl)
                . "&state=" . Yii::app()->session['state']
                . "&scope=" . urlencode($config->scope);
        Yii::app()->session['reurl'] = Yii::app()->request->urlReferrer;
        $this->redirect($login_url);
    }

    public function actionQQCallback($code, $state) {
        require_once 'QQ/QQConfig.php';
        $config = QQConfig::getConfig();

        if ($state != Yii::app()->session['state'])
            return $this->redirect('/login/qq');
        unset(Yii::app()->session['state']);

        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
                . "client_id=" . $config->AppKey . "&redirect_uri=" . urlencode($config->CallbackUrl)
                . "&client_secret=" . $config->SecretKey . "&code=" . $code;

        $response = file_get_contents($token_url);
        if (strpos($response, "callback") !== false) {
            MuleApp::log('qq login failed');
            return;
        }

        $params = array();
        parse_str($response, $params);
        $access_token = $params['access_token'];

        $str = file_get_contents("https://graph.qq.com/oauth2.0/me?access_token=" . urlencode($access_token));
        if (strpos($str, "callback") !== false) {
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str = substr($str, $lpos + 1, $rpos - $lpos - 1);
        }
        $arr = json_decode($str);
        $open_id = $arr->openid;
        
        Yii::app()->session['social_account'] = array(
            'access_token' => $access_token,
            'account_type' => SocialAccountAR::TYPE_QQ,
            'account_id' => $open_id,
        );

        $record = SocialAccountAR::model()->findByAttributes(array(
            'type' => SocialAccountAR::TYPE_QQ,
            'open_id' => $open_id,
                ));

        if ($record == null) {
            $get_user_info = "https://graph.qq.com/user/get_user_info?"
                    . "access_token=" . urlencode($access_token)
                    . "&oauth_consumer_key=" . urldecode($config->AppKey)
                    . "&openid=" . urlencode($open_id)
                    . "&format=json";

            $info = file_get_contents($get_user_info);
            $arr = json_decode($info, true);
            if ($arr['ret'] == 0) {
                Yii::app()->session['social_account'] = array_merge(Yii::app()->session['social_account'], array(
                    'name' => $arr['nickname'],
                    'avatar' => $arr['figureurl_2'],
                    'sex' => $arr['gender'],
                ));
                $this->redirect('/account/signup');
            }
        } else {
            $identity = new UserIdentity('dummy', 'dummy');
            $identity->authenticateUser($record->user_id);
            if ($identity->errorCode == UserIdentity::ERROR_NONE)
                Yii::app()->user->login($identity);
            $this->redirectBack();
        }
    }

    public function actionDouban() {
        require_once 'Douban/DoubanConfig.php';
        $config = DoubanConfig::getConfig();

        $o = new DoubanOAuthV2($config->AppKey, $config->SecretKey);
        $login_url = $o->getAuthorizeURL($config->CallbackUrl);
        $login_url = "https://www.douban.com/service/auth2/auth" . $login_url;
        Yii::app()->session['reurl'] = Yii::app()->request->urlReferrer;
        $this->redirect($login_url);
    }

    public function actionDoubanCallback($code) {
        require_once 'Douban/DoubanConfig.php';
        $config = DoubanConfig::getConfig();

        $o = new DoubanOAuthV2($config->AppKey, $config->SecretKey);
        $keys = array();
        $keys['code'] = $code;
        $keys['redirect_uri'] = $config->CallbackUrl;
        try {
            $token = $o->getAccessToken('code', $keys);
        } catch (OAuthException $e) {
            return $this->redirect('/login/douban');
        }
        $access_token = $token['access_token'];
        $refresh_token = $token['refresh_token'];
        $open_id = $token['douban_user_id'];
        
        Yii::app()->session['social_account'] = array(
            'access_token' => $access_token,
            'account_type' => SocialAccountAR::TYPE_DOUBAN,
            'account_id' => $open_id,
        );
        
        $record = SocialAccountAR::model()->findByAttributes(array(
            'type' => SocialAccountAR::TYPE_DOUBAN,
            'open_id' => $open_id,
                ));

        if ($record == null) {
            $client = new Douban_Tclientv2($config->AppKey, $config->SecretKey, $access_token, $refresh_token);
            //$user_info = $client->user_login();
            $user_info = $client->shuo_userinfo($open_id);
            Yii::app()->session['social_account'] = array_merge(Yii::app()->session['social_account'], array(
                'name' => $user_info['screen_name'],
                'avatar' => $user_info['large_avatar'],
                'location' => $user_info['city'],
            ));
            $this->redirect('/account/signup');
        } else {
            $identity = new UserIdentity('dummy', 'dummy');
            $identity->authenticateUser($record->user_id);
            if ($identity->errorCode == UserIdentity::ERROR_NONE)
                Yii::app()->user->login($identity);
            $this->redirectBack();
        }
    }

}
