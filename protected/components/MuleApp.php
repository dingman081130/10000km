<?php
/**
 * class MuleApp file.
 * 
 * @package application.components
 */

/**
 * class MuleApp.
 */
class MuleApp
{
    /**
     * 记录日志，在消息中添加用户名信息
     *
     * 这是对Yii::log的简单封装。为了方便追查问题，用户操作的日志信息是很重要的。在记录日志
     * 时，可以提供分类值，比如noah.perm, noah.user等，
     * 按照page目录下的分类即可。详见Yii的log机制。
     * 
     * 默认的信息level是'info'。默认会记录用户的名字，所以通常不需要特别指定。
     *
     * @param string $message 日志信息
     * @param integer $level 日志级别，默认是'info'
     * @param string $category 日志分类，默认是olive
     * @param string $user 用户名
     */
    public static function log($message, $level='info', $category='mule', $user=null)
    {
        if (php_sapi_name() == 'cli') {
            $url = __FILE__;
            $user===null && $user = 'guest';
        }
        else {
            $url = Yii::app()->request->requestUri;
            $user===null && $user = Yii::app()->user->name;
            
        }       
        $msg = "[$user] [$url] [$message]";
        
        Yii::log($msg, $level, $category);
    }
}

