<?php
/**
 * @file class BreadCrumb 页面导航
 * @package application.components.widgets
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-1-6
 * @version
 */
class BreadCrumbWidget extends CWidget {
 
    public $crumbs = array();
    public $delimiter = '/';
 
    public function run() {
        $this->render('breadCrumbWidget', array(
            'crumbs' => $this->crumbs,
            'delimiter' => $this->delimiter,
        ));
    }
}