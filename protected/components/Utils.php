<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Uitls
 *
 * @author yemin
 */
class Utils {

    const IPLOOKUP_URL = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json';
    const GEOCODER_URL = 'http://api.map.baidu.com/geocoder?city=&output=json&key=133c3ab1c745a0ee41f0c901ff77d4b7';

    //put your code here

    /**
     * 返回客户端的IP地址
     * 
     * @return string 客户端的IP地址
     */
    public static function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (empty($ip) || $ip == '127.0.0.1')  return null;
        return $ip;
    }
    
    /**
     * 获取一个ip所在地理位置
     * 
     * @param string $ip
     * @return array
     */
    public static function getIpLocation($ip) {
        if (empty($ip)) return null;
        
        $redis = RedisClient::getClient();
        $key = "ipaddr_$ip";
        $addr = $redis->get($key);
        if ($addr === false) {
            $addr = json_decode(file_get_contents(self::IPLOOKUP_URL . '&ip=' . urlencode($ip)));
            $redis->setex("ipaddr_$ip", 86400 * 15, $addr); // 15 days
        }

        if (isset($addr->ret) && $addr->ret == 1) {
            return array(
                'province' => !empty($addr->province) ? $addr->province : null,
                'city' => !empty($addr->city) ? $addr->city : null,
                'district' => !empty($addr->district) ? $addr->district : null,
            );
        }
    }

    /**
     * 获取访问者的地理位置
     * 
     * @return array
     */
    public static function getClientLocation() {
        $ip = self::getClientIp();
        return self::getIpLocation($ip);
    }

    public static function locationToString($location) {
        if (!is_array($location)) return '';
        $str = '';
        if (!empty($location['province']))
            $str .= $location['province'];
        if (!empty($location['city']))
            $str .= ' ' . $location['city'];
        if (!empty($location['district']))
            $str .= ' ' . $location['district'];
        return $str;
    }

    /**
     * 根据字符串字符串返回经度、纬度信息
     * 
     * @param string $location
     * @return array array('lng'=>120.111, 'lat'=>24.444,  ...)
     * 
     */
    public static function geoCoderLocation($location) {
        $redis = RedisClient::getClient();
        $key = "geocoder_$location";
        $ret = $redis->get($key);
        if ($ret === false) {
            $url = self::GEOCODER_URL . '&address=' . urlencode($location);
            $ret = json_decode(file_get_contents($url));
            if (isset($ret->status) && $ret->status == 'OK' && !empty($ret->result)) {
                $result = array(
                    'lng' => $ret->result->location->lng,
                    'lat' => $ret->result->location->lat,
                    'precise' => $ret->result->precise,
                    'confidence' => $ret->result->confidence,
                    'level' => $ret->result->level,
                );
                $redis->setex($key, 86400 * 30, $result);
                return $result;
            }

            return null;
        }
        return $ret;
    }

    /**
     * return string by giving array
     * 
     * @param array $arr
     * @return string
     */
    public static function arrToString($arr) {
        $search = array("\n", "\t", '    ');
        return str_replace($search, ' ', print_r($arr, true));
    }

    /**
     * 截取固定长度的字符
     * 
     * @param mixed $subject 需要处理的数据
     * @param int $limit 限长
     * @param string $delimiter = ' ' 分割符
     * @return string
     */
    public static function amputate($subject, $limit = 30, $delimiter = ' ') {
        if (is_string($subject)) {
            $arr = explode($delimiter, $subject);
        } elseif (is_array($subject)) {
            $arr = $subject;
        } else {
            $arr = array();
        }
        if (empty($arr)) {
            return '';
        }
        $postfix = '';
        $con = array(); // current content
        $len = 0;       // current length
        foreach ($arr as $one) {
            $t_len = strlen($one);
            if ($len + 1 + $t_len > $limit) {
                $postfix = '...';
                break;
            } else {
                $con[] = $one;
                $len += 1 + $t_len;
            }
        }
        if (empty($con)) {
            $con[] = substr_compare($arr[0], 0, $limit);
        }
        return implode($delimiter, $con) . $postfix;
    }

    public static function tripDescStriper($str, $length = 300) {
        $str = strip_tags($str, '<img>');
        $str = str_replace(array("\n", "\t", "\r"), '', $str);
        $ret = mb_strcut($str, 0, $length, 'utf-8');
        if(strlen($ret) != strlen($str)){
            $ret .= '...';
        }
        return $ret;
    }

}

?>
