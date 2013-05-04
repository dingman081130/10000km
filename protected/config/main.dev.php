<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'language' => 'zh_cn',
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'runtimePath' => '/tmp', //暂时把这个目录放到svn工作目录之外
    'name' => '一万公里哦~~~',
    'defaultController' => 'index',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.components.*',
        'application.components.widgets.*'
    ),
    'modules' => array(
        // uncomment the following to enable the Gii tool
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('192.168.161.1', '127.0.0.1', '::1'),
        ),
    ),
    // application components
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'loginUrl' => null,
        ),
        // uncomment the following to enable URLs in path-format
        'urlManager' => array(
            'urlFormat' => 'path',
            'rules' => array(
                'gii'=>'gii',
                'gii/<controller:\w+>'=>'gii/<controller>',
                'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
                
                'place/<pinyin:\w+>' => 'place/view',
                'place/<pinyin:\w+>/<action:\w+>' => 'place/<action>',
                
                'user/<id:\d+>/<action:\w+>' => 'user/<action>',
                
                'search/in/<condition:.+>' => 'search/in',
                
                '<controller:\w+>/<id:\d+>(/<type:\w+>)?' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<type:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                
                
            ),
            'showScriptName' => false,
        ),
        /*
          'db'=>array(
          'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
          ),
         */
        // uncomment the following to use a MySQL database
        'db' => array(
            //'connectionString' => 'mysql:host=192.168.1.100;dbname=10000km',
            'connectionString' => 'mysql:host=localhost;dbname=10000km',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'enableProfiling' => true,
        ),
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
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
                // uncomment the following to show log messages on web pages
                /*
                array(
                    'class'=>'CWebLogRoute',
                ),
                 */
                // uncomment sql profile log report
                
                array(
                    'class' => 'CProfileLogRoute',
                    'report' => 'summary',
                ),
                
            ),
        ),
        'request' => array(
            'enableCsrfValidation' => true,
            'csrfTokenName' => 'csrf_token',
        ),
        'session' => array(
            'sessionName' => 'sid',
            'cookieParams' => array(
                'httponly' => true
                ),
        ),
        'email' => array(
            'class' => 'ext.KEmail.KEmail',
            'host_name' => 'smtp.exmail.qq.com',
            'ssl' => true,
            'host_port' => 465,
            'user' => 'admin@10000km.cn',
            'password' => '1wkmgogogo',
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
        'staticBaseUrl' => 'http://10000km.oss.aliyuncs.com/',
        'version' => 20130115,
        'mode' => 'dev',    // dev | prod | test
        'messagePageSize' => 15,    // 默认消息每页的大小
        'commentPageSize' => 5,
        'freshPageSize' => 5,
        'topicCommentPageSize' => 5,   // 话题评论每页展示多少
        'groupPageSize' => 6,
        'sysId' => 10000,   // 系统id
    ),
);
