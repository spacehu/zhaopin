<?php

namespace TigerDAL\cli;

use TigerDAL\BaseDAL;

class UserDAL {

    /** 获取用户信息列表 */
    public static function getAll() {
        $base = new BaseDAL("cli");
        $sql = "select * from " . $base->table_name("user") . "  "
                . "where`delete`=0  and is_email=1 and start_date<='" . date("Y-m-d H:i:s") . "' "
                . "and ((times=0)or(times=1 and ROUND(datediff('" . date("Y-m-d H:i:s") . "', start_date)/7)=0)or(times=2 and datediff('" . date("Y-m-d H:i:s") . "', just_date)=0)) "
                . "order by edit_time desc "
                . " ;";
        return $base->getFetchAll($sql);
    }

    /** 获取数量 */
    public static function getTotal() {
        $base = new BaseDAL("cli");
        $sql = "select count(1) as total from " . $base->table_name("user") . " where `delete`=0  limit 1 ;";
        return $base->getFetchRow($sql)['total'];
    }

    /** 获取发件信息 */
    public static function getConfig() {
        $base = new BaseDAL("cli");
        $sql = "select * from " . $base->table_name("system") . "  where type='1' order by order_by asc ;";
        $res = $base->getFetchAll($sql);
        $fromInfo = array();
        if (!empty($res)) {
            foreach ($res as $k => $v) {
                if (strpos($v['name'], '_arr') == false) {
                    $fromInfo[$v['name']] = $v['value'];
                } else {
                    $arr = explode(';', $v['value']);
                    $_arr = '';
                    foreach ($arr as $ka => $va) {
                        $_sarr = explode(':', $va);
                        $_arr[$_sarr[0]] = $_sarr[1];
                    }
                    $fromInfo[$v['name']] = $_arr;
                }
            }
        }
        return $fromInfo;
    }
    // 获取学习报告的接口 暂时不用
    public static function getData() {
        $base = new BaseDAL("cli");
    }

}
