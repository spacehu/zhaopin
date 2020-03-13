<?php

namespace TigerDAL\Cms;

use TigerDAL\BaseDAL;

class TestDAL {

    /** 获取用户信息列表 */
    public static function getAll($currentPage, $pagesize, $keywords = '', $lesson_id = '', $category = '', $enterprise_id = '') {
        $base = new BaseDAL();
        $limit_start = ($currentPage - 1) * $pagesize;
        $limit_end = $pagesize;
        $where = "";
        if (!empty($keywords)) {
            $where .= " and t.name like '%" . $keywords . "%' ";
        }
        if (is_int($lesson_id)) {
            $where .= " and t.lesson_id = " . $lesson_id . " ";
        }
        if (!empty($category)&&empty($lesson_id)) {
            $where .= " and t.cat_id = '" . $category . "' ";
        }
        if (!empty($enterprise_id)) {
            $where .= " and t.enterprise_id = '" . $enterprise_id . "' ";
        }
        $sql = "select t.*,e.name as eName from " . $base->table_name("test") . " as t "
                . "left join " . $base->table_name("enterprise") . " as e on t.enterprise_id=e.id "
                . "where t.`delete`=0 " . $where . " "
                . "order by t.edit_time desc limit " . $limit_start . "," . $limit_end . " ;";
                //echo $sql;
        return $base->getFetchAll($sql);
    }

    /** 获取数量 */
    public static function getTotal($keywords = '', $lesson_id = '', $category = '', $enterprise_id = '') {
        $base = new BaseDAL();
        $where = "";
        if (!empty($keywords)) {
            $where .= " and t.name like '%" . $keywords . "%' ";
        }
        if (is_int($lesson_id)) {
            $where .= " and t.lesson_id = " . $lesson_id . " ";
        }
        if (!empty($category)&&empty($lesson_id)) {
            $where .= " and t.cat_id = '" . $category . "' and t.lesson_id=0 ";
        }
        if (!empty($enterprise_id)) {
            $where .= " and t.enterprise_id = '" . $enterprise_id . "' ";
        }
        $sql = "select count(1) as total from " . $base->table_name("test") . " as t "
                . "left join " . $base->table_name("enterprise") . " as e on t.enterprise_id=e.id "
                . "where t.`delete`=0 " . $where . " "
                . "limit 1 ;";
        return $base->getFetchRow($sql)['total'];
    }

    /** 获取用户信息 */
    public static function getOne($id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("test") . " where `delete`=0 and id=" . $id . "  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 获取用户信息 */
    public static function getByName($name) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("test") . " where `delete`=0 and name='" . $name . "'  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 新增用户信息 */
    public static function insert($data) {
        $base = new BaseDAL();
        if (is_array($data)) {
            foreach ($data as $v) {
                if (is_numeric($v)) {
                    $_data[] = " " . $v . " ";
                } else if (empty($v)) {
                    $_data[] = " null ";
                } else {
                    $_data[] = " '" . $v . "' ";
                }
            }
            $set = implode(',', $_data);
            $sql = "insert into " . $base->table_name('test') . " values (null," . $set . ");";
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
                } else if (empty($v)) {
                    $_data[] = " `" . $k . "`= null ";
                } else {
                    $_data[] = " `" . $k . "`='" . $v . "' ";
                }
            }
            $set = implode(',', $_data);
            $sql = "update " . $base->table_name('test') . " set " . $set . "  where id=" . $id . " ;";
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 删除用户信息 */
    public static function delete($id) {
        $base = new BaseDAL();
        $sql = "update " . $base->table_name('test') . " set `delete`=1  where id=" . $id . " ;";
        return $base->query($sql);
    }

    /** 获取试卷使用的试题 */
    public static function getExaminationTestList($enterprise_id,$cat_id=null) {
        $base = new BaseDAL();
        $where = "";
        if (!empty($enterprise_id)) {
            $where .= " and enterprise_id = " . $enterprise_id . " ";
        } else {
            $where .= " and (enterprise_id = 0 or enterprise_id is null) ";
        }
        if (!empty($cat_id)) {
            $where .= " and cat_id = " . $cat_id . " ";
        } 
        $sql = "select * from " . $base->table_name("test") . " where `delete`=0 and lesson_id=0 " . $where . " order by edit_time desc ;";
        return $base->getFetchAll($sql);
    }
    
    /** 获取问卷使用的试题 */
    public static function getQuestionnaireTestList($enterprise_id,$cat_id=null) {
        $base = new BaseDAL();
        $where = "";
        if (!empty($enterprise_id)) {
            $where .= " and enterprise_id = " . $enterprise_id . " ";
        } else {
            $where .= " and (enterprise_id = 0 or enterprise_id is null) ";
        }
        if (!empty($cat_id)) {
            $where .= " and cat_id = " . $cat_id . " ";
        } 
        $sql = "select * from " . $base->table_name("test") . " where `delete`=0 and lesson_id=0 " . $where . " order by edit_time desc ;";
        return $base->getFetchAll($sql);
    }

}
