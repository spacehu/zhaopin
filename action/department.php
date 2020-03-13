<?php

namespace action;

use mod\common as Common;
use TigerDAL;
use TigerDAL\Cms\EnterpriseDAL;
use TigerDAL\Cms\DepartmentDAL;
use TigerDAL\Cms\UserInfoDAL;
use TigerDAL\Cms\EnterpriseUserDAL;
use TigerDAL\Cms\CourseDAL;
use TigerDAL\Cms\EnterpriseCourseDAL;
use config\code;

class department {

    private $class;
    public static $data;
    private $enterprise_id;

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
            //Common::pr(self::$data);die;
            self::$data['total'] = DepartmentDAL::getTotal($this->enterprise_id, $keywords);
            self::$data['data'] = DepartmentDAL::getAll($currentPage, $pagesize, $this->enterprise_id, $keywords);
            self::$data['class'] = $this->class;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function getDepartment() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data['data'] = DepartmentDAL::getOne($id);
                self::$data['enterpriseUser'] = UserInfoDAL::getEnterpriseUser($this->enterprise_id, $id);
                self::$data['enterpriseCourse'] = EnterpriseCourseDAL::getEnterpriseCourse($this->enterprise_id, $id);
                $enterprise_id = self::$data['data']['enterprise_id'];
            } else {
                self::$data['data'] = null;
                self::$data['enterpriseUser'] = UserInfoDAL::getEnterpriseUser($this->enterprise_id, 0);
                self::$data['enterpriseCourse'] = EnterpriseCourseDAL::getEnterpriseCourse($this->enterprise_id, 0);
                $enterprise_id = $this->enterprise_id;
            }
            //Common::pr(self::$data['enterpriseCourse']);die;
            self::$data['class'] = $this->class;
            //print_r(self::$data['enterpriseUser']);die;
            self::$data['enterprise_id'] = $enterprise_id;
            //Common::pr(self::$data['list']);die;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function updateDepartment() {
        Common::isset_cookie();
        //var_dump($_POST['users']);die;
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                $data = [
                    'name' => $_POST['name'],
                    'enterprise_id' => $_POST['enterprise_id'],
                    'edit_by' => Common::getSession("id"),
                ];
                self::$data = DepartmentDAL::update($id, $data);
            } else {
                $data = [
                    'enterprise_id' => $_POST['enterprise_id'],
                    'name' => $_POST['name'],
                    'add_by' => Common::getSession("id"),
                    'add_time' => date("Y-m-d H:i:s"),
                    'edit_by' => Common::getSession("id"),
                    'edit_time' => date("Y-m-d H:i:s"),
                    'delete' => 0,
                ];
                $id = self::$data = DepartmentDAL::insertGetId($data);
            }
            if (self::$data) {
                //Common::pr(Common::getSession($this->class));die;
                if (!empty($_POST['departmentadd'])) {
                    EnterpriseUserDAL::updateDepartmentId($_POST['departmentadd'], $id, $_POST['enterprise_id']);
                }
                if (!empty($_POST['departmentremove'])) {
                    EnterpriseUserDAL::updateDepartmentId($_POST['departmentremove'], 0, $_POST['enterprise_id']);
                }
                if (!empty($_POST['courses_add'])) {
                    EnterpriseCourseDAL::updateDepartmentId($this->enterprise_id, $_POST['courses_add'], 0, $id);
                }
                if (!empty($_POST['courses_remove'])) {
                    EnterpriseCourseDAL::updateDepartmentId($this->enterprise_id, $_POST['courses_remove'], $id, 0);
                }
                Common::js_redir(Common::getSession($this->class));
            } else {
                Common::js_alert('修改失败，请联系系统管理员');
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_UPDATE], code::CATEGORY_UPDATE, json_encode($ex));
        }
    }

    function deleteDepartment() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data = DepartmentDAL::delete($id);
            }
            Common::js_redir(Common::getSession($this->class));
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_DELETE], code::CATEGORY_DELETE, json_encode($ex));
        }
    }

}
