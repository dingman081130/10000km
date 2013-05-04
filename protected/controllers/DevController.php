<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.Event.*');
Yii::import('application.models.User.User');
Yii::import('application.models.AR.CityAR');


class DevController extends Controller {
    
    public function actionTest1(){
        //echo Yii::app()->user->id;
        $redis = RedisClient::getClient();
        $ret = $redis->zRange('EU_'.$_GET['uid'],0,100);
        print_r($ret);exit;
        
    }
    
    public function actionTest2(){
        $arr = array(
            '慕士塔格峰',
            '塔县',
            '塔湖',
            '帕米尔高原'
        );
        Utils::amputate($arr);
    }
    
    public function actionTest3(){
        echo intval(1);echo '<br>';
        echo intval(1.1);echo '<br>';
        echo intval(1.9);echo '<br>';
        echo intval(2);echo '<br>';
    }
    
    public function actionTest4(){
        imagecreatefromjpeg('domgfeo');
    }
    
    public function actionTest5(){
        EventPusher::genPushPicEvent();
    }
    
    public function actionSuggest() {
        $users = User::suggestUser(Yii::app()->user->id);
        var_dump($users);
    }
    
    public function actionAaa() {
        Yii::import('ext.iwi.Iwi');
        $img = new Iwi('/tmp/1.jpg');
        echo $img->width;
    }
    
    public function actionWebSocket(){
        //sleep(4);
        $this->renderPartial("webSocket");
    }
    
    public function actionfile(){
        if(Yii::app()->request->isPostRequest){
            $ins = CUploadedFile::getInstanceByName('image');
            print_r($_FILES);
            var_dump($ins);
        }else{
            $this->renderPartial('upfile');
        }
    }
    
    public function actionSaveSignup(){
        Yii::import('application.models.Message.SysMessage');
        SysMessage::saveSingup(2);
    }
}
