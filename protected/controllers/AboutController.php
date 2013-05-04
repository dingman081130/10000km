<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.User.User');

/**
 * Description of AboutController
 *
 * @author yemin
 */
class AboutController extends Controller{
    //put your code here
    
    protected $defaultPageInfo = array(
        'index' => array('title' => '关于一万公里旅行网'),
    );


    public function actionIndex() {
        $this->render('about');
    }
    
    
    public function actionState() {
        $this->auth();
        $ids = Yii::app()->db->createCommand()
                ->select('id')
                ->from('user')
                ->where('to_days(register_time) = to_days(now())')
                ->queryColumn();
        $total = Yii::app()->db->createCommand()
                ->select('count(id)')
                ->from('user')
                ->queryScalar();
        
        $today_users = User::getUsers($ids);
        $this->render('state', array(
            'users' => $today_users,
            'total' => $total,
        ));
    }
    
    private function auth() {
        $login = 'caonima'; 
        $pass = 'caonidaye'; 

        if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW']!= $pass || $_SERVER['PHP_AUTH_USER'] != $login) 
        { 
            header('WWW-Authenticate: Basic realm="Test auth"'); 
            header('HTTP/1.0 401 Unauthorized'); 
            echo 'Auth failed'; 
            exit; 
        }
    }
}

?>
