<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.User.User');
Yii::import('application.models.User.UserSearch');
Yii::import('application.models.Place.Place');
Yii::import('application.models.Search.Search');
Yii::import('application.models.City.City');

/**
 * Description of SearchController
 *
 * @author yemin
 */
class SearchController extends Controller {

//put your code here
    
    const PAGE_SIZE = 6;

    private $city_search = array(
        'city',
        'city_id',
        'user_type',
        'start_age',
        'end_age',
        'sex',
        'photo',
    );
    private $area_search = array(
        'area',
        'sw_lng',
        'sw_lat',
        'ne_lng',
        'ne_lat',
        'user_type',
        'start_age',
        'end_age',
        'sex',
        'photo',
    );

    private function parseCondition($condition) {

        $params = explode(',', $condition);
        $type = isset($params[0]) ? $params[0] : '';

        if ($type == $this->city_search[0]) {
            $ret = array(
                'type' => 'city',
            );
            for ($i = 1; $i < count($this->city_search); $i++) {
                $ret[$this->city_search[$i]] = isset($params[$i]) ? $params[$i] : null;
            }
            return $ret;
        }

        if ($type == $this->area_search[0]) {
            $ret = array(
                'type' => 'area',
            );
            for ($i = 1; $i < count($this->area_search); $i++) {
                $ret[$this->area_search[$i]] = isset($params[$i]) ? $params[$i] : null;
            }
            return $ret;
        }
        return null;
    }

    private function buildCondition($type, $params) {
        $cond = array();
        if ($type == 'city') {
            $cond[] = 'city';
            for ($i = 1; $i < count($this->city_search); $i++) {
                $p = $this->city_search[$i];
                $cond[] = isset($params[$p]) ? $params[$p] : '';
            }
        } else if ($type == 'area') {
            $cond[] = 'area';
            for ($i = 1; $i < count($this->area_search); $i++) {
                $p = $this->area_search[$i];
                $cond[] = isset($params[$p]) ? $params[$p] : '';
            }
        }
        return implode(',', $cond);
    }

    public function actionIn($condition) {
        $params = $this->parseCondition($condition);
        if (!$params) {
            throw new CHttpException(404);
        }

        $users = null;
        if ($params['type'] == 'city') {
            $city = City::getCity($params['city_id']);
            if ($params['user_type'] == 'local') {
                $this->pageTitle = $city['name'] . '的当地人';
                $users = UserSearch::getCityLocals($params, 0, self::PAGE_SIZE);
            } else if ($params['user_type'] == 'surfer') {
                $this->pageTitle = $city['name'] . '的沙发客';
                $users = UserSearch::getCitySurfers($params, 0, self::PAGE_SIZE);
            } else if ($params['user_type'] == 'traveler') {
                $this->pageTitle = '在'.$city['name'] . '旅行的人';
                $users = UserSearch::getCityTravelers($params, 0, self::PAGE_SIZE);
            } else if ($params['user_type'] == 'host') {
                $this->pageTitle =  $city['name'] . '的沙发主';
                $users = UserSearch::getCityHosts($params, 0, self::PAGE_SIZE);
            }
        }

        if ($params['type'] == 'area') {
            if ($params['user_type'] == 'local') {
                $this->pageTitle = '当地人';
                $users = UserSearch::getAreaLocals($params, 0, self::PAGE_SIZE);
            } else if ($params['user_type'] == 'surfer') {
                $this->pageTitle = '沙发客';
                $users = UserSearch::getAreaSurfers($params, 0, self::PAGE_SIZE);
            } else if ($params['user_type'] == 'traveler') {
                $this->pageTitle = '旅行者';
                $users = UserSearch::getAreaTravelers($params, 0, self::PAGE_SIZE);
            } else if ($params['user_type'] == 'host') {
                $this->pageTitle = '沙发主';
                $users = UserSearch::getAreaHosts($params, 0, self::PAGE_SIZE);
            }
        }
        
        if (is_array($users)) {
            $this->render('index', array(
                'users' => $users,
                'params' => $params,
            ));
        }
    }

    public function actionSearch($kw) {
        $city_id = Place::searchCity($kw);
        if (!$city_id) {
            return $this->returnJson(array(
                        'code' => -1,
                        'msg' => '未找到符合条件的地点',
                    ));
        }

        $city = City::getCity($city_id);
        $this->returnJson(array(
            'code' => 0,
            'city_id' => $city['id'],
        ));
    }

    public function actionCity($offset = 0) {
        $users = null;
        $params = $_GET;
        $user_type = isset($params['user_type']) ? $params['user_type'] : null;
        if ($user_type == 'local') {
            $users = UserSearch::getCityLocals($params, $offset, self::PAGE_SIZE);
        } else if ($user_type == 'surfer') {
            $users = UserSearch::getCitySurfers($params, $offset, self::PAGE_SIZE);
        } else if ($user_type == 'traveler') {
            $users = UserSearch::getCityTravelers($params, $offset, self::PAGE_SIZE);
        } else if ($user_type == 'host') {
            $users = UserSearch::getCityHosts($params, $offset, self::PAGE_SIZE);
        }

        if (is_array($users)) {
            $html = '';
            foreach ($users as $user) {
                $html .= $this->renderPartial('userItem', array(
                    'user' => $user,
                        ), true);
            }
            $this->returnJson(array(
                'code' => 0,
                'html' => $html,
                'cond' => $this->buildCondition('city', $params),
                'location' => Helpers::cityName($params['city_id']),
            ));
        }
    }

    public function actionArea($offset = 0) {
        $params = $_GET;
        $user_type = isset($params['user_type']) ? $params['user_type'] : null;
        $users = null;

        if ($user_type == 'local') {
            $users = UserSearch::getAreaLocals($params, $offset, self::PAGE_SIZE);
        } else if ($user_type == 'traveler') {
            $users = UserSearch::getAreaTravelers($params, $offset, self::PAGE_SIZE);
        } else if ($user_type == 'surfer') {
            $users = UserSearch::getAreaSurfers($params, $offset, self::PAGE_SIZE);
        } else if ($user_type == 'host') {
            $users = UserSearch::getAreaHosts($params, $offset, self::PAGE_SIZE);
        }

        if (is_array($users)) {
            $html = '';
            foreach ($users as $user) {
                $html .= $this->renderPartial('userItem', array(
                    'user' => $user,
                        ), true);
            }
            $this->returnJson(array(
                'code' => 0,
                'html' => $html,
                'location' => '地图区域',
                'cond' => $this->buildCondition('area', $params),
            ));
        }
    }

    public function actionCurrentCity() {
        $city_id = Place::getClientCity();
        if ($city_id) {
            return $this->returnJson(array(
                        'code' => 0,
                        'city_id' => $city_id,
                    ));
        }
        $this->returnJson(array(
            'code' => -1,
            'msg' => '无法定位城市',
        ));
    }

    public function actionIndex() {
        $city_id = Place::getClientCity();
        if (!$city_id) $city_id = 31;
        $this->redirect("/search/in/city,$city_id,local");
    }

}

?>
