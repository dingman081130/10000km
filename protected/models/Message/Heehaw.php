<?php
/**
 * @file class Heehaw 管理用户的驴叫信息
 * @package application.models.Message
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-25 00:42:01
 * @version
 */

Yii::import('application.models.AR.HeehawAR');
Yii::import('application.models.Event.EventListener');

class Heehaw{
    
    /**
     * 驴叫一声
     * @param int $user_id 用户id
     * @param string $msg 驴叫的内容
     * @param bool $is_push 是否推送消息给好友
     * @return Event
     */
    public static function pubHeehaw($user_id, $msg, $is_push = TRUE){
        $ar = new HeehawAR;
        $ar->user_id = $user_id;
        $ar->msg = $msg;
        $ar->saveL();
        $ret = EventListener::getListener()->run(array(
            'user_id' => $user_id,
            'content' => CJSON::encode(array(
                'msg' => $msg,
             )),
            'type'    => Event::HEEHAW,
        ));
        $ret['content'] = CJSON::decode($ret['content']);
        return $ret;
    }
    
}
