<?php

namespace TigerDAL\Api;

use TigerDAL\BaseDAL;

/*
 * 用来返回生成首页需要的数据
 * 类
 * 访问数据库用
 * 继承数据库包
 */

class AuthDAL {

    function __construct() {
        
    }

    /** 检查是否可以注册 */
    public function checkPhone($phone, $code) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("sms") . "  "
                . "where `phone`='" . $phone . "' and `code`='" . $code . "' and `add_time` >= '" . date("Y-m-d H:i:s", strtotime("-15 minute")) . "' "
                . "limit 1";
        //echo $sql;
        $data = $base->getFetchAll($sql);
        if (empty($data)) {
            return "errorSms";
        }
        $sql = "select * from " . $base->table_name("user_info") . "  "
                . "where `phone`='" . $phone . "' "
                . "limit 1";
        $user = $base->getFetchAll($sql);
        if (!empty($user)) {
            return "hadUser";
        }
        return true;
    }

    /** 检查手机号码是否正在使用，并确认是否是在册cms用户 */
    public function bCheckPhone($phone, $code) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("sms") . "  "
                . "where `phone`='" . $phone . "' and `code`='" . $code . "' and `add_time` >= '" . date("Y-m-d H:i:s", strtotime("-15 minute")) . "' "
                . "limit 1";
        //echo $sql;
        $data = $base->getFetchAll($sql);
        if (empty($data)) {
            return "errorSms";
        }
        $sql = "select * from " . $base->table_name("user") . "  "
                . "where `name`='" . $phone . "' "
                . "limit 1";
        $user = $base->getFetchAll($sql);
        if (!empty($user)) {
            return "hadUser";
        }
        return true;
    }

    public function checkPassword($id, $password) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("user_info") . "  "
                . "where `id`='" . $id . "' "
                . "limit 1";
        //echo $sql;
        $data = $base->getFetchRow($sql);
        if (empty($data)) {
            return ['error' => '1', 'code' => "emptyUser"];
        }
        if ($data['password'] !== md5($password)) {
            return ['error' => '1', 'code' => "errorPassword"];
        }
        return ['error' => '0', 'code' => "", 'data' => $data];
    }

    /** 检查用户是否存在 */
    public function checkUser($phone, $password) {

        if (!is_numeric($phone)) {
            return ['error' => '1', 'code' => "errorPhone"];
        }
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("user_info") . "  "
                . "where `phone`='" . $phone . "' "
                . "limit 1";
        $user = $base->getFetchRow($sql);
        if (empty($user)) {
            return ['error' => '1', 'code' => "emptyUser"];
        }
        if ($user['password'] !== md5($password)) {
            return ['error' => '1', 'code' => "errorPassword"];
        }
        $data = [
            'last_login_time' => date("Y-m-d H:i:s", time()),
        ];
        $this->updateUserInfo($user['id'], $data);
        return ['error' => '0', 'code' => "", 'data' => $user];
    }

    /** 注册插入用户表 */
    public function insert($data) {
        $base = new BaseDAL();
        self::insertUserInfo($data);
        return $base->last_insert_id();
    }

    /** 获取用户信息 */
    public function getUserInfo($userid) {
        $base = new BaseDAL();
        $sql = "select ui.id,ui.`name`,ui.phone,ui.nickname,ui.photo,ui.brithday,ui.province,ui.city,ui.district,ui.email,ui.sex,ui.user_id,eu.enterprise_id,e.name as eName,e.phone as ePhone,CONCAT(eu.`status`,'') as eStatus "
                . "from " . $base->table_name("user_info") . " as ui "
                . "left join  " . $base->table_name("enterprise_user") . " as eu on eu.user_id=ui.id and eu.`status`=1 and eu.`delete`=0 "
                . "left join  " . $base->table_name("enterprise") . " as e on e.id=eu.enterprise_id "
                . "where ui.`id`=" . $userid . " limit 1; ";
        return $base->getFetchRow($sql);
    }

    /** 新增用户信息 */
    public function insertUserInfo($data) {
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
            $sql = "insert into " . $base->table_name('user_info') . " values (null," . $set . ");";
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 更新用户信息 */
    public function updateUserInfo($id, $data) {
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
            $sql = "update " . $base->table_name('user_info') . " set " . $set . "  where id=" . $id . " ;";
            return $base->query($sql);
        } else {
            return true;
        }
    }

    /** 获取用户积分 */
    public function getUserPoint($userid) {
        $base = new BaseDAL();
        $sql = "select sum(`point`) as sum from " . $base->table_name("user_point") . " where `user_id`=" . $userid . " limit 1; ";
        return $base->getFetchRow($sql);
    }

    /** 根据电话和code获取用户信息 */
    public function getUserInfoByCode($phone, $code) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("sms") . "  "
                . "where `phone`='" . $phone . "' and `code`='" . $code . "' and `add_time` >= '" . date("Y-m-d H:i:s", strtotime("-15 minute")) . "' "
                . "limit 1";
        //echo $sql;
        $data = $base->getFetchAll($sql);
        if (empty($data)) {
            return "errorSms";
        }
        $sql = "select * from " . $base->table_name("user_info") . "  "
                . "where `phone`='" . $phone . "' "
                . "limit 1";
        $user = $base->getFetchRow($sql);
        if (!empty($user)) {
            return $user;
        }
        return "emptyUser";
    }

    /** 检查用户是否存在 */
    public function checkEnterPrise($phone, $password) {

//        if (!is_numeric($phone)) {
//            return ['error' => '1', 'code' => "errorPhone"];
//        }
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("user") . "  "
                . "where `name`='" . $phone . "' "
                . "limit 1";
        $user = $base->getFetchRow($sql);
        if (empty($user)) {
            return ['error' => '1', 'code' => "emptyUser"];
        }
        if ($user['password'] !== md5($password)) {
            return ['error' => '1', 'code' => "errorPassword"];
        }
        return ['error' => '0', 'code' => "", 'data' => $user];
    }

}
