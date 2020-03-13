<?php

namespace TigerDAL\Cms;

use TigerDAL\BaseDAL;

class UserDAL {

    /** 获取用户信息列表 */
    public static function getAll($currentPage, $pagesize, $keywords) {
        $base = new BaseDAL();
        $limit_start = ($currentPage - 1) * $pagesize;
        $limit_end = $pagesize;
        $where = "";
        if (!empty($keywords)) {
            $where .= " and u.name like '%" . $keywords . "%' ";
        }
        if (\mod\common::getSession("id") != 1) {
            $where .= " and u.id <> 1 ";
        }
        $sql = "select u.*,r.name as rname,e.name as ename from " . $base->table_name("user") . " as u "
                . "left join " . $base->table_name('role') . " as r on u.role_id=r.id "
                . "left join " . $base->table_name('enterprise') . " as e on u.enterprise_id=e.id  and e.`delete`=0 "
                . "where u.`delete`=0 " . $where . " "
                . "order by u.edit_time desc "
                . "limit " . $limit_start . "," . $limit_end . " ;";
        return $base->getFetchAll($sql);
    }

    /** 获取数量 */
    public static function getTotal($keywords) {
        $base = new BaseDAL();
        $where = "";
        if (!empty($keywords)) {
            $where .= " and name like '%" . $keywords . "%' ";
        }
        if (\mod\common::getSession("id") != 1) {
            $where .= " and id <> 1 ";
        }
        $sql = "select count(1) as total from " . $base->table_name("user") . " where `delete`=0 " . $where . " limit 1 ;";
        return $base->getFetchRow($sql)['total'];
    }

    /** 获取用户信息 */
    public static function getOne($id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("user") . " where `delete`=0 and id=" . $id . "  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 获取用户信息 */
    public static function getByName($name) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("user") . " where `delete`=0 and name='" . $name . "'  limit 1 ;";
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
            $sql = "insert into " . $base->table_name('user') . " values (null," . $set . ");";
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
            $sql = "update " . $base->table_name('user') . " set " . $set . "  where id=" . $id . " ;";
            //echo $sql;die;
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 删除用户信息 */
    public static function delete($id) {
        $base = new BaseDAL();
        $sql = "update " . $base->table_name('user') . " set `delete`=1  where id=" . $id . " ;";
        return $base->query($sql);
    }

    /*     * *************************************************************************************** */

    public static function getRole($id) {
        $base = new BaseDAL();
        $sql = "select r.data from " . $base->table_name('user') . " as u , " . $base->table_name('role') . " as r where u.role_id=r.id and u.id=" . $id . " ;";
        return $base->getFetchRow($sql);
    }


    
    /** 获取用户信息 关联微信表 */
    public static function getUser($id) {
        $res=self::getOne($id);
        if(!empty($res)){
            unset($res['password']);
            $base = new BaseDAL();
            $sql = "select * from " . $base->table_name("user_wechat") . " where user_id=" . $id . "  limit 1 ;";
            $res['wechat']=$base->getFetchRow($sql);
        }
        return $res;
    }

}
