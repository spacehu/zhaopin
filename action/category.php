<?php

namespace action;

use mod\common as Common;
use TigerDAL;
use TigerDAL\Cms\CategoryDAL;
use TigerDAL\Cms\ImageDAL;
use TigerDAL\Cms\CourseDAL;
use TigerDAL\Cms\EnterpriseDAL;
use config\code;
use TigerDAL\Api\LogDAL;

class category {

    private $class;
    public static $data;
    private $enterprise_id;

    function __construct() {
        $this->class = str_replace('action\\', '', __CLASS__);
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
    function __destruct() {
        LogDAL::_saveLog();
    }

    function index() {
        Common::isset_cookie();
        Common::writeSession($_SERVER['REQUEST_URI'], $this->class);
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        $cat_id = ($type == "view") ? 1 : 0;
        try {
            self::$data['total'] = CategoryDAL::getTotal("");
            $_data = CategoryDAL::tree($cat_id, 0, false);
            $_total = 0;
            if (!empty($_data)) {
                foreach ($_data as $k => $v) {
                    $cat_ids[] = $v['id'];
                }
                $cat_id = implode(',', $cat_ids);
                $countdata = CourseDAL::getByCatId($cat_id, $this->enterprise_id);
                if (!empty($countdata)) {
                    foreach ($countdata as $k => $v) {
                        $_count[$v['category_id']] = $v['num'];
                        $_total += $v['num'];
                    }
                    foreach ($_data as $k => $v) {
                        $_data[$k]['num'] = !empty($_count[$v['id']]) ? $_count[$v['id']] : 0;
                    }
                }
            }
            self::$data['data'] = $_data;
            self::$data['class'] = $this->class;
            self::$data['type'] = $type;
            self::$data['course_total'] = $_total;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function getCategory() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data['data'] = CategoryDAL::getOne($id);
            } else {
                self::$data['data'] = null;
            }
            self::$data['list'] = CategoryDAL::tree();
            self::$data['image'] = ImageDAL::getAll(1, 999, "");
            self::$data['config'] = \mod\init::$config['env'];
            //Common::pr(self::$data['list']);die;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function updateCategory() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            $media_id = 0;
            if ($_POST['edit_doc'] !== "") {
                $material = new material();
                $media_id = $material->_saveImage($_POST['edit_doc']);
            }
            if ($id != null) {
                $data = [
                    'parent_id' => $_POST['parent_id'],
                    'name' => $_POST['name'],
                    'overview' => $_POST['overview'],
                    'media_id' => $media_id,
                    'edit_by' => Common::getSession("id"),
                    'order_by' => isset($_POST['order_by']) ? $_POST['order_by'] : 50,
                ];
                self::$data = CategoryDAL::update($id, $data);
            } else {
                if (CategoryDAL::getByName($_POST['name'])) {
                    Common::js_alert(code::ALREADY_EXISTING_DATA);
                    TigerDAL\CatchDAL::markError(code::$code[code::ALREADY_EXISTING_DATA], code::ALREADY_EXISTING_DATA, json_encode($_POST));
                    Common::js_redir(Common::getSession($this->class));
                }
                //Common::pr(UserDAL::getUser($_POST['name']));die;
                $data = [
                    'parent_id' => $_POST['parent_id'],
                    'name' => $_POST['name'],
                    'overview' => $_POST['overview'],
                    'type' => "",
                    'order_by' => isset($_POST['order_by']) ? $_POST['order_by'] : 50,
                    'add_by' => Common::getSession("id"),
                    'add_time' => date("Y-m-d H:i:s"),
                    'edit_by' => Common::getSession("id"),
                    'edit_time' => date("Y-m-d H:i:s"),
                    'delete' => 0,
                    'media_id' => $media_id,
                ];
                self::$data = CategoryDAL::insert($data);
            }
            if (self::$data) {
                //Common::pr(Common::getSession($this->class));die;
                Common::js_redir(Common::getSession($this->class));
            } else {
                Common::js_alert('修改失败，请联系系统管理员');
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_UPDATE], code::CATEGORY_UPDATE, json_encode($ex));
        }
    }

    function deleteCategory() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data = CategoryDAL::delete($id);
            }
            Common::js_redir(Common::getSession($this->class));
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_DELETE], code::CATEGORY_DELETE, json_encode($ex));
        }
    }

}
