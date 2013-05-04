<?php
/**
 * @file class UserWidget
 * @package application.components
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-16
 * @version
 */

class UserWidget extends CWidget{
   
    public $user;

    public function run(){
        echo $this->render('userWidget',array('user'=>$this->user));
    }
}
