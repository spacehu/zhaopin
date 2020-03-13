<?php

namespace TigerDAL\Cms;

use TigerDAL\BaseDAL;

class ExaminationTestDAL {

    /** 获取用户信息列表 */
    public static function getAll($examination_id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("examination_test") . " where `delete`=0 and examination_id=" . $examination_id . " order by edit_time desc  ;";
        return $base->getFetchAll($sql);
    }

    /** 获取用户信息 */
    public static function getOne($id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("examination_test") . " where `delete`=0 and id=" . $id . "  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 获取用户信息 */
    public static function getByName($name) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("examination_test") . " where `delete`=0 and name='" . $name . "'  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 插入 */
    public function insertExamination($data) {
        $base = new BaseDAL();
        self::insert($data);
        return $base->last_insert_id();
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
            $sql = "insert into " . $base->table_name('examination_test') . " values (null," . $set . ");";
            //echo $sql;//die;
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
            $sql = "update " . $base->table_name('examination_test') . " set " . $set . "  where id=" . $id . " ;";
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 删除用户信息 */
    public static function delete($id) {
        $base = new BaseDAL();
        $sql = "update " . $base->table_name('examination_test') . " set `delete`=1  where id=" . $id . " ;";
        return $base->query($sql);
    }

    /** 保存最新值 其他直接删除 */
    public static function save($_data, $aid, $_sourseData,$_removeData) {
        $base = new BaseDAL();
        if (!empty($_removeData)) {
            $sql = "delete from " . $base->table_name('examination_test') . " where examination_id=".$aid." and `test_id` in (" . $_removeData . ");";
            //echo $sql;
            $base->query($sql);
        }
        if (!empty($_data)) {
            $arr=explode(",",$_data);
            foreach ($arr as $v) {
                if ($v != 0) {
                    $os = $_sourseData;
                    array_unshift($os, $aid, $v);
                    print_r($os);
                    self::insert($os);
                }
            }
        }
        //die;
        return true;
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

}
