<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Yii::import('application.models.AR.UserAR');
Yii::import('application.models.User.User');

/**
 * Description of Account
 *
 * @author yemin
 */
class Account {
    //put your code here

    const TTL = 3600; // 1小时有效
    
    const ERROR_NONE = 0;
    const ERROR_MAIL_NOT_EXIST = 1;
    const ERROR_MAIL_FAILED = 2;
    const ERROR_RETRY = 3;
    

    public static function forgetPassword($email) {
        $user = UserAR::model()->findByAttributes(array(
            'login_email' => $email,
                ));
        if ($user) {
            $redis = RedisClient::getClient();
            $email = $user->login_email;

            $key = self::passwordResetKey($user->id);
            $obj = $redis->get($key);

            if ($obj) {
                if ($obj['times'] >= 5) {
                    return array(
                        'code' => -1,
                        'msg' => '您发送的邮件太多，请稍后再试',
                    );
                } else {
                    if (self::sendPasswordResetEmail($user->id, $obj['code'])) {
                        $obj['times']++;
                        $redis->setex($key, self::TTL, $obj);
                        return array(
                            'code' => 0,
                            'msg' => '已经发送重置密码邮件到您的邮箱，请点击邮箱中的链接重置您的密码',
                        );
                    } else {
                        return array(
                            'code' => -1,
                            'msg' => '发送邮件失败',
                        );
                    }
                }
            }

            $code = mt_rand();
            if (self::sendPasswordResetEmail($user->id, $code)) {
                $redis->setex($key, self::TTL, array(
                    'code' => $code,
                    'times' => 1,
                ));
                return array(
                    'code' => 0,
                    'msg' => '已经发送重置密码邮件到您的邮箱，请点击邮箱中的链接重置您的密码',
                );
            } else {
                return array(
                    'code' => -1,
                    'msg' => '发送邮件失败',
                );
            }
        } else {
            return array('code' => -1, 'msg' => '该邮箱未注册');
        }
    }

    public static function resetPassword($id, $code, $new_pwd) {
        $redis = RedisClient::getClient();
        $key = self::passwordResetKey($id);
        $obj = $redis->get($key);
        if (isset($obj['code']) && $obj['code'] == $code) {
            $redis->del($key);
            $user = UserAR::model()->findByPk($id);
            if ($user) {
                $user->password = $new_pwd;
                return $user->save();
            }
        }
        return false;
    }

    private static function passwordResetKey($id) {
        return "pwd_reset_$id";
    }

    public static function sendPasswordResetEmail($id, $code) {
        $user = User::getUser($id);
        if ($user) {
            $url = 'http://10000km.cn/account/reset?id=' . $user['id'] . '&code=' . urlencode($code);
            $body = "重置密码链接为<a href=\"$url\">$url</a>";
            $headers = array(
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=utf-8',
            );

            return Yii::app()->email->send(Yii::app()->email->user, $user['login_email'], '重置密码', $body, $headers);
        }
        return false;
    }

    public static function activateEmail($id) {
        $user = User::getUser($id);
        $redis = RedisClient::getClient();
        if ($user) {
            $key = self::activateKey($id);
            $obj = $redis->get($key);
            if ($obj) {
                if ($obj['times'] >= 5) {
                    return array(
                        'code' => -1,
                        'msg' => '邮件发送次数太多，请稍后再试',
                    );
                } else {
                    if (self::sendActivateEmail($id, $obj['code'])) {
                        $obj['times']++;
                        $redis->setex($key, self::TTL, $obj);
                        return array(
                            'code' => 0,
                            'msg' => '已经发送激活邮件到您的邮箱，请点击邮件中的链接激活账号',
                        );
                    } else {
                        return array(
                            'code' => -1,
                            'msg' => '发送邮件失败',
                        );
                    }
                }
            }

            $code = mt_rand();
            if (self::sendActivateEmail($user['id'], $code)) {
                $redis->setex($key, self::TTL, array(
                    'code' => $code,
                    'times' => 1,
                ));
                return array(
                    'code' => 0,
                    'msg' => '已经发送激活邮件到您的邮箱，请点击邮件中的链接激活账号',
                );
            } else {
                return array(
                    'code' => -1,
                    'msg' => '发送邮件失败',
                );
            }
        } else {
            return array(
                'code' => -1,
                'msg' => '不存在该账号',
            );
        }
    }

    /**
     * 向用户的邮箱发送一封激活邮件
     * 
     * @param integer $id
     * @return boolean
     */
    public static function sendActivateEmail($id, $code) {
        $user = User::getUser($id);
        if ($user) {
            $url = "http://10000km.cn/account/activate?id=$id&code=$code";
            $body = "您的激活链接为<a href=\"$url\">$url</a>，点击该链接激活您的一万公里账号。请认准一万公里域名，谨防假冒钓鱼哦～～～";
            $headers = array(
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=utf-8',
            );

            return Yii::app()->email->send(Yii::app()->email->user, $user['login_email'], '激活您的一万公里账号', $body, $headers
            );
        }
        return false;
    }

    public static function activateVerify($id, $code) {
        $key = self::activateKey($id);
        $obj = RedisClient::getClient()->get($key);
        if (isset($obj['code']) && $obj['code'] == $code) {
            $ret = UserAR::model()->updateByPk($id, array(
                'email_verified' => 1,
                    ));
            if ($ret) {
                User::delUserCache($id);
                return true;
            }
        }
        return false;
    }

    private static function activateKey($id) {
        return "user_activate_$id";
    }

    /**
     * 检查用户能否使用该username
     * 
     * @param integer $user_id 用户的id。在注册时用户还没有id，此时应该传入null。
     * @param string $name 需要检查的用户名
     * @return array
     */
    public static function usernameAvailable($user_id, $name) {
        if (!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', $name)
            || preg_match('/^\d/u', $name)) {
            return array('code' => -1, 'msg' => '用户名只能包含英文字母、汉字、数字、下划线，不能以数字开头');
        }
        if (strlen($name) < 5)
            return array('code' => -1, 'msg' => '太短了哦～～，加油！！');
        else if (strlen($name) > 45)
            return array('code' => -1, 'msg' => '好长啊～～～，受不了～～');

        if ($user_id == null && UserAR::model()->exists('name = :name', array(':name' => $name,))) {
            return array('code' => -1, 'msg' => '该用户名已经被注册');
        } else {
            if (UserAR::model()->exists('name = :name and id != :id', array(
                        ':name' => $name,
                        ':id' => $user_id,
                    )))
                return array('code' => -1, 'msg' => '该用户名已经被注册');
        }
        return array('code' => 0, 'msg' => '该用户名可用');
    }

    /**
     * 检查一个email是否可用来注册
     * 
     * @param string $email
     * @return boolean 该邮箱地址可用时返回true，否则返回false
     */
    public static function emailAvailable($email) {
        return !UserAR::model()->exists('login_email = :email', array(
                    'email' => $email,
                ));
    }

    public static function bindSocialAccount($uid, $openid, $type) {
        $social_account = new SocialAccountAR();
        $social_account->open_id = $openid;
        $social_account->type = $type;
        $social_account->user_id = $uid;
        return $social_account->save();
    }
    
    
    public static function unbindSocialAccount($uid, $type) {
        return SocialAccountAR::model()->deleteAllByAttributes(array(
            'user_id' => $uid,
            'type' => $type,
        )) > 0;
    }

    public static function isSocialAccountBinded($uid, $type) {
        return SocialAccountAR::model()->exists('user_id = :uid and type = :type', array(
                    ':uid' => $uid,
                    ':type' => $type,
                ));
    }

}

?>
