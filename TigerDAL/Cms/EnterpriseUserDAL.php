<?php

namespace TigerDAL\Cms;

use TigerDAL\BaseDAL;

class EnterpriseUserDAL {

    /** 获取用户信息 */
    public static function getOne($id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("enterprise_user") . " where `delete`=0 and id=" . $id . "  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 新增用户信息 */
    public static function insert($data) {
        $base = new BaseDAL();
        if (is_array($data)) {
            foreach ($data as $v) {
                if (is_numeric($v)) {
                    $_data[] = " " . $v . " ";
                } else {
                    $_data[] = " '" . $v . "' ";
                }
            }
            $set = implode(',', $_data);
            $sql = "insert into " . $base->table_name('enterprise_user') . " values (null," . $set . ");";
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 更新用户信息 */
    public static function update($id, $data) {
        $base = new BaseDAL();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (is_numeric($v)) {
                    $_data[] = " `" . $k . "`=" . $v . " ";
                } else {
                    $_data[] = " `" . $k . "`='" . $v . "' ";
                }
            }
            $set = implode(',', $_data);
            $sql = "update " . $base->table_name('enterprise_user') . " set " . $set . "  where id=" . $id . " ;";
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 删除用户信息 */
    public static function delete($id) {
        $base = new BaseDAL();
        $sql = "update " . $base->table_name('enterprise_user') . " set `delete`=1  where id=" . $id . " ;";
        return $base->query($sql);
    }

    /** 更新department */
    public static function updateDepartmentId($_userids, $id = "", $enterprise_id = 0) {
        $base = new BaseDAL();
        if (!empty($id)) {
            $set = " department_id=" . $id . " , position_id = 0 ";
        } else {
            $set = " department_id = 0 , position_id = 0 ";
        }
        $sql = "update " . $base->table_name('enterprise_user') . " set " . $set . "  where user_id in (" . $_userids . ") and enterprise_id= " . $enterprise_id . " ;";
        return $base->query($sql);
    }

    /** 更新position */
    public static function updatePositionId($_userids, $id = "", $enterprise_id = 0) {
        $base = new BaseDAL();
        if (!empty($id)) {
            $set = " position_id=" . $id . " ";
        } else {
            $set = " position_id = 0 ";
        }
        $sql = "update " . $base->table_name('enterprise_user') . " set " . $set . "  where user_id in (" . $_userids . ") and enterprise_id= " . $enterprise_id . " ;";
        return $base->query($sql);
    }

}
