<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of push10000kmToWeiboCommand
 *
 * @author dingfei
 */
class push1wkmToWeiboCommand extends CConsoleCommand{
    
    //put your code here
    
    private function getCookie(){
        $cookie = 'NSC_wjq_xfjcp.dpn_w3.6_w4=ffffffff0941010a45525d5f4f58455e445a4a423660; _s_tentry=-; Apache=2587868329137.5635.1359194646424; ULV=1359194646431:1:1:1:2587868329137.5635.1359194646424:; SinaRot/u/1974650511%3Fwvr%3D5%26wvr%3D5%26lf%3Dreg=28; SinaRot/u/1974650511%3Fwvr%3D5%26=17; WBStore=9a5f4a8352d9b9c1|; ULOGIN_IMG=13592080545763; SinaRot/u/1974650511%3Fwvr%3D5%26topnav%3D1%26wvr%3D5=50; SinaRot/u/1767886450%3Fwvr%3D5%26wvr%3D5%26lf%3Dreg=87; myuid=1767886450; SINAGLOBAL=2587868329137.5635.1359194646424; SUE=es%3D60f5a2a547e1b686b9666907e15ea770%26ev%3Dv1%26es2%3D7f8ba37bb6b15c25a083f4216dc2bab1%26rs0%3DudyqQTd9ksq0ISOwl4fYrZWgZU94ipkMVznR9RA2Kn63EuXkTnRZrXp3O4aObFunuj5Wa1zdPuIPVSL0kcqLvkv69fpDHGXy9pVpH0dRJ%252F0EoB5TzlGvSYPXFdC9sO0lf6Zx%252BWqUuueFaX08qaMzsEFmMMzJ5BlPLEhu1z6%252Bpa4%253D%26rv%3D0; SUP=cv%3D1%26bt%3D1359215312%26et%3D1359301712%26d%3Dc909%26i%3Dc718%26us%3D1%26vf%3D0%26vt%3D0%26ac%3D27%26uid%3D1767886450%26user%3Dgypsai%2540foxmail.com%26ag%3D4%26name%3Dgypsai%2540foxmail.com%26nick%3Dgypsai%26fmp%3D%26lcp%3D; SUS=SID-1767886450-1359215313-XD-7e3nt-0ff50dfba692eca09bb91116c6ac23c5; ALF=1359820112; SSOLoginState=1359215312; v=5; un=gypsai@foxmail.com; UOR=,weibo.com,#weibo.com';
        return $cookie;
    }
    
    private function getUserAgent() {
        return 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11';
    }
    
    private function getLogSeparator() {
        return "\n----------------------------\n";
    }
    
    
    /**
     * fans为fan的list，wid作为key，fan作为value
     * fan的数据结构为 array('wid' => int('微博id'), 'wname' => string('微博名'));
     */
    private $good_fans = array();   //// 推送了微博的fans
    private $scan_fans = array();   //// 扫描过的fans
    
    /**
     * log
     */
    private function l($msg){
        $file = '/tmp/'.__CLASS__.'.'.date('Y-m-d');
        $msg = date('Y-m-d H:i:s').' '.$msg."\n";
        echo $msg;
        file_put_contents($file, $msg, FILE_APPEND);
    }
    
    public function run($args) {
        $this->l("Let's start ...");
        $this->go();
        $this->l("Done!");
        $this->l($this->getLogSeparator());
        $this->summary();
    }
    
    public function go(){
        $root = $this->getRoot();
        $fans = $this->getFans($root);
        $this->printFans($fans);
        $i = 0;
        while ($fan = array_shift($fans)) {
            //print_r($fans);exit;
            echo '当前处理的fan为.'.$fan['wname']."\n";
            $this->scan_fans[$fan['wid']] = $fan;
            $mid = $this->getWeibo($fan);
            if ($mid) {
                $this->pushWeibo($fan, $mid) && $this->good_fans[$fan['wid']] = $fan;
                $new_fans = $this->getFans($fan);
                $fans = $this->merge_fans( $new_fans, $fans);
            } else {
                $new_fans = $this->getFans($fan, 5);
                $fans = $this->merge_fans($fans, $new_fans );
            }
            
            $this->printFans($fans);
            if ($i ++ > 300){
                break;
            }
        }
    }
    
    private function printFans($fans) {
        echo 'fans_list'.count($fans).':';return TRUE;  // debug
        
        foreach($fans as $fan) {
            echo $fan['wname'].'('.$fan['wid'].') => ';
        }
        echo "end\n";
    }
    
    /**
     * 合并fans数组
     * 将$fans附加到@fans1的尾部然后返回
     * @return array $fans
     */
    public function merge_fans($fans1, $fans2) {
        $fans = array();
        foreach($fans1 as $fan) {
            $wid = $fan['wid'];
            if (!isset($this->scan_fans[$wid]))
                $fans[] = $fan;
        }
        foreach($fans2 as $fan) {
            $wid = $fan['wid'];
            if (!isset($this->scan_fans[$wid]) && !in_array($fan, $fans))
                    $fans[] = $fan;
        }
        return $fans;
        
    }
    
    /**
     * 获取微博某用户的有关关键字的一条微博
     * @param array $fan
     * @param string $kw
     * @return int 某用户的微博id
     */
    private function getWeibo($fan, $kw = "沙发客"){
        $wid   = $fan['wid'];
        $wname = $fan['wname'];
        //// 如果是重定向过来的
        $url = 'http://weibo.com/u/'.$wid.'?from=profile&wvr=5&loc=tagweibo';
        
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_HEADER, TRUE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ch, CURLOPT_COOKIE, $this->getCookie());
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        
        $response = curl_exec($ch); 
        $error = curl_error($ch);
        if ($error) { 
            $this->l("$wname($wid)".__FUNCTION__."错误,error=[{$error}]");
            curl_close($ch);
            return false;
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code != 200) {
            $this->l("$wname($wid)".__FUNCTION__."错误，error=[http_code={$http_code}");
            curl_close($ch);
            return false;
        }
        $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $body = substr($response, $header_size);
        //echo $url;echo "\n";
        //echo $body;echo "\n";
        preg_match('/feed_list_item[^m]+mid=\\\"(\d+)\\\".*?WB_text.*?('.$kw.')/si', $body, $match);
        //print_r($match);exit;
        if (isset($match[1])) {
            return $match[1];
        }
        return false;
    }
    
    /**
     * 获取微博某用户的粉丝
     * @param type $wid
     * @parma int  $size = 20
     * @return array 同fans结构
     */
    private function getFans($fan, $size = 20){
        $wid = $fan['wid'];
        $url = 'http://weibo.com/'.$wid.'/fans?from=profile&wvr=5&loc=tagfans';
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_HEADER, TRUE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ch, CURLOPT_COOKIE, $this->getCookie());
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        $response = curl_exec($ch); 
        $error = curl_error($ch);
        if ($error) { 
            $this->l("$wname($wid)".__FUNCTION__."错误,error=[{$error}]");
            curl_close($ch);
            return array();
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        if ($http_code != 200) {
            $this->l("$wname($wid)".__FUNCTION__."错误，error=[http_code={$http_code}");
            curl_close($ch);
            return array();
        }
        $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $body = substr($response, $header_size);
        $reg = '/uid=(\d+)&fnick=([^&]+)&sex=/';
        preg_match_all($reg, $body, $matches);
        $ret = array();$i = 0;
        foreach($matches[0] as $index => $value) {
            $wid = intval($matches[1][$index]);
            $wname = trim($matches[2][$index]);
            $ret[] = array('wid'=>$wid, 'wname'=>$wname);;
            if( $i ++ >= $size )
                break;
            
        }
        return $ret;
    }
    
    /**
     * 获取root微博的id
     * @return int
     */
    private function getRoot(){
        return array(
            'wid' => 3027830683,
            'wname' => '猪婆a芳芳'
        );
        /*
        return array(
            'wid' => 1235733075,
            'wname' => '吴佩慈'
        );*/
        return array(
            'wid' => 2571943644,  // 7998微博id
            'wname' => '7998穷游网',
            );
    }
    
    /**
     * 输出汇总信息
     * @log
     */
    private function summary() {
        $file = '/tmp/'.__CLASS__.'.summary'.date('Y-m-d');
        $str = "已推送过weibo的用户:\n";
        foreach($this->good_fans as $wid => $fan) {
            $str.= $wid.','.$fan['wname']."\n";
        }
        $str.= '----------'."\n";
        $str.= "已浏览过weibo的用户id:\n";
        foreach($this->scan_fans as $wid => $fan) {
            $str.= $wid.','.$fan['wname']."\n";
        }
        $str.= '----------'."\n";
        $str.= $this->getLogSeparator();
        file_put_contents($file, $str, FILE_APPEND);
    }
    
    /**
     * 像某个用户推送推广信息
     * @param array $fan
     * @param int $mid
     * @return boolean
     */
    private function pushWeibo($fan, $mid) {
        $this->l("已为{$fan['wname']}({$fan['wid']}))的微博({$mid})推送推广信息");
        return true;
    }
}

?>
