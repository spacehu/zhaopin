<?php

namespace TigerDAL\Api;

use TigerDAL\BaseDAL;
use TigerDAL\Cms\UserDAL;
use TigerDAL\Api\CourseDAL;
use TigerDAL\Cms\DepartmentDAL as cmsDepartmentDAL;
use TigerDAL\Cms\PositionDAL as cmsPositionDAL;

/*
 * 用来返回生成首页需要的数据
 * 类
 * 访问数据库用
 * 继承数据库包
 */

class AccountDAL {

    function __construct() {
        
    }

    /** 新建收藏 */
    public static function insertUserFavorites($data) {
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
            $sql = "insert into " . $base->table_name('user_favorites') . " values (null," . $set . ");";
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 更新收藏 */
    public static function updateUserFavorites($id, $data) {
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
            $sql = "update " . $base->table_name('user_favorites') . " set " . $set . "  where id=" . $id . " ;";
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 收藏的反复方法 */
    public static function doFavorites($user_id, $article_id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name('user_favorites') . " where user_id=" . $user_id . " and article_id=" . $article_id . " ;";
        $row = $base->getFetchRow($sql);
        if (empty($row)) {
            $_data = [
                'user_id' => $user_id,
                'article_id' => $article_id,
                'add_time' => date("Y-m-d H:i:s"),
                'edit_time' => date("Y-m-d H:i:s"),
                'delete' => 0,
            ];
            self::insertUserFavorites($_data);
        } else {
            if ($row['delete'] == 0) {
                $_data = ['delete' => '1'];
            } else {
                $_data = ['delete' => '0'];
            }
            self::updateUserFavorites($row['id'], $_data);
        }
        return true;
    }

    /** 收藏文章的状态 */
    public static function getFavorite($user_id, $article_id) {
        $base = new BaseDAL();
        $sql = "select uf.* from " . $base->table_name("user_favorites") . " as uf "
                . "where uf.user_id=" . $user_id . " and uf.article_id=" . $article_id . " ;";
        return $base->getFetchRow($sql);
    }

    /** 收藏的文章列表 */
    public static function getFavorites($currentPage, $pagesize, $user_id) {
        $base = new BaseDAL();
        $limit_start = ($currentPage - 1) * $pagesize;
        $limit_end = $pagesize;
        $sql = "select c.*,i.original_src from " . $base->table_name("user_favorites") . " as uf "
                . "left join " . $base->table_name("article") . " as c on c.id=uf.article_id "
                . "left join " . $base->table_name("image") . " as i on i.id=c.media_id "
                . "where uf.`delete`=0 and c.`delete`=0 and uf.user_id=" . $user_id . " "
                . "order by uf.id desc "
                . "limit " . $limit_start . "," . $limit_end . " ;";
        return $base->getFetchAll($sql);
    }

    /** 收藏的文章列表 total */
    public static function getFavoritesTotal($user_id) {
        $base = new BaseDAL();
        $sql = "select count(uf.id) as num from " . $base->table_name("user_favorites") . " as uf "
                . "left join " . $base->table_name("article") . " as c on c.id=uf.article_id "
                . "where uf.`delete`=0 and c.`delete`=0 and uf.user_id=" . $user_id . " ;";
        return $base->getFetchRow($sql)['num'];
    }

    /** 获取已读课程信息列表 
     * enterprise_id 需要筛选的企业
     * _enterprise_id 所在企业
     */
    public static function getCourses($currentPage, $pagesize, $user_id) {
        $base = new BaseDAL();
        $limit_start = ($currentPage - 1) * $pagesize;
        $limit_end = $pagesize;
        $where = "";
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
        $sql = "select c.*,uc.status as ucStatus,i.original_src,count(l.id) as ls,count(ul.id) as uls, "
                . " if(count(l.id)<>0,count(ul.id)/count(l.id)*100,0) as progress "
                . " from " . $base->table_name("course") . " as c "
                . " left join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id and ec.delete=0 "
                . " left join " . $base->table_name("user_course") . " as uc on c.id=uc.course_id "
                . " left join " . $base->table_name("image") . " as i on i.id=c.media_id "
                . " LEFT JOIN " . $base->table_name("lesson") . " AS l ON l.course_id = c.id AND l.`delete` = 0 "
                . " LEFT JOIN " . $base->table_name("user_lesson") . " AS ul ON ul.lesson_id = l.id and ul.user_id= uc.user_id AND ul.`delete` = 0 "
                . " where uc.`delete`=0 and c.`delete`=0 and uc.user_id=" . $user_id . " "
                . $where
                . " GROUP BY c.id "
                . " order by uc.id desc "
                . " limit " . $limit_start . "," . $limit_end . " ;";
        //echo $sql;
        return $base->getFetchAll($sql);
    }

    /** 获取已读课程信息列表 total */
    public static function getCoursesTotal($user_id) {
        $base = new BaseDAL();
        $where = "";
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
        $sql = " select count(uc.id) as num from " . $base->table_name("user_course") . " as uc "
                . " left join " . $base->table_name("course") . " as c on c.id=uc.course_id "
                . " left join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id and ec.delete=0 "
                . " where uc.`delete`=0 and c.`delete`=0 and uc.user_id=" . $user_id . " "
                . $where
                . " ;";
        // echo $sql;die;
        return $base->getFetchRow($sql)['num'];
    }

    /** 获取完成课程信息列表 total */
    public static function getCoursesPass($user_id) {
        $base = new BaseDAL();
        $where = "";
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
        $sql = "select count(uc.id) as num from " . $base->table_name("user_course") . " as uc "
                . " left join " . $base->table_name("course") . " as c on c.id=uc.course_id "
                . " left join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id and ec.delete=0 "
                . " where uc.`delete`=0 and c.`delete`=0 and uc.status=2 and uc.user_id=" . $user_id . " "
                . $where
                . " ;";
        return $base->getFetchRow($sql)['num'];
    }

    /** 获取失败课程信息列表 total */
    public static function getCoursesFailed($user_id) {
        $base = new BaseDAL();
        $where = "";
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
        $sql = "select count(uc.id) as num from " . $base->table_name("user_course") . " as uc "
                . " left join " . $base->table_name("course") . " as c on c.id=uc.course_id "
                . " left join " . $base->table_name("exam") . " as e on uc.course_id=e.course_id and uc.user_id=e.user_id "
                . " left join " . $base->table_name("enterprise_course") . " as ec on c.id=ec.course_id and ec.delete=0 "
                . " where uc.`delete`=0 and c.`delete`=0 and c.percentage>0 and c.percentage is not null and e.point<c.percentage and uc.user_id=" . $user_id . " "
                . $where
                . " ;";
        return $base->getFetchRow($sql)['num'];
    }

    /** 绑定企业员工关系 */
    public static function doEnterpriseRelation($user_id, $code) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("enterprise") . " where code='" . $code . "' ;";
        $row = $base->getFetchRow($sql);
        if (empty($row)) {
            return "errorCode";
        }
        $sql = "select * from " . $base->table_name("enterprise_user") . " where user_id=" . $user_id . " and enterprise_id=" . $row['id'] . " ;";
        $rowEU = $base->getFetchRow($sql);
        if (!empty($rowEU)) {
            if ($rowEU['delete'] == 1) {
                $data = [
                    'status' => 0,
                    'delete' => 0,
                ];
            }
            if ($rowEU['status'] == 2) {
                $data = [
                    'status' => 0,
                ];
            }
            if (!empty($data)) {
                if (self::updateEnterpriseUser($rowEU['id'], $data)) {
                    return [
                        'eName' => $row['name'],
                        'ePhone' => $row['phone'],
                        'eStatus' => (string) $data['status'],
                    ];
                }
            }
            return [
                'eName' => $row['name'],
                'ePhone' => $row['phone'],
                'eStatus' => (string) $rowEU['status'],
            ];
        }
        $data = [
            'enterprise_id' => $row['id'],
            'user_id' => $user_id,
            'status' => 0,
            'add_by' => 0,
            'add_time' => date("Y-m-d H:i:s"),
            'edit_by' => 0,
            'edit_time' => date("Y-m-d H:i:s"),
            'delete' => 0,
            'department_id'=>'0',
            'position_id'=>'0',
        ];
        if (self::insertEnterpriseUser($data)) {
            return [
                'eName' => $row['name'],
                'ePhone' => $row['phone'],
                'eStatus' => (string) $data['status'],
            ];
        }
    }

    /** 绑定企业员工关系 */
    public static function unEnterpriseRelation($user_id, $enterprise_id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("enterprise_user") . " where user_id=" . $user_id . " and enterprise_id=" . $enterprise_id . " ;";
        $rowEU = $base->getFetchRow($sql);
        if (!empty($rowEU)) {
            $data = [
                'delete' => 1,
            ];
            if (self::updateEnterpriseUser($rowEU['id'], $data)) {
                return true;
            }
        }
    }

    /** 新建企业员工关系 */
    public static function insertEnterpriseUser($data) {
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

    /** 更新企业员工关系 */
    public static function updateEnterpriseUser($id, $data) {
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

    /** 获取企业员工关系 */
    public static function getEnterpriseUser($user_id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("enterprise_user") . " where `delete`=0 and `status`=1 and user_id='" . $user_id . "' ;";
        //echo $sql;
        return $base->getFetchRow($sql);
    }

    /** 获取员工已考课程id */
    public static function getExamListByCourse($user_id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("exam") . " "
                . "where `delete`=0 "
                . "and user_id='" . $user_id . "' "
                . "and course_id<>0 "
                . "and course_id is not null ;";
        //echo $sql;
        return $base->getFetchAll($sql);
    }

    /** 获取员工已考试题id */
    public static function getExamListByExamination($user_id) {
        $base = new BaseDAL();
        $sql = "select e.*,ex.percentage,max(point) as maxPoint,min(point) as minPoint "
                . "from " . $base->table_name("exam") . " as e "
                . "left join " . $base->table_name("examination") . " as ex on e.examination_id=ex.id "
                . "where e.`delete`=0 "
                . "and e.user_id='" . $user_id . "' "
                . "and e.examination_id<>0 "
                . "and e.examination_id is not null "
                . "group by examination_id ;";
        //echo $sql;
        return $base->getFetchAll($sql);
    }

}
