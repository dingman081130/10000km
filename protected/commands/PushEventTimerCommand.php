<?php
/**
 * @file class PushEventTimerCommand 定时推送事件
 * 像发图片这种需要进行集合的事件不由这个类来推
 * 
 * @package application.commands
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */

Yii::import('application.models.Event.EventPusher');

class PushEventTimerCommand extends CConsoleCommand{
    
    public function run($args){
        EventPusher::pushRestEvent();
        echo 'Task ['.__CLASS__.'] finished @ ['.date('Y-m-d')."]\n";
    }
}