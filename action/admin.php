<?php

namespace action;

use mod\common as Common;
use TigerDAL\Cms\UserDAL;
use TigerDAL\Cms\RoleDAL;
use TigerDAL\Cms\EnterpriseDAL;

class admin {

    public static $data;

    function __construct() {
        
    }

    function index() {
        Common::isset_cookie();
        \mod\init::getTemplate('admin', 'main', false);
    }

    function main_top() {
        Common::isset_cookie();
        $id = Common::getSession("id");
        try {
            self::$data['data'] = UserDAL::getOne($id);
            self::$data['data']['role'] = RoleDAL::getOne(self::$data['data']['role_id']);
            self::$data['data']['enterprise'] = EnterpriseDAL::getOne(self::$data['data']['enterprise_id']);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::USER_INDEX], code::USER_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', 'top', false);
    }

    function main_right() {
        Common::isset_cookie();
        \mod\init::getTemplate('admin', 'right', false);
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
        self::$data['data']=\mod\init::$config['leftMenu'];
        \mod\init::getTemplate('admin', 'left', false);
    }

    function error() {
        \mod\init::getTemplate('admin', 'error', false);
        exit;
    }

}
