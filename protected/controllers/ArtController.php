<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ArtController
 *
 * @author dingfei
 */
class ArtController extends Controller{
    //put your code here

    
    public function actionPain(){
        $this->render('pain');
    }
    
    public function actionSubmit(){
        
        if(isset($_POST['data'])){
            $imageData=$_POST['data'];
            $filteredData=substr($imageData,strpos($imageData, ",")+1);
            $unencodedData=base64_decode($filteredData);
            $fp = fopen( '/tmp/'.time().'.png', 'wb' );
            fwrite( $fp, $unencodedData);
            fclose( $fp );
        }
    }
}
