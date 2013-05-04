<?php
/**
 * @file class PushMessageTimerCommand 定时推送消息
 * 
 * @package application.commands
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */
Yii::import('application.models.Message.SysMessage');

class PushMessageTimerCommand extends CConsoleCommand{
    
    public function run($args){
        SysMessage::saveCTopMsg(0, 'push');
        SysMessage::saveJTripMsg(0, 0, 'push');
        echo 'Task ['.__CLASS__.'] finished @ ['.date('Y-m-d')."]\n";
    }
}