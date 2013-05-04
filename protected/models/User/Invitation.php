<?php
/**
 * @file class Invitation 管理邀请码
 * 
 * @package application.models.User
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date
 * @version
 */

Yii::import('application.models.AR.InvitationAR');

class Invitation {
    //put your code here
    
    /**
     * 判断邀请码是否有效
     * @param string $code
     * @thorws CException
     */
    public static function isValid($code){
        //return InvitationAR::model()->exists("code = ? and cnt < max_cnt", array($code));
        $ar = InvitationAR::model()->find("code=?", array($code));
        if(!$ar){
            throw new CException('该邀请码不存在');
        }
        if($ar->cnt >= $ar->max_cnt){
            throw new CException("该邀请码已达到使用上限");
        }   
    }
    
    /**
     * 将某个邀请码的使用次数加一
     */
    public static function addCnt($code){
        $ar = InvitationAR::model()->find("code=?", array($code));
        if($ar){
            $ar->cnt ++;
            $ar->save();
        }
    }
}

?>
