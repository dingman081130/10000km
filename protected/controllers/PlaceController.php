<?php

Yii::import('application.models.User.User');
Yii::import('application.models.User.UserSearch');
Yii::import('application.models.User.UserSuggest');
Yii::import('application.models.City.City');
Yii::import('application.models.Place.Place');
Yii::import('application.models.Group.Group');
Yii::import('application.models.Group.Topic');

class PlaceController extends Controller {
    
    protected $defaultPageInfo =  array(
        'view' => array(
            'keywords' => '四海客,求沙发,导游,同城,驴友,旅行,沙发客',
            'description' => '四海客是一种旅行方式，意为五湖四海的旅行者。旅行者可以随时寻找当地沙发，和当地人交朋友，体验当地生活。',
        )
    );

    public function actionIndex($kw=null) {
        if (empty($kw)) {
            $city_id = Place::getClientCity();
        } else {
            $city_id = Place::searchCity($kw);
        }
        if (empty($city_id)) $city_id = 31;
        $city = City::getCity($city_id);
        $pinyin = $city['pinyin'];
        $up_city = City::getCity($city['upid']);
        if ($up_city) $pinyin = $up_city['pinyin'];
        $this->redirect('/place/' . $pinyin);
    }
    

    public function actionView($pinyin) {
        $city = City::getCityByPinyin($pinyin);
        if (!$city)
            throw new CHttpException(404);
        
        $this->pageTitle = $city['name'] . '的同城旅行者';
        //if (Place::getClientCity() == $city['id']) {
        //    $users = UserSearch::getCitySurfers(array('city_id' => $city['id']), 0, 6);
        //    $type = 'surfer';
        //} else {
            $users = UserSearch::getCityLocals(array('city_id' => $city['id']), 0, 6);
            $type = 'local';
        //}
        
        $user = User::getUser(Yii::app()->user->id);
        $this->render('view', array(
            'place' => $city,
            'type' => $type,
            'users' => $users,
            'user' => $user, // current user
            'topics' => Topic::getTopicsByCity($city['id']),
            'groups' => Group::getGroupsByCity($city['id']),
        ));
    }
    

}