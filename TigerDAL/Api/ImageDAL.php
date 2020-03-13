<?php

namespace TigerDAL\Api;

use TigerDAL\BaseDAL;

class ImageDAL {

    /** 获取用户信息列表 */
    public static function getImages($_ids) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("image") . " where `delete`=0 and id in (" . $_ids . ")  ;";
        $_res = $base->getFetchAll($sql);
        //\mod\common::pr($sql);
        if (empty($_res)) {
            return 'emptydata';
        }
        foreach ($_res as $k => $v) {
            $res[$v['id']] = $v;
        }
        return $res;
    }

}
