<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {
    
    public $pageKeywords;
    public $pageDescription;

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/column1';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();

    public function returnJson($data) {
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($data);
        Yii::app()->end();
    }
    
    public function returnJsonp($callback, $data) {
        header('Content-type: application/javascript; charset=utf-8');
        echo $callback . '(' . json_encode($data) . ')';
        Yii::app()->end();
    }
    
    public function returnText($txt) {
        header('Content-type: text/plain; charset=utf-8');
        echo $txt;
        Yii::app()->end();
    }
    
    public function init() {
        if (Yii::app()->user->id) {
            Yii::import('application.models.User.User');
            User::updateLastOnlineTime();
        }
        return parent::init();
    }
    public function renderView($path, $data=null, $return = false){
        $viewFile = Yii::app()->basePath.'/views/'.$path[0].'/'.$path[1].'.php';
        return $this->renderFile($viewFile, $data, $return);
    }
    
    public function beforeAction($action) {
        if (isset($this->defaultPageInfo) && array_key_exists($action->id, $this->defaultPageInfo)) {
            $info = $this->defaultPageInfo[$action->id];
            
            if (array_key_exists('title', $info)) {
                $this->pageTitle = $info['title'];
            }
            
            if (array_key_exists('description', $info)) {
                $this->pageDescription = $info['description'];
            }
            
            if (array_key_exists('keywords', $info)) {
                $this->pageKeywords = $info['keywords'];
            }
        }
        
        return parent::beforeAction($action);
    }

}