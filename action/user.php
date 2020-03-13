<?php

namespace action;

use mod\common as Common;
use TigerDAL;
use TigerDAL\Cms\UserDAL;
use TigerDAL\Cms\RoleDAL;
use TigerDAL\Cms\EnterpriseDAL;
use config\code;

class user {

    private $class;
    public static $data;

    function __construct() {
        $this->class = str_replace('action\\', '', __CLASS__);
    }

    function index() {
        //Common::pr(date("Y-m-d H:i:s"));die;
        Common::isset_cookie();
        Common::writeSession($_SERVER['REQUEST_URI'], $this->class);
        //Common::pr(Common::getSession($this->class));die;
        $currentPage = isset($_GET['currentPage']) ? $_GET['currentPage'] : 1;
        $pagesize = isset($_GET['pagesize']) ? $_GET['pagesize'] : \mod\init::$config['page_width'];
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : "";
        try {
            self::$data['data'] = UserDAL::getAll($currentPage, $pagesize, $keywords);
            self::$data['total'] = UserDAL::getTotal($keywords);

            self::$data['currentPage'] = $currentPage;
            self::$data['pagesize'] = $pagesize;
            self::$data['keywords'] = $keywords;
            self::$data['class'] = $this->class;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::USER_INDEX], code::USER_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function getUser() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data['data'] = UserDAL::getOne($id);
            } else {
                self::$data['data'] = null;
            }
            self::$data['list'] = RoleDAL::getAll(1, 99, "");
            self::$data['enterprise'] = EnterpriseDAL::getAll(1, 999, "");
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::USER_INDEX], code::USER_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function updateUser() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                if (!empty($_POST['password'])) {
                    $data = [
                        'password' => md5($_POST['password']),
                    ];
                } else {
                    $data = [
                        'role_id' => $_POST['role_id'],
                        'edit_by' => Common::getSession("id"),
                        'enterprise_id' => $_POST['enterprise_id'],
                        'email' => $_POST['email'],
                        'is_email' => isset($_POST['is_email']) ? 1 : 0,
                        'mail_content' => isset($_POST['mail_content']) ? $_POST['mail_content'] : "",
                        'start_date' => isset($_POST['start_date']) ? $_POST['start_date'] : null,
                        'times' => isset($_POST['times']) ? $_POST['times'] : "",
                        'just_date' => isset($_POST['just_date']) ? $_POST['just_date'] : null,
                    ];
                }
                //Common::pr($data);die;

                self::$data = UserDAL::update($id, $data);
            } else {
                if (UserDAL::getByName($_POST['name'])) {
                    Common::js_alert(code::ALREADY_EXISTING_DATA);
                    TigerDAL\CatchDAL::markError(code::$code[code::ALREADY_EXISTING_DATA], code::ALREADY_EXISTING_DATA, json_encode($_POST));
                    Common::js_redir(Common::getSession($this->class));
                }
                //Common::pr(UserDAL::getUser($_POST['name']));die;
                $data = [
                    'name' => $_POST['name'],
                    'password' => md5(\mod\init::$config['config']['password']),
                    'add_by' => Common::getSession("id"),
                    'add_time' => date("Y-m-d H:i:s"),
                    'edit_by' => Common::getSession("id"),
                    'edit_time' => date("Y-m-d H:i:s"),
                    'role_id' => $_POST['role_id'],
                    'delete' => 0,
                    'enterprise_id' => $_POST['enterprise_id'],
                    'email' => $_POST['email'],
                    'is_email' => isset($_POST['is_email']) ? 1 : 0,
                    'mail_content' => isset($_POST['mail_content']) ? $_POST['mail_content'] : "",
                    'start_date' => isset($_POST['start_date']) ? $_POST['start_date'] : "",
                    'times' => isset($_POST['times']) ? $_POST['times'] : "",
                    'just_date' => isset($_POST['just_date']) ? $_POST['just_date'] : "",
                ];
                self::$data = UserDAL::insert($data);
            }
            if (self::$data) {
                //Common::pr(Common::getSession($this->class));die;
                Common::js_redir(Common::getSession($this->class));
            } else {
                Common::js_alert('修改失败，请联系系统管理员');
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::USER_UPDATE], code::USER_UPDATE, json_encode($ex));
        }
    }

    function deleteUser() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data = UserDAL::delete($id);
            }
            Common::js_redir(Common::getSession($this->class));
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::USER_DELETE], code::USER_DELETE, json_encode($ex));
        }
    }

}
