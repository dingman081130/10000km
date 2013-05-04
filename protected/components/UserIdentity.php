<?php

Yii::import('application.models.AR.UserAR');
Yii::import('application.models.User.User');

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    public function authenticate() {
        $user = UserAR::model()->find('LOWER(login_email) = ?', array(strtolower($this->username)));
        if ($user == null)
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        else if (!$user->validatePassword($this->password)) 
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        else {
            $this->updateLoginInfo($user->id);
            $attrs = User::getUser($user->id);
            $this->username = $user->id;
            $this->setState('attrs', $attrs);
            $this->errorCode = self::ERROR_NONE;
        }
        return $this->errorCode == self::ERROR_NONE;
    }

    public function authenticateUser($user_id) {
        $user = User::getUser($user_id);
        if ($user == null) {
            $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
        } else {
            $this->updateLoginInfo($user_id);
            $this->username = $user_id;
            $this->setState('attrs', $user);
            $this->errorCode = self::ERROR_NONE;
        }
        return $this->errorCode == self::ERROR_NONE;
    }
    
    private function updateLoginInfo($user_id) {
        UserAR::model()->updateByPk($user_id, array(
            'last_login_time' => date('Y-m-d H:i:s'),
            'last_login_ip' => Utils::getClientIp(),
        ));
        User::delUserCache($user_id);
    }
}
