<?php

Yii::import('application.models.AR.*');
Yii::import('application.models.Emotion.Emotion');
Yii::import('application.models.City.City');

class ApiController extends Controller
{
    
    public function actionGetChildCities($id) {
        $cities = City::getChildCities($id);
        $ret = array();
        foreach ($cities as $city) {
            $ret[] = array('id' => $city['id'], 'name' => $city['name']);
        }
        $this->returnJson($ret);
    }

    public function actionDstAutocomplete($term) {
        $this->returnJson(City::cityAutocomplete(trim($term)));
    }
    
    public function actionEmotions($callback) {
        $data = array();
        $emotions = Emotion::getAllEmotions();
        
        foreach($emotions as $emotion) {
            $data[] = array(
                'category' => $emotion['category'],
                'phrase' => $emotion['phrase'],
                'url' => Yii::app()->params['staticBaseUrl'].'/img/emotions/'.$emotion['filename'],
                'value' => $emotion['value'],
                'hot' => $emotion['hot'],
            );
        }
        $this->returnJsonp($callback, $data);
    }
    
    public function actionGetCurrentLocation() {
        $location = Utils::getClientLocation();
        $geocoder_result = Utils::geoCoderLocation($location);
        
        if ($geocoder_result) {
            return $this->returnJson(array(
                'code' => 0,
                'location' => $location,
                'lng' => $geocoder_result['lng'],
                'lat' => $geocoder_result['lat'],
            ));
        }
        return $this->returnJson(array(
            'code' => -1,
            'msg' => '获取位置失败',
        ));
    }
    
}