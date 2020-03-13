<?php

namespace TigerDAL\Api;

use TigerDAL\BaseDAL;
use TigerDAL\Cms\CourseDAL as cmsCourseDAL;
use TigerDAL\Cms\EnterpriseCourseDAL as cmsEnterpriseCourseDAL;
use TigerDAL\Cms\DepartmentDAL as cmsDepartmentDAL;
use TigerDAL\Cms\PositionDAL as cmsPositionDAL;

class CourseDAL {

    /** 获取课程信息列表 
     * 需求：获取公共课程和企业必修课 不需要去掉已经学习的课程
     */
    public static function getAll($currentPage, $pagesize, $keywords = '', $cat_id = '', $enterprise_id = '', $user_id = '') {
        $base = new BaseDAL();
        $limit_start = ($currentPage - 1) * $pagesize;
        $limit_end = $pagesize;
        $where = "";
        if (!empty($keywords)) {
            $where .= " and c.name like '%" . $keywords . "%' ";
        }
        if ($cat_id !== '') {
            $where .= " and c.category_id = '" . $cat_id . "' ";
        }
        if ($enterprise_id !== '') {
            $where .= " and ec.enterprise_id = '" . $enterprise_id . "' ";
        }
        //用户id 为零 只取出公共课程 id不为零 取出和用户有关的课程（包含隶属企业，部门，职位）
        //普通用户id enterprise_user里面没有数据
        $_sql = "select * from " . $base->table_name("enterprise_user") . " where user_id= " . $user_id . " and `delete`=0 and `status` = 1 ";
        $_ec = $base->getFetchRow($_sql);
        //企业用户id enterprise_user有数据
        if (!empty($_ec)) {
            $enterprise_id = $_ec['enterprise_id'];
            $department_id = $_ec['department_id'];
            $position_id = $_ec['position_id'];
            if(!cmsDepartmentDAL::getOne($department_id)){
                $department=" ec.department_id is null ";
            }else{
                $department=" ec.department_id =".$department_id." ";
            }
            if(!cmsPositionDAL::getOne($position_id)){
                $position=" ec.position_id is null ";
            }else{
                $position=" ec.position_id =".$position_id." ";
            }
            $where .= " and ((ec.enterprise_id = ".$enterprise_id." and ".$department." and ".$position." ) or ec.enterprise_id is null) ";
        }else{
            $where .= " and ec.enterprise_id is null ";
        }

        $sql = "select c.*,i.original_src,if(uc.status,uc.status,0) as ucStatus "
                . " from " . $base->table_name("course") . " as c "
                . " left join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id and ec.delete=0 "
                . " left join " . $base->table_name("image") . " as i on i.id=c.media_id "
                . " left join " . $base->table_name("user_course") . " as uc on uc.course_id=c.id and uc.user_id=" . $user_id . " and uc.`delete`=0 "
                . " where c.`delete`=0 " . $where . " "
                . " group by c.id "
                . " order by c.order_by asc, c.edit_time desc "
                . " limit " . $limit_start . "," . $limit_end . " ;";
        // echo $sql;die;
        return $base->getFetchAll($sql);
    }

    /** 获取数量 */
    public static function getTotal($keywords = '', $cat_id = '', $enterprise_id = '', $user_id = '') {
        $base = new BaseDAL();
        $where = "";
        if (!empty($keywords)) {
            $where .= " and c.name like '%" . $keywords . "%' ";
        }
        if ($cat_id !== '') {
            $where .= " and c.category_id = '" . $cat_id . "' ";
        }
        if ($enterprise_id !== '') {
            $where .= " and ec.enterprise_id = '" . $enterprise_id . "' ";
            $join = " left join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id ";
        }
        //用户id 为零 只取出公共课程 id不为零 取出和用户有关的课程（包含隶属企业，部门，职位）
        //普通用户id enterprise_user里面没有数据
        $_sql = "select * from " . $base->table_name("enterprise_user") . " where user_id= " . $user_id . " and `delete`=0 and `status` = 1 ";
        $_ec = $base->getFetchRow($_sql);
        //企业用户id enterprise_user有数据
        if (!empty($_ec)) {
            $enterprise_id = $_ec['enterprise_id'];
            $department_id = $_ec['department_id'];
            $position_id = $_ec['position_id'];
            if(!cmsDepartmentDAL::getOne($department_id)){
                $department=" ec.department_id is null ";
            }else{
                $department=" ec.department_id =".$department_id." ";
            }
            if(!cmsPositionDAL::getOne($position_id)){
                $position=" ec.position_id is null ";
            }else{
                $position=" ec.position_id =".$position_id." ";
            }
            $where .= " and ((ec.enterprise_id = ".$enterprise_id." and ".$department." and ".$position." ) or ec.enterprise_id is null) ";
        }else{
            $where .= " and ec.enterprise_id is null ";
        }

        $sql = "select count(distinct(c.id)) as total from " . $base->table_name("course") . " as c "
                . " left join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id and ec.delete=0 "
                . " where c.`delete`=0 " . $where . " limit 1 ;";
        return $base->getFetchRow($sql)['total'];
    }

    /** 获取企业允许用户使用的专用课程 */
    public static function getEnterpriseCourse($currentPage, $pagesize, $user_id) {
        $base = new BaseDAL();
        $limit_start = ($currentPage - 1) * $pagesize;
        $limit_end = $pagesize;
        //用户id 为零 只取出公共课程 id不为零 取出和用户有关的课程（包含隶属企业，部门，职位）
        $_in_ids = self::getCourseIdByUserId($user_id);
        $where = " and c.id in (" . $_in_ids . ") ";

        $sql = "select c.*,i.original_src,if(uc.status,uc.status,0) as ucStatus,count(l.id) as ls,count(ul.id) as uls, "
                . "if(count(l.id)<>0,count(ul.id)/count(l.id)*100,0) as progress  "
                . "from " . $base->table_name("course") . " as c "
                . "left join " . $base->table_name("image") . " as i on i.id=c.media_id "
                . "left join " . $base->table_name("user_course") . " as uc on uc.course_id=c.id and uc.user_id=" . $user_id . " and uc.`delete`=0 "
                . "LEFT JOIN " . $base->table_name("lesson") . " AS l ON l.course_id = c.id AND l.`delete` = 0 "
                . "LEFT JOIN " . $base->table_name("user_lesson") . " AS ul ON ul.lesson_id = l.id and ul.user_id= uc.user_id AND ul.`delete` = 0 "
                . "where c.`delete`=0 " . $where . " "
                . "GROUP BY c.id "
                . "order by c.order_by asc, c.edit_time desc "
                . "limit " . $limit_start . "," . $limit_end . " ;";
        //echo $sql;
        return $base->getFetchAll($sql);
    }

    /** 获取企业允许用户使用的专用课程 total */
    public static function getEnterpriseCourseTotal($user_id) {
        $base = new BaseDAL();
        //用户id 为零 只取出公共课程 id不为零 取出和用户有关的课程（包含隶属企业，部门，职位）
        $_in_ids = self::getCourseIdByUserId($user_id);
        $where = " and c.id in (" . $_in_ids . ") ";

        $sql = "select count(*) as num "
                . "from " . $base->table_name("course") . " as c "
                . "where c.`delete`=0 " . $where . " "
                . " ;";
        return $base->getFetchRow($sql)['num'];
    }

    /** 获取用户信息 */
    public static function getOne($id, $user_id) {
        $base = new BaseDAL();
        $sql = "select c.*,i.original_src,count(DISTINCT(l.id)) as lessonCount,sum(ul.status) as lessonStartCount,uc.status as ucStatus "
                . "from " . $base->table_name("course") . " as c "
                . "left join " . $base->table_name("image") . " as i on i.id=c.media_id "
                . "left join " . $base->table_name("lesson") . " as l on l.course_id=c.id and l.`delete`=0 "
                . "left join " . $base->table_name("user_lesson") . " as ul on l.id=ul.lesson_id and ul.user_id=" . $user_id . " "
                . "left join " . $base->table_name("user_course") . " as uc on c.id=uc.course_id and uc.user_id=" . $user_id . " "
                . "where c.`delete`=0 and c.id=" . $id . "  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 获取用户信息 */
    public static function getByName($name) {
        $cms = new cmsCourseDAL();
        return $cms->getByName($name);
    }

    /** 参与课程 */
    public static function joinCourse($data) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name('user_course') . " where user_id=" . $data['user_id'] . " and course_id=" . $data['course_id'] . " and `delete`=0 and `status`=1 ";
        if (!empty($base->getFetchRow($sql))) {
            return false;
        }
        return self::insertUserCourse($data);
    }

    /** 新建参与课程 */
    public static function insertUserCourse($data) {
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
            $sql = "insert into " . $base->table_name('user_course') . " values (null," . $set . ");";
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 获取参与的课程 */
    public static function getIdByUserCourse($user_id) {
        $base = new BaseDAL();
        $sql = "select course_id "
                . "from " . $base->table_name("user_course") . " "
                . "where `delete`=0 and user_id=" . $user_id . " ;";
        $res = $base->getFetchAll($sql);
        if (!empty($res)) {
            foreach ($res as $v) {
                $_res[] = $v['course_id'];
            }
            return implode(',', $_res);
        }
        return false;
    }

    /** 根据uid获取可以使用的课程id */
    public static function getCourseIdByUserId($user_id) {
        $base = new BaseDAL();
        $where ="";
        if (!empty($user_id)) {
            //普通用户id enterprise_user里面没有数据
            $_sql = "select * from " . $base->table_name("enterprise_user") . " where user_id= " . $user_id . " and `delete`=0 and `status` = 1 ";
            $_ec = $base->getFetchRow($_sql);
            //企业用户id enterprise_user有数据
            if (!empty($_ec)) {
                $enterprise_id = $_ec['enterprise_id'];
                $department_id = $_ec['department_id'];
                $position_id = $_ec['position_id'];
                if(!cmsDepartmentDAL::getOne($department_id)){
                    $department=" ec.department_id is null ";
                }else{
                    $department=" ec.department_id =".$department_id." ";
                }
                if(!cmsPositionDAL::getOne($position_id)){
                    $position=" ec.position_id is null ";
                }else{
                    $position=" ec.position_id =".$position_id." ";
                }
                //获取企业课程
                $where .=" and ec.enterprise_id = " . $enterprise_id . " and " . $department . " and " . $position . "  ";
                $sql = "select ec.course_id "
                        . " from " . $base->table_name("enterprise_course") . " as ec "
                        . " left join ".$base->table_name("course")." as c on c.id=ec.course_id "
                        . " where ec.`delete`=0 and c.delete=0 "
                        . " ".$where ." "
                        . " group by ec.course_id ;";
                $res = $base->getFetchAll($sql);
                if (!empty($res)) {
                    $_res[] = 0;
                    foreach ($res as $v) {
                        $_res[] = $v['course_id'];
                    }
                    return implode(',', $_res);
                }
            }
        }
        return 0;
    }

    /** 企业移动端 获取数量 */
    public static function getEnterpriseCoursesTotal($enterprise_id) {
        $base = new BaseDAL();
        $sql = "select count(distinct(c.id)) as total from " . $base->table_name("course") . " as c "
                . " left join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id "
                . "where c.`delete`=0 and ec.enterprise_id = '" . $enterprise_id . "' and ec.`delete`=0 limit 1 ;";
        return $base->getFetchRow($sql)['total'];
    }
}
