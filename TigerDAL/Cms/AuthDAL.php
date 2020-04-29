<?php

namespace TigerDAL\Cms;

use TigerDAL\BaseDAL;

class AuthDAL {

    /** 获取用户信息
     * @param $name
     * @return array|bool|null
     */
    public static function getByName($name) {
        $base = new BaseDAL();
        $sql = "select u.*,count(*) as num,r.level "
                . "from " . $base->table_name('user') . " as u "
                . "left join " . $base->table_name('role') . " as r on u.role_id=r.id "
                . "where u.name='" . $name . "'  and u.`delete`=0 ;";
        return $base->getFetchRow($sql);
    }

}
