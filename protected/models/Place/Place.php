<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('application.models.AR.UserProfileAR');
Yii::import('application.models.AR.SceneryAR');
Yii::import('application.models.AR.CityAR');

/**
 * Description of Place
 *
 * @author yemin
 */
class Place {

    //put your code here




    

    /**
     * 获取用户当前所在的城市
     * 
     * @return integer 当前用户所在的城市id
     */
    public static function getClientCity() {
        $location = Utils::getClientLocation();
        if (!$location)
            return null;

        $province = $location['province'];
        $city = $location['city'];
        $ret = Yii::app()->db->createCommand()
                ->select('c.id')
                ->from('city as c')
                ->leftJoin('city as p', 'c.upid = p.id')
                ->where('c.name = :cname and p.name=:pname or c.name = :cname and p.name is null', array(
                    ':cname' => $city,
                    ':pname' => $province,
                ))
                ->queryScalar();
        if ($ret) {
            return $ret;
        } else {
            return null;
        }
    }

    /**
     * 根据关键词查找离它最近的城市id
     * 
     * @param string $kw 关键词
     * @return integer return city_id or null
     */
    public static function searchCity($kw) {
        $kw = trim($kw);
        $scenery = SceneryAR::model()->findByAttributes(array(
            'name' => $kw,
        ));
        if ($scenery) {
            return $scenery['city_id'];
        }
        
        $city_id = Yii::app()->db->createCommand()
                ->select('id')
                ->from('city')
                ->where('name = :kw or pinyin = :kw', array(':kw' => $kw))
                ->queryScalar();
        if ($city_id) {
            return $city_id;
        }
        
        $geo = Utils::geoCoderLocation($kw);
        if (!$geo) {
            return null;
        }

        $lng = $geo['lng'];
        $lat = $geo['lat'];
        //var_dump($geo);die;
        return Yii::app()->db->createCommand()
                        ->select('id, pow(longitude-:lng,2)+pow(latitude-:lat,2) as distance')
                        ->from('city')
                        ->where('longitude != 0')
                        ->order('distance asc')
                        ->limit(1)
                        ->bindValues(array(
                            ':lng' => $lng,
                            ':lat' => $lat,
                        ))
                        ->queryScalar();
    }

}

?>
