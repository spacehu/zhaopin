<?php

namespace action;

use mod\common as Common;
use TigerDAL;
use TigerDAL\Cms\CategoryDAL;
use TigerDAL\Cms\CourseDAL;
use TigerDAL\Cms\ImageDAL;
use TigerDAL\Cms\EnterpriseDAL;
use TigerDAL\Cms\EnterpriseCourseDAL;
use config\code;

class course {

    private $class;
    public static $data;
    private $cat_id;
    private $enterprise_id;

    function __construct() {
        $this->class = str_replace('action\\', '', __CLASS__);
        //课程类
        $this->cat_id = !empty($_REQUEST['cat_id']) ? $_REQUEST['cat_id'] : 1;
        //企业id
        try {
            $_enterprise = EnterpriseDAL::getByUserId(Common::getSession("id"));
            if (!empty($_enterprise)) {
                $this->enterprise_id = $_enterprise['id'];
            } else {
                $this->enterprise_id = '';
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
            //Common::pr(self::$data);die;
            self::$data['total'] = CourseDAL::getTotal($keywords, $this->cat_id, $this->enterprise_id);
            self::$data['data'] = CourseDAL::getAll($currentPage, $pagesize, $keywords, $this->cat_id, $this->enterprise_id);
            self::$data['class'] = $this->class;
            self::$data['cat_id'] = $this->cat_id;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function getCourse() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data['data'] = CourseDAL::getOne($id);
                self::$data['enterprise_course'] = EnterpriseCourseDAL::getAll($id);
            } else {
                self::$data['data'] = null;
                self::$data['enterprise_course'] = null;
            }
            self::$data['cat_id'] = $this->cat_id;
            self::$data['image'] = ImageDAL::getAll(1, 99, '');
            self::$data['enterprise'] = EnterpriseDAL::getAll(1, 99, '');
            self::$data['class'] = $this->class;
            self::$data['enterprise_id'] = $this->enterprise_id;
            //echo $this->enterprise_id;
            self::$data['config'] = \mod\init::$config['env'];
            //Common::pr(self::$data['enterprise_course']);die;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function updateCourse() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            $media_id = 0;
            if ($_POST['edit_doc'] !== "") {
                $material = new material();
                $media_id = $material->_saveImage($_POST['edit_doc']);
            }
//            if (CourseDAL::getByName($_POST['name'], $id)) {
//                Common::js_alert(code::ALREADY_EXISTING_DATA);
//                TigerDAL\CatchDAL::markError(code::$code[code::ALREADY_EXISTING_DATA], code::ALREADY_EXISTING_DATA, json_encode($_POST));
//                Common::js_redir(Common::getSession($this->class));
//            }
            if ($id != null) {
                $data = [
                    'category_id' => $this->cat_id,
                    'name' => $_POST['name'],
                    'overview' => $_POST['overview'],
                    'detail' => $_POST['detail'],
                    'order_by' => $_POST['order_by'],
                    'edit_by' => Common::getSession("id"),
                    'media_id' => $media_id,
                    'text_max' => $_POST['text_max'],
                    'enterprise_id' => 0,
                    'percentage' => $_POST['percentage'],
                ];
                self::$data = CourseDAL::update($id, $data);
            } else {
                //Common::pr(UserDAL::getUser($_POST['name']));die;
                $data = [
                    'category_id' => $this->cat_id,
                    'name' => $_POST['name'],
                    'overview' => $_POST['overview'],
                    'detail' => $_POST['detail'],
                    'order_by' => $_POST['order_by'],
                    'add_by' => Common::getSession("id"),
                    'add_time' => date("Y-m-d H:i:s"),
                    'edit_by' => Common::getSession("id"),
                    'edit_time' => date("Y-m-d H:i:s"),
                    'delete' => 0,
                    'media_id' => $media_id,
                    'text_max' => $_POST['text_max'],
                    'enterprise_id' => 0,
                    'percentage' => $_POST['percentage'],
                ];
                $id = self::$data = CourseDAL::insertById($data);
            }
            if (self::$data) {
                $_data = [
                    'add_by' => Common::getSession("id"),
                    'add_time' => date("Y-m-d H:i:s"),
                    'edit_by' => Common::getSession("id"),
                    'edit_time' => date("Y-m-d H:i:s"),
                    'delete' => 0,
                    'department_id' => 0,
                    'position_id' => 0,
                ];
                if (!empty($_POST['enterprise_id'])) {
                    EnterpriseCourseDAL::save(array_unique($_POST['enterprise_id']), $id, $_data);
                }
                if (!empty($this->enterprise_id)) {
                    EnterpriseCourseDAL::save([$this->enterprise_id], $id, $_data);
                }
                //Common::pr(Common::getSession($this->class));die;
                Common::js_redir(Common::getSession($this->class));
            } else {
                Common::js_alert('修改失败，请联系系统管理员');
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_UPDATE], code::CATEGORY_UPDATE, json_encode($ex));
        }
    }

    function deleteCourse() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data = CourseDAL::delete($id);
            }
            Common::js_redir(Common::getSession($this->class));
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_DELETE], code::CATEGORY_DELETE, json_encode($ex));
        }
    }

}
