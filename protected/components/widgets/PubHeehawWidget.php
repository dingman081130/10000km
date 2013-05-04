<?php
/**
 * @file class
 * @package application.components.Widgets
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-14
 * @version
 */

class PubHeehawWidget extends CWidget{
   
    public function run(){
        echo '<div class="heehaw">';
        $this->render('pubHeehawWidget', array());
        echo '</div>';
    }
}
