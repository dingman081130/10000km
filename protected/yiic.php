<?php

// change the following paths if necessary
// 上线前注视掉这一行
 defined('YII_DEBUG') or define('YII_DEBUG',true);

$config = defined('YII_DEBUG') ? dirname(__FILE__).'/config/console.dev.php' : dirname(__FILE__).'/config/console.php';

require_once('yii/yiic.php');
