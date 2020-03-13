<?php

namespace TigerDAL\Cms;

use mod\common as Common;
use TigerDAL\BaseDAL;

class EnterpriseCourseDAL {

    /** 获取用户信息列表 */
    public static function getAll($course_id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("enterprise_course") . " where `delete`=0 and course_id=" . $course_id . " order by edit_time desc ;";
        return $base->getFetchAll($sql);
    }

    /** 保存最新值 其他直接删除 */
    public static function save($_data, $aid, $_sourseData) {
        if (empty($_data)) {
            return true;
        }
        $base = new BaseDAL();
        $sql = "delete from " . $base->table_name('enterprise_course') . " where `course_id`='" . $aid . "';";
        $base->query($sql);

        foreach ($_data as $v) {
            if ($v != 0) {
                $os = $_sourseData;
                array_unshift($os, $v, $aid);
                //print_r($os);
                self::insert($os);
            }
        }
        return true;
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
            $sql = "insert into " . $base->table_name('enterprise_course') . " values (null," . $set . ");";
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
            $sql = "update " . $base->table_name('enterprise_course') . " set " . $set . "  where id=" . $id . " ;";
            //echo $sql;die;
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 删除用户信息 */
    public static function delete($id) {
        $base = new BaseDAL();
        $sql = "update " . $base->table_name('enterprise_course') . " set `delete`=1  where id=" . $id . " ;";
        return $base->query($sql);
    }

    /** 获取企业课程 */
    public static function getEnterpriseCourse($enterprise_id, $department_id = '', $position_id = '') {
        $base = new BaseDAL();
        $where = "";
        if (isset($department_id) && is_numeric($department_id)) {
            //$where = " and (ec.department_id = " . $department_id . " or ec.department_id = 0  or ec.department_id is null ) ";

            if (isset($position_id) && is_numeric($position_id)) {
//                $where = " and (ec.department_id = " . $department_id . " )  "
//                        . " and (ec.position_id = " . $position_id . " or ec.position_id =0 or ec.position_id is null) ";
                $where = " and (ec.department_id = " . $department_id . " )  ";
            }
        }
        $sql = "select c.*,ec.department_id,group_concat(distinct ec.department_id) as d_ids,ec.position_id,group_concat(distinct ec.position_id) as p_ids "
                . "from " . $base->table_name("course") . " as c "
                . "right join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id "
                . " where  ec.enterprise_id=" . $enterprise_id . " and c.`delete`=0 and ec.`delete`=0 " . $where . " "
                . "group by c.id "
                . "order by c.edit_time desc ;";
        //echo $sql;
        return $base->getFetchAll($sql);
    }

    /** 更新department */
    public static function updateDepartmentId($enterprise_id, $_courseids, $fid = 0, $tid = 0) {
        $base = new BaseDAL();
        if (!empty($_courseids)) {
            $_courseidsArr = explode(",", $_courseids);
            $where = " and ec.department_id=" . $fid . " ";
            $elsewhere = " and ec.department_id <>" . $fid . " ";
            foreach ($_courseidsArr as $v) {
                $sql = "select id from " . $base->table_name("enterprise_course") . " as ec where ec.`delete`=0 and ec.enterprise_id= " . $enterprise_id . " and ec.course_id=" . $v . " " . $where . " ;";
                //echo $sql;
                $rows = $base->getFetchAll($sql);
                if (!empty($rows)) {
                    foreach ($rows as $key => $val) {
                        if ($key == 0) {
                            $sql = "select id from " . $base->table_name("enterprise_course") . " as ec where ec.`delete`=0 and ec.enterprise_id= " . $enterprise_id . " and ec.course_id=" . $v . " " . $elsewhere . " ;";
                            //echo $sql;
                            $exipt = $base->getFetchAll($sql);
                            if (empty($exipt)) {
                                //如果是唯一的数据 执行保留操作
                                $_data = [
                                    'department_id' => $tid,
                                    'position_id' => 0,
                                ];
                                self::update($val['id'], $_data);
                                continue;
                            }
                        }
                        //执行删除操作
                        $_data = [
                            'delete' => 1,
                        ];
                        self::update($val['id'], $_data);
                    }
                } else {
                    $_data = [
                        "enterprise_id" => $enterprise_id,
                        "course_id" => $v,
                        'add_by' => Common::getSession("id"),
                        'add_time' => date("Y-m-d H:i:s"),
                        'edit_by' => Common::getSession("id"),
                        'edit_time' => date("Y-m-d H:i:s"),
                        'delete' => 0,
                        "department_id" => $tid,
                        "position_id" => 0,
                    ];
                    self::insert($_data);
                }
            }
        }
        //die;
    }

    /** 更新position */
    public static function updatePositionId($enterprise_id, $department_id, $_courseids, $fid = 0, $tid = 0) {
        $base = new BaseDAL();
        if (!empty($_courseids)) {
            $_courseidsArr = explode(",", $_courseids);
            $where = " and ec.department_id=" . $department_id . " and ec.position_id=" . $fid . " ";
            $elsewhere = " and ec.department_id =" . $department_id . " and ec.position_id <>" . $fid . " ";
            foreach ($_courseidsArr as $v) {
                $sql = "select id from " . $base->table_name("enterprise_course") . " as ec where ec.`delete`=0 and ec.enterprise_id= " . $enterprise_id . " and ec.course_id=" . $v . " " . $where . " ;";
                //echo $sql;die;
                $rows = $base->getFetchAll($sql);
                if (!empty($rows)) {
                    foreach ($rows as $key => $val) {
                        if ($key == 0) {
                            $sql = "select id from " . $base->table_name("enterprise_course") . " as ec where ec.`delete`=0 and ec.enterprise_id= " . $enterprise_id . " and ec.course_id=" . $v . " " . $elsewhere . " ;";
                            //echo $sql;
                            $exipt = $base->getFetchAll($sql);
                            if (empty($exipt)) {
                                //如果是唯一的数据 执行保留操作
                                $_data = [
                                    'position_id' => $tid,
                                ];
                                self::update($val['id'], $_data);
                                continue;
                            }
                        }
                        $_data = [
                            'delete' => 1,
                        ];
                        self::update($val['id'], $_data);
                    }
                } else {
                    $_data = [
                        "enterprise_id" => $enterprise_id,
                        "course_id" => $v,
                        'add_by' => Common::getSession("id"),
                        'add_time' => date("Y-m-d H:i:s"),
                        'edit_by' => Common::getSession("id"),
                        'edit_time' => date("Y-m-d H:i:s"),
                        'delete' => 0,
                        "department_id" => $department_id,
                        "position_id" => $tid,
                    ];
                    self::insert($_data);
                }
            }
        }
    }

}
