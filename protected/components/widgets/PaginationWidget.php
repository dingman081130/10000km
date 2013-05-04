<?php
/**
 * @file class PaginationWidget
 * @package application.components
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-15
 * @version
 */

class PaginationWidget extends CWidget{
   
    public $pagination;
    public $base_url;

    public function init(){
        parent::init();
    }

    protected function renderContent(){
        $this->render('paginationWidget', array(
            'pagination' => $this->pagination,
            'base_url' => $this->base_url,
        ));
    }

    public function run(){
        $this->renderContent();
    }
}
