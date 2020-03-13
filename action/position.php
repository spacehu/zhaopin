<?php

namespace action;

use mod\common as Common;
use TigerDAL;
use TigerDAL\Cms\PositionDAL;
use TigerDAL\Cms\EnterpriseDAL;
use TigerDAL\Cms\UserDAL;
use TigerDAL\Cms\UserInfoDAL;
use TigerDAL\Cms\EnterpriseUserDAL;
use TigerDAL\Cms\EnterpriseCourseDAL;
use config\code;

class position {

    private $class;
    public static $data;
    private $enterprise_id;
    private $department_id;

    function __construct() {
        $this->class = str_replace('action\\', '', __CLASS__);
        try {
            $_enterprise = EnterpriseDAL::getByUserId(Common::getSession("id"));
            if (!empty($_enterprise)) {
                $this->enterprise_id = $_enterprise['id'];
            } else {
                if (!empty($_GET['enterprise_id'])) {
                    $this->enterprise_id = $_GET['enterprise_id'];
                } else {
                    Common::js_alert_redir("缺乏参数：enterprise_id", ERROR_405);
                }
            }
            $this->department_id = $_REQUEST['department_id'];
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
    }

    function index() {
        Common::isset_cookie();
        Common::writeSession($_SERVER['REQUEST_URI'], $this->class);
        try {
            $currentPage = isset($_GET['currentPage']) ? $_GET['currentPage'] : 1;
            $pagesize = isset($_GET['pagesize']) ? $_GET['pagesize'] : \mod\init::$config['page_width'];
            $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : "";

            self::$data['currentPage'] = $currentPage;
            self::$data['pagesize'] = $pagesize;
            self::$data['keywords'] = $keywords;
            self::$data['enterprise_id'] = $this->enterprise_id;
            self::$data['department_id'] = $this->department_id;
            //Common::pr(self::$data);die;
            self::$data['total'] = PositionDAL::getTotal($this->enterprise_id, $this->department_id, $keywords);
            self::$data['data'] = PositionDAL::getAll($currentPage, $pagesize, $this->enterprise_id, $this->department_id, $keywords);
            self::$data['class'] = $this->class;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function getPosition() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data['data'] = PositionDAL::getOne($id);
                self::$data['enterpriseUser'] = UserInfoDAL::getEnterpriseUser($this->enterprise_id, $this->department_id, $id);
                self::$data['enterpriseCourse'] = EnterpriseCourseDAL::getEnterpriseCourse($this->enterprise_id, $this->department_id, $id);
            } else {
                self::$data['data'] = null;
                self::$data['enterpriseUser'] = UserInfoDAL::getEnterpriseUser($this->enterprise_id, $this->department_id, 0);
                self::$data['enterpriseCourse'] = EnterpriseCourseDAL::getEnterpriseCourse($this->enterprise_id, $this->department_id, 0);
            }
            self::$data['class'] = $this->class;
            self::$data['list'] = UserDAL::getAll(1, 999, '');
            self::$data['enterprise_id'] = $this->enterprise_id;
            self::$data['department_id'] = $this->department_id;
            //Common::pr(self::$data['department_id']);die;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function updatePosition() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                $data = [
                    'name' => $_POST['name'],
                    'edit_by' => Common::getSession("id"),
                ];
                self::$data = PositionDAL::update($id, $data);
            } else {
                //Common::pr(UserDAL::getUser($_POST['name']));die;
                $data = [
                    'enterprise_id' => $_POST['enterprise_id'],
                    'department_id' => $_POST['department_id'],
                    'name' => $_POST['name'],
                    'add_by' => Common::getSession("id"),
                    'add_time' => date("Y-m-d H:i:s"),
                    'edit_by' => Common::getSession("id"),
                    'edit_time' => date("Y-m-d H:i:s"),
                    'delete' => 0,
                ];
                $id = self::$data = PositionDAL::insertGetId($data);
            }
            if (self::$data) {
                //Common::pr(Common::getSession($this->class));die;
                if (!empty($_POST['departmentadd'])) {
                    EnterpriseUserDAL::updatePositionId($_POST['departmentadd'], $id, $_POST['enterprise_id']);
                }
                if (!empty($_POST['departmentremove'])) {
                    EnterpriseUserDAL::updatePositionId($_POST['departmentremove'], 0, $_POST['enterprise_id']);
                }
                if (!empty($_POST['courses_add'])) {
                    EnterpriseCourseDAL::updatePositionId($this->enterprise_id, $_POST['department_id'], $_POST['courses_add'], 0, $id);
                }
                if (!empty($_POST['courses_remove'])) {
                    EnterpriseCourseDAL::updatePositionId($this->enterprise_id, $_POST['department_id'], $_POST['courses_remove'], $id, 0);
                }
                Common::js_redir(Common::getSession($this->class));
            } else {
                Common::js_alert('修改失败，请联系系统管理员');
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_UPDATE], code::CATEGORY_UPDATE, json_encode($ex));
        }
    }

    function deletePosition() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data = PositionDAL::delete($id);
            }
            Common::js_redir(Common::getSession($this->class));
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_DELETE], code::CATEGORY_DELETE, json_encode($ex));
        }
    }

}
