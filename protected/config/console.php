<?php

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    //'runtimePath' => '/tmp', //暂时把这个目录放到svn工作目录之外
    'name' => '一万公里后台应用',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.components.*',
    ),
    'modules' => array(
    ),
    // application components
    'components' => array(
        /*
        'user' => array(
            // enable cookie-based authentication
            'class' => 'application.components.UserIdentity',
            'allowAutoLogin' => true,
            'loginUrl' => null,
            ),
         */
        // uncomment the following to use a MySQL database
        'db' => array(
            //'connectionString' => 'mysql:host=192.168.1.100;dbname=10000km',
            'connectionString' => 'mysql:host=localhost;dbname=10000km',
            'emulatePrepare' => true,
            'username' => '10000km',
            'password' => '10000km',
            'charset' => 'utf8',
            'enableProfiling' => true,
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error,warning',
                    'logFile' => 'muleErrWarn.log',
                    'logPath' => '/var/log/mule/',
                    'maxFileSize' => 500000,
                    'maxLogFiles' => 1,
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error,warning,info',
                    'categories' => 'system.db.*',
                    'logFile' => 'muleAR.log',
                    'logPath' => '/var/log/mule/',
                    'maxFileSize' => 500000,
                    'maxLogFiles' => 3,
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info,error',
                    'categories' => 'mule',
                    'logFile' => 'muleInfo.log',
                    'logPath' => '/var/log/mule/',
                    'maxFileSize' => 500000,
                    'maxLogFiles' => 2,
                ),
            ),
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        'sysId' => 10000,   // 系统id
    ),
);
