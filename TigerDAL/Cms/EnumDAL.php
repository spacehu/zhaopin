<?php

namespace TigerDAL\Cms;

use TigerDAL\BaseDAL;

class EnumDAL {

    /** 获取用户信息列表 */
    public static function getAll($currentPage, $pagesize, $keywords = '', $enterprise_id = '') {
        $base = new BaseDAL();
        $limit_start = ($currentPage - 1) * $pagesize;
        $limit_end = $pagesize;
        $where = "";
        if (!empty($keywords)) {
            $where .= " and e.name like '%" . $keywords . "%' ";
        }
        if ($enterprise_id !== '') {
            $where .= " and e.enterprise_id = '" . $enterprise_id . "' ";
        }
        $sql = "select e.* from " . $base->table_name("enum") . " as e "
                . "where e.`delete`=0 " . $where . " "
                . "order by e.edit_time desc limit " . $limit_start . "," . $limit_end . " ;";
        return $base->getFetchAll($sql);
    }

    /** 获取数量 */
    public static function getTotal($keywords = '', $enterprise_id = '') {
        $base = new BaseDAL();
        $where = "";
        if (!empty($keywords)) {
            $where .= " and e.name like '%" . $keywords . "%' ";
        }
        if ($enterprise_id !== '') {
            $where .= " and e.enterprise_id = '" . $enterprise_id . "' ";
        }
        $sql = "select count(1) as total from " . $base->table_name("enum") . " as e "
                . " where e.`delete`=0 " . $where . " ;";
        return $base->getFetchRow($sql)['total'];
    }

    /** 获取用户信息 */
    public static function getOne($id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("enum") . " where `delete`=0 and id=" . $id . "  limit 1 ;";
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
                } else if (empty($v)) {
                    $_data[] = " null ";
                } else {
                    $_data[] = " '" . $v . "' ";
                }
            }
            $set = implode(',', $_data);
            $sql = "insert into " . $base->table_name('enum') . " values (null," . $set . ");";
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
                } else if (empty($v)) {
                    $_data[] = " `" . $k . "`= null ";
                } else {
                    $_data[] = " `" . $k . "`='" . $v . "' ";
                }
            }
            $set = implode(',', $_data);
            $sql = "update " . $base->table_name('enum') . " set " . $set . "  where id=" . $id . " ;";
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 删除用户信息 */
    public static function delete($id) {
        $base = new BaseDAL();
        $sql = "update " . $base->table_name('enum') . " set `delete`=1  where id=" . $id . " ;";
        return $base->query($sql);
    }

    /** 获取格式化后的字典数据 */
    public static function getAllDecode($keywords=[]){
        $res=false;
        if(!empty($keywords)&&is_array($keywords)){
            foreach($keywords as $k=>$v){
                $row=self::getAll(1,1,$v)[0];
                $row['data']=explode(',',$row['value']);
                $res[$row['id']]=$row;
            }
        }
        return $res;
    }
}
