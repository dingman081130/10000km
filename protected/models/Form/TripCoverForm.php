<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TripCoverForm
 *
 * @author yemin
 */
class TripCoverForm extends CFormModel{
    //put your code here
    
    public $cover;
    
    public function rules() {
        return array(
            array('cover', 'file', 'types' => 'jpeg, jpg, png, gif', 'maxSize' => 2*1024*1024, 'allowEmpty' => false),
        );
    }
}

?>
