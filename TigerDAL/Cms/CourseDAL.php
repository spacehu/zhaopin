<?php

namespace TigerDAL\Cms;

use TigerDAL\BaseDAL;

class CourseDAL {

    /** 获取用户信息列表 */
    public static function getAll($currentPage, $pagesize, $keywords = '', $cat_id = '', $enterprise_id = '') {
        $base = new BaseDAL();
        $limit_start = ($currentPage - 1) * $pagesize;
        $limit_end = $pagesize;
        $where = "";
        $join = "";
        $fields = "";
        if (!empty($keywords)) {
            $where .= " and c.name like '%" . $keywords . "%' ";
        }
        if ($cat_id !== '') {
            $where .= " and c.category_id = '" . $cat_id . "' ";
        }
        if ($enterprise_id !== '') {
            $where .= " and ec.enterprise_id = '" . $enterprise_id . "' ";
        }
        $fields .= ",e.name as eName ";
        $join .= " left join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id "
                . "left join " . $base->table_name("enterprise") . " as e on ec.enterprise_id=e.id ";
        $sql = "select c.*" . $fields . " from " . $base->table_name("course") . " as c "
                . $join
                . "where c.`delete`=0 " . $where . " "
                . "group by c.id "
                . "order by c.edit_time desc limit " . $limit_start . "," . $limit_end . " ;";
        return $base->getFetchAll($sql);
    }

    /** 获取数量 */
    public static function getTotal($keywords = '', $cat_id = '', $enterprise_id = '') {
        $base = new BaseDAL();
        $where = "";
        $join = '';
        if (!empty($keywords)) {
            $where .= " and c.name like '%" . $keywords . "%' ";
        }
        if ($cat_id !== '') {
            $where .= " and c.category_id = '" . $cat_id . "' ";
        }
        if ($enterprise_id !== '') {
            $where .= " and ec.enterprise_id = '" . $enterprise_id . "' ";
        }
        $join .= " left join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id "
                . "left join " . $base->table_name("enterprise") . " as e on ec.enterprise_id=e.id ";
        $sql = "select count(1) as total from ("
                . "select count(1) as t,c.id from " . $base->table_name("course") . " as c "
                . $join
                . "where c.`delete`=0 " . $where . " "
                . "group by c.id "
                . ") as o;";
        //echo $sql;
        return $base->getFetchRow($sql)['total'];
    }

    /** 获取用户信息 */
    public static function getOne($id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("course") . " where `delete`=0 and id=" . $id . "  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 获取用户信息 */
    public static function getByName($name, $id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("course") . " where `delete`=0 and name='" . $name . "' and id <> '" . $id . "'  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 新增用户返回id */
    public static function insertById($data) {
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
            $sql = "insert into " . $base->table_name('course') . " values (null," . $set . ");";
            //\mod\common::pr($sql);die;
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
            $sql = "update " . $base->table_name('course') . " set " . $set . "  where id=" . $id . " ;";
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 删除用户信息 */
    public static function delete($id) {
        $base = new BaseDAL();
        $sql = "update " . $base->table_name('course') . " set `delete`=1  where id=" . $id . " ;";
        return $base->query($sql);
    }

    /** 根据分类ids获取ids下的课程数量 */
    public static function getByCatId($cat_id, $enterprise_id) {
        $base = new BaseDAL();
        $where = "";
        if (!empty($enterprise_id)) {
            $where .= " and ec.enterprise_id=" . $enterprise_id . " ";
        }
        $sql = "select count(o.category_id) as num,o.category_id from ( "
                . "select c.category_id,c.id "
                . " from " . $base->table_name("course") . " as c "
                . " left join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id and ec.`delete`=0 "
                . " where c.category_id in (" . $cat_id . ") and c.`delete`=0 "
                . $where
                . " GROUP by c.id "
                . ") as o "
                . "group by o.category_id ;";
        //echo $sql;die;
        return $base->getFetchAll($sql);
    }

}
