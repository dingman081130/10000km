<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="shortcut icon" href="/img/favicon.png" type="image/png">
        
        <title><?php echo CHtml::encode($this->pageTitle); ?> - 一万公里旅行</title>
        <meta name="keywords" content="<?php echo CHtml::encode($this->pageKeywords);  ?>">
        <meta name="description" content="<?php echo CHtml::encode($this->pageDescription); ?>">
        <meta name="csrf_token_name" content="<?php echo Yii::app()->request->csrfTokenName; ?>">
        <meta name="csrf_token_value" content="<?php echo CHtml::encode(Yii::app()->request->csrfToken); ?>">
        
        <?php
        $static_base = Yii::app()->params['staticBaseUrl'];
        $ver = Yii::app()->params['version'];
        ?>
        <link rel="stylesheet" href="<?php echo $static_base;?>css/bootstrap.css">
        <link rel="stylesheet" href="<?php echo $static_base;?>css/jquery.tagsinput.css">
        <link rel="stylesheet" href="/css/custom-theme/jquery-ui-1.9.2.custom.css">
        <link rel="stylesheet" href="<?php echo $static_base;?>css/jquery.emotion.css">
        <link rel="stylesheet" href="<?php echo $static_base;?>css/jquery.qtip.css">
        <link rel="stylesheet" href="<?php echo $static_base;?>css/jquery.fileupload-ui.css">
        <link rel="stylesheet" href="<?php echo $static_base;?>css/redactor.css">
        <link rel="stylesheet" href="/css/style.css?v=<?php echo $ver; ?>">

        <script src="<?php echo $static_base;?>js/jquery-1.8.3.min.js"></script>
        <script src="<?php echo $static_base;?>js/jquery-ui-1.9.2.custom.js"></script>
        <script src="<?php echo $static_base;?>js/bootstrap.js"></script>
        <script src="<?php echo $static_base;?>js/jquery.qtip.js"></script>
        <script src="<?php echo $static_base;?>js/jquery.jeditable.js"></script>
        <script src="<?php echo $static_base;?>js/jquery.emotion.js"></script>
        <script src="<?php echo $static_base;?>js/jquery.form.js"></script>
        <script src="<?php echo $static_base;?>js/jquery.tagsinput.js"></script>
        <script src="<?php echo $static_base;?>js/zh_cn.js"></script>
        <script src="<?php echo $static_base;?>js/redactor.js"></script>
        <script src="<?php echo $static_base;?>js/html5.js"></script>
        
        <script src="/js/main.js?v=<?php echo $ver; ?>"></script>
    </head>
    <body>
        <div class="container">
            <div class="navbar navbar-fixed-top">
                <div class="navbar-inner">
                    <div class="container">
                        <a href="/" class="brand" style="padding: 0;"><img style="height: 30px;margin-bottom: 3px; margin-top: 5px;" src="/img/logo.png"></a>
                        <ul class="nav">
                            <li><a href="/place" style="font-size: 20px; padding: 12px 20px 10px 20px;"><b>四海客</b></a></li>
                            <li><a href="/trip" style="font-size: 20px; padding: 12px 20px 10px 20px;"><b>结伴</b></a></li>
                            <li><a href="/group" style="font-size: 20px; padding: 12px 20px 10px 20px;"><b>小组</b></a></li>
                        </ul>

                        <ul class="nav pull-right">
                            <li><div class="input-append" style="margin-top: 7px; margin-right: 15px;">
                                    <form action="/place" style="margin-bottom: 0;">
                                        <input class="span3" type="text" name="kw" placeholder="你要去哪里?" autocomplete="off">
                                        <button class="btn" type="submit">搜索</button>
                                    </form>
                                </div>
                            </li>
                            <?php
                            $this->widget('AccountNavWidget');
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!--div id="ttips" class="navbar-fixed-top" style="display: none;text-align: center;position: fixed;top: 10px;left: 500px;width: 100px;">
                <a href="#">您有1条消息</a>
            </div-->

            <?php echo $content; ?>


            <footer class="clearfix main-footer">
                <div class="pull-left logo">
                    <img src="/img/logo.png">
                </div>
                <ul class="pull-left unstyled span5 about-help">
                    <li><a href="/">主页</a></li>
                    <li><a href="#">帮助</a></li>
                    <li><a href="/about">关于</a></li>
                </ul>
                <p class="pull-right span3">Copyright © 2012 10000km</p>
                <div class="hide">
                    <script src="http://s17.cnzz.com/stat.php?id=4955885&web_id=4955885" language="JavaScript"></script>
                </div>
                <?php
                if (!Yii::app()->user->id) {
                    $this->renderView(array('login', 'loginDlg'));
                    $this->renderView(array('login', 'registerDlg'));
                }
                ?>
            </footer>
        </div>
    </body>
</html>
