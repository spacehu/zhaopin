<?php

namespace action;

use config\code;
use http\Exception;
use mod\common as Common;
use mod\init;
use TigerDAL\CatchDAL;
use TigerDAL\Cms\UserDAL;
use TigerDAL\Cms\RoleDAL;
use TigerDAL\Cms\EnterpriseDAL;

class admin {

    public static $data;

    function __construct() {
        
    }

    function index() {
        Common::isset_cookie();
        init::getTemplate('admin', 'main', false);
    }

    function main_top() {
        Common::isset_cookie();
        $id = Common::getSession("id");
        try {
            self::$data['data'] = UserDAL::getOne($id);
            self::$data['data']['role'] = RoleDAL::getOne(self::$data['data']['role_id']);
            self::$data['data']['enterprise'] = EnterpriseDAL::getOne(self::$data['data']['enterprise_id']);
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::USER_INDEX], code::USER_INDEX, json_encode($ex));
        }
        init::getTemplate('admin', 'top', false);
    }

    function main_right() {
        Common::isset_cookie();
        init::getTemplate('admin', 'right', false);
    }

    function main_left() {
        Common::isset_cookie();
        $id = Common::getSession("id");
        $user = UserDAL::getOne($id);
        $role = RoleDAL::getOne($user['role_id']);
        $_role='';
        if(!empty($role)){
            $_role=explode(";",$role['data']);
        }
        self::$data['role']=$_role;
        self::$data['data']= init::$config['leftMenu'];
        init::getTemplate('admin', 'left', false);
    }

    function error() {
        init::getTemplate('admin', 'error', false);
        exit;
    }

}
