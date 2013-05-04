<?php
/*
$login = 'caonima'; 
$pass = 'caonidaye'; 

if(($_SERVER['PHP_AUTH_PW']!= $pass || $_SERVER['PHP_AUTH_USER'] != $login)|| !$_SERVER['PHP_AUTH_USER']) 
{ 
    header('WWW-Authenticate: Basic realm="Test auth"'); 
    header('HTTP/1.0 401 Unauthorized'); 
    echo 'Auth failed'; 
    exit; 
}
*/
// change the following paths if necessary
$config=dirname(__FILE__).'/../protected/config/main.dev.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once('yii/yii.php');
Yii::createWebApplication($config)->run();