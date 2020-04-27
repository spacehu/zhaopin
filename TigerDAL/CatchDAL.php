<?php

namespace TigerDAL;

/*
 * 基本数据类包
 * 类
 * 访问数据库用
 * 继承数据库包
 */

class CatchDAL {

    /** 记录异常
     * @param $name
     * @param $code
     * @param $detail
     * @return bool|\mysqli_result
     */
    public static function markError($name, $code, $detail) {
        $base = new BaseDAL();
        $_data=[
            'name'=>$name,
            'code'=>$code,
            'detail'=>$detail,
            'add_time'=>'NOW()',
        ];
        return $base->insert($_data,"error_log");
    }

}
