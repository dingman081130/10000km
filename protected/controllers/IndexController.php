<?php

Yii::import('application.models.City.City');

class IndexController extends Controller {
    
    protected $defaultPageInfo = array(
        'index' => array(
            'title' => '一万公里旅行网',
            'keywords' => '一万公里旅行网,一万公里穷游网,拼客,拼玩,拼车,在路上,找驴友,捡人,求捡,四海客,背包客,沙发客,穷游网,间隔年,骑行,搭车,徒步,地接穷游',
            'description' => '一万公里旅行网，为您提供实时的捡人，求拣，拼客，拼玩以及沙发客，同城导游等信息的在线旅行平台。是沙发客旅行，背包客旅行、搭车旅行、徒步旅行、骑行、打工旅行、摆摊旅行等旅行者的交流平台',
        )
    );

    public function actionIndex() {
        $this->renderPartial('index');
    }
    
    public function actionChildCities($id) {
        $city = City::getCity($id);
        if ($city) {
            $child_cities = City::getChildCities($id);
            if (empty($child_cities)) {
                $child_cities = array($city);
            }
            $this->renderPartial('childCities', array(
                'child_cities' => $child_cities,
            ));
        }
    }
    
}
