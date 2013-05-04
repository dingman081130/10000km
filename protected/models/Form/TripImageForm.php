<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TripImageForm
 *
 * @author yemin
 */
class TripImageForm extends CFormModel{
    //put your code here
    
    public $image;
    
    public function rules() {
        return array(
            array('image', 'file', 'types' => 'jpg, png, gif', 'maxSize' => 5*1024*1024, 'allowEmpty' => false),
        );
    }
}

?>
