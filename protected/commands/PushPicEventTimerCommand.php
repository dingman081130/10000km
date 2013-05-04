<?php
/**
 * @file class PushPicEventTimerCommand 定时推送图片事件
 * 
 * @package application.commands
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */

Yii::import('application.models.Event.EventPusher');
Yii::import('application.models.Event.EventCleaner');
Yii::import('application.models.Event.Event');
Yii::import('application.models.Album.Photo');

class PushPicEventTimerCommand extends CConsoleCommand{
    
    public function run($args){
        date_default_timezone_set("Asia/Shanghai");
        $time = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        //echo $time;exit;
        $albums = Photo::getPhotoByTimeGE($time);
        foreach($albums as $album){
            $aid = $album['album']['id'];
            EventCleaner::delAlbumEvent($aid);      // 删除该相册上次的事件
            $event = Event::addAlbumEvent($album);  // 新增该相册本次的事件
            EventPusher::pushEvent($event, true);
        }
        echo 'Task ['.__CLASS__.'] finished @ ['.date('Y-m-d')."]\n";
    }
}
