<?php

namespace action;

use http\Exception;
use mod\common as Common;
use mod\init;
use TigerDAL\CatchDAL;
use TigerDAL\Cms\UserDAL;
use config\code;

class account {

    private $class;
    public static $data;

    function __construct() {
        $this->class = str_replace('action\\', '', __CLASS__);
    }

    function index() {
        
    }

    function getAccount() {
        Common::isset_cookie();
        Common::writeSession($_SERVER['REQUEST_URI'], $this->class);
        $id = Common::getSession("id");
        try {
            if ($id != null) {
                self::$data['data'] = UserDAL::getOne($id);
            } else {
                self::$data['data'] = null;
            }
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::USER_INDEX], code::USER_INDEX, json_encode($ex));
        }
        init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function updateAccount() {
        Common::isset_cookie();
        $id = Common::getSession("id");
        try {
            if ($_POST['password'] !== $_POST['password_cfn']) {
                Common::js_alert(code::$code['errorPasswordDifferent']);
                CatchDAL::markError(code::$code[code::$code['errorPasswordDifferent']], code::$code['errorPasswordDifferent'], json_encode($_POST));
                Common::js_redir(Common::getSession($this->class));
            }
            $data = [
                'password' => md5($_POST['password']),
                'edit_time' => date("Y-m-d H:i:s"),
            ];
            self::$data = UserDAL::update($id, $data);

            if (self::$data) {
                //Common::pr(Common::getSession($this->class));die;
                Common::js_redir(Common::getSession($this->class));
            } else {
                Common::js_alert('修改失败，请联系系统管理员');
            }
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::USER_UPDATE], code::USER_UPDATE, json_encode($ex));
        }
    }

}
