<?php

namespace TigerDAL\Cms;

use TigerDAL\BaseDAL;

class ArticleDAL {

    /** 获取用户信息列表 */
    public static function getAll($currentPage, $pagesize, $keywords, $category = '',$enterprise_id='') {
        $base = new BaseDAL();
        $limit_start = ($currentPage - 1) * $pagesize;
        $limit_end = $pagesize;
        $where = "";
        if (!empty($keywords)) {
            $where .= " and a.name like '%" . $keywords . "%' ";
        }
        if (!empty($category)) {
            $where .= " and a.cat_id = '" . $category . "' ";
        }
        if(!empty($enterprise_id)){
            $where .= " and a.enterprise_id = '" . $enterprise_id . "' and e.`delete`=0 ";
        }
        $sql = "select a.*,count(ura.id) as resumeCount,e.`name` as eName from " . $base->table_name("article") . " as a "
                . "left join " . $base->table_name("user_resume_article") . " as ura on a.id=ura.article_id and ura.`delete`=0 and ura.user_resume_id>0 "
                . "left join ".$base->table_name("enterprise")." as e on e.id=a.enterprise_id "
                . "where a.`delete`=0 " . $where . " "
                . "group by a.id "
                . "order by a.edit_time desc limit " . $limit_start . "," . $limit_end . " ;";
        //echo $sql;
        return $base->getFetchAll($sql);
    }

    /** 获取数量 */
    public static function getTotal($keywords, $category = '',$enterprise_id='') {
        $base = new BaseDAL();
        $where = "";
        if (!empty($keywords)) {
            $where .= " and name like '%" . $keywords . "%' ";
        }
        if (!empty($category)) {
            $where .= " and cat_id = '" . $category . "' ";
        }
        if(!empty($enterprise_id)){
            $where .= " and enterprise_id = '" . $enterprise_id . "' ";
        }
        $sql = "select count(1) as total from " . $base->table_name("article") . " where `delete`=0 " . $where . " limit 1 ;";
        return $base->getFetchRow($sql)['total'];
    }

    /** 获取用户信息 */
    public static function getOne($id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("article") . " where `delete`=0 and id=" . $id . "  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 获取用户信息 */
    public static function getByName($name, $type) {
        $base = new BaseDAL();
        if (!empty($type)) {
            $where .= " and type = '" . $type . "' ";
        }
        $sql = "select * from " . $base->table_name("article") . " where `delete`=0 and name='" . $name . "' " . $where . "  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 新增用户信息 */
    public static function insert($data) {
        try {
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
                $sql = "insert into " . $base->table_name('article') . " values (null," . $set . ");";
                //echo $sql;die;
                $res = $base->query($sql);
                $id = $base->last_insert_id();
                if ($res) {
                    return $id;
                }
            } else {
                return true;
            }
        } catch (Exception $ex) {
            \TigerDAL\CatchDAL::markError(\config\code::$code[\config\code::WORKS_UPDATE], \config\code::WORKS_UPDATE, json_encode($ex));
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
            $sql = "update " . $base->table_name('article') . " set " . $set . "  where id=" . $id . " ;";
            //echo $sql;die;
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 删除用户信息 */
    public static function delete($id) {
        $base = new BaseDAL();
        $sql = "update " . $base->table_name('article') . " set `delete`=1  where id=" . $id . " ;";
        return $base->query($sql);
    }

}
