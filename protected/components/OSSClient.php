<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.vendors.*');
require_once 'oss/sdk.class.php';

/**
 * Description of OSSClient
 *
 * @author yemin
 */
class OSSClient {
    //put your code here
    
    const DEFAULT_BUCKET_NAME = '10000km';
    
    private $_oss_sdk_service;
    private $_bucket;
    
    private $access_id = 'gjjbxov7apvyah6scs6qsugs';
    private $access_key = 'WxGqb/Apd9dATYCRbkAgYBMRlQ4=';
    private $oss_host = 'oss.aliyuncs.com';
    private $internal_oss_host = 'oss-internal.aliyuncs.com';
    
    /**
     * 构造函数
     */
    public function __construct($bucket=self::DEFAULT_BUCKET_NAME) {
        $this->_oss_sdk_service = new ALIOSS();
        $this->_bucket = $bucket;
    }
    
    
    /**
     * 上传一个object
     * 
     * @param string $object 目标object，如js/jquery.js
     * @param string $file_path 本地文件路径，如/tmp/jquery.js
     * @return boolean
     */
    public function upload($object, $file_path) {
        $resp = $this->_oss_sdk_service->upload_file_by_file($this->_bucket, $object, $file_path);
        return $resp->isOK();
    }
    
    
    /**
     * 删除一个object
     * 
     * @param string $object 目标object
     * @return boolean
     */
    public function delete($object) {
        $resp = $this->_oss_sdk_service->delete_object($this->_bucket, $object);
        return $resp->isOK();
    }
}

?>
