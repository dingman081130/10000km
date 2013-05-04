<?php
/**
 * @file class Pagination 管理和分页相关的一些公用方法
 * @package application.models
 *
 * @author <DingFei> dingman081130@gmail.com
 * @date 2012-12-15
 * @version
 */

class Pagination{

    /**
     * 根据记录总数和每页条数计算出分页数量
     *
     * @param int $cnt 记录总数
     * @param int $per 每页条数
     * @return int
     */
    public static function getPageCnt($cnt, $per){
        if($per <= 0 or $cnt <= 0){
            return 0;
        }
        return ceil($cnt/$per);
    }
    
    /**
     * get net page
     * @param int $offset offset
     * @param int $per page size
     * @return int
     */
    public static function getNextPage($offset, $per){
        if($offset < 0 or $per <= 0){
            return 1;
        }
        $mod = $offset % $per;
        $div = intval( $offset / $per );
        
        if($mod == 0){
            $cur = $div;
        }else{
            $cur = $div + 1;
        }
        return $cur + 1;
    }
    /**
     * 根据记录偏移量和每页条数计算出当前在哪一页
     *
     * @param int $offset 记录偏移量
     * @param int $per 每页条数
     * @return int;
     */
    public static function getCurNum($offset, $per){
        if($offset < 0 or $per <= 0){
            return 1;
        }
        return ceil(($offset + 1) / $per);
    }
    
    /**
     * 通过每页数量和当前页码计算迁移量
     * @param int $cur_page = 1
     * @param int $per_page = 0
     * @return int
     */
    public static function getOffset($cur_page = 1, $per_page = 0){
        if($cur_page <= 0 || $per_page <= 0){
            return 0;
        }
        return ($cur_page - 1) * $per_page;
    }
    
    public static function getRestCnt( $cnt, $offset, $limit ){
        $rest = $cnt - $offset - $limit;
        $rest < 0 && $rest = 0;
        return $rest;
    }
}
