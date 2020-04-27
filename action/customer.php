<?php

namespace action;

use http\Exception;
use mod\common as Common;
use mod\init;
use TigerDAL\CatchDAL;
use TigerDAL\Cms\UserInfoDAL;
use TigerDAL\Cms\EnterpriseDAL;
use TigerDAL\Cms\CourseDAL;
use config\code;

class customer {

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
                $this->enterprise_id = '';
            }
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
    }

    /** 获取应聘者列表 */
    function index() {
        //Common::pr(date("Y-m-d H:i:s"));die;
        Common::isset_cookie();
        Common::writeSession($_SERVER['REQUEST_URI'], $this->class);
        //Common::pr(Common::getSession($this->class));die;
        $currentPage = isset($_GET['currentPage']) ? $_GET['currentPage'] : 1;
        $pagesize = isset($_GET['pagesize']) ? $_GET['pagesize'] : init::$config['page_width'];
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : "";
        try {
            self::$data['data'] = UserInfoDAL::getAll($currentPage, $pagesize, $keywords, $this->enterprise_id);
            self::$data['total'] = UserInfoDAL::getTotal($keywords, $this->enterprise_id);
//var_dump(self::$data['total']);die;
            self::$data['currentPage'] = $currentPage;
            self::$data['pagesize'] = $pagesize;
            self::$data['keywords'] = $keywords;
            self::$data['class'] = $this->class;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::USER_INDEX], code::USER_INDEX, json_encode($ex));
        }
        init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function getCustomer() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                $res = UserInfoDAL::getOne($id);
                self::$data['data'] = $res;
                if (!empty($this->enterprise_id)) {
                    $resCourse = UserInfoDAL::getUserEnterpriseCourseList($id, $this->enterprise_id);
                    $course = CourseDAL::getAll(1, 999, '', '', $this->enterprise_id);
                    self::$data['userCourse'] = $resCourse;
                    self::$data['course'] = $course;
                } else {
                    self::$data['userCourse'] = null;
                    self::$data['course'] = null;
                }
            } else {
                self::$data['data'] = null;
            }
            self::$data['class'] = $this->class;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::USER_INDEX], code::USER_INDEX, json_encode($ex));
        }
        init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function updateCustomer() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            $_data = [
                'status' => 1,
                'add_time' => date("Y-m-d H:i:s"),
                'edit_time' => date("Y-m-d H:i:s"),
                'delete' => 0,
            ];
            if (!empty($_POST['user_course_ids'])) {
                UserInfoDAL::saveUserCourse(array_unique($_POST['user_course_ids']), $id, $_data, $this->enterprise_id);
                Common::js_redir(Common::getSession($this->class));
            } else {
                Common::js_alert('修改失败，请联系系统管理员');
            }
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::CATEGORY_UPDATE], code::CATEGORY_UPDATE, json_encode($ex));
        }
    }

    function setEu() {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            $_data = [
                'status' => $_GET['status'],
                'edit_time' => date("Y-m-d H:i:s"),
            ];
            UserInfoDAL::saveEnterpriseUser($id, $this->enterprise_id, $_data);
            Common::js_redir(Common::getSession($this->class));
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::CATEGORY_UPDATE], code::CATEGORY_UPDATE, json_encode($ex));
        }
    }

    function setRelation() {
        $phone = isset($_GET['phone']) ? $_GET['phone'] : null;
        try {
            if (empty($this->enterprise_id)) {
                Common::js_alert_redir("非企业管理员", Common::getSession($this->class));
            }
            if (!UserInfoDAL::saveEnterpriseUserByPhone($phone, $this->enterprise_id)) {
                Common::js_alert_redir("用户不存在", Common::getSession($this->class));
            }
            Common::js_redir(Common::getSession($this->class));
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::CATEGORY_UPDATE], code::CATEGORY_UPDATE, json_encode($ex));
        }
    }

}
