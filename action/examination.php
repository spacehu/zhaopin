<?php

namespace action;

use mod\common as Common;
use TigerDAL;
use TigerDAL\Cms\CourseDAL;
use TigerDAL\Cms\ExaminationDAL;
use TigerDAL\Cms\ExaminationTestDAL;
use TigerDAL\Cms\TestDAL;
use TigerDAL\Cms\EnterpriseDAL;
use TigerDAL\Cms\CategoryDAL;
use config\code;

class examination {

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
                    $this->enterprise_id = '';
                    //Common::js_alert_redir("缺乏参数：enterprise_id", ERROR_405);
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
            //Common::pr(self::$data);die;
            self::$data['total'] = ExaminationDAL::getTotal($keywords, $this->enterprise_id);
            self::$data['data'] = ExaminationDAL::getAll($currentPage, $pagesize, $keywords, $this->enterprise_id);
            self::$data['class'] = $this->class;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function getExamination() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $enterprise_id = $this->enterprise_id;
        try {
            if ($id != null) {
                self::$data['data'] = ExaminationDAL::getOne($id);
                self::$data['examination_test'] = ExaminationTestDAL::getAll($id);
                $examination_test_id = [];
                if (!empty(self::$data['examination_test'])) {
                    foreach (self::$data['examination_test'] as $k => $v) {
                        $examination_test_id[] = $v['test_id'];
                    }
                }
                self::$data['examination_test_id'] = $examination_test_id;
                $enterprise_id = self::$data['data']['enterprise_id'];
            } else {
                self::$data['data'] = null;
                self::$data['examination_test'] = null;
                self::$data['examination_test_id'] = null;
                $enterprise_id = $this->enterprise_id;
            }
            $cat_id=1;
            $tests = TestDAL::getExaminationTestList($enterprise_id);
            if(!empty($tests)){
                $cat_id=$tests[0]['cat_id'];
            }
            self::$data['category']=$cat_id;
            self::$data['categorys'] = CategoryDAL::getCategorys(1,99,"",1);
            self::$data['class'] = $this->class;
            self::$data['enterprise_id'] = $enterprise_id;
            //Common::pr(self::$data['list']);die;
            //Common::pr(self::$data);die;

        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function getExaminationTestList(){
        try {
            $enterprise_id = $_GET['enterprise_id'];
            $cat_id = $_GET['cat_id'];
            $data = TestDAL::getExaminationTestList($enterprise_id,$cat_id);
            echo json_encode(['success' => true,'data'=>$data]);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::MATERIAL_UPDATE], code::MATERIAL_UPDATE, json_encode($ex));
            echo json_encode(['success' => false, 'message' => '999']);
        }
    }

    function updateExamination() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                $data = [
                    'name' => $_POST['name'],
                    'percentage' => $_POST['percentage'],
                    'export_count' => $_POST['export_count'],
                    'total' => $_POST['total'],
                    'type' => $_POST['type'],
                    'edit_by' => Common::getSession("id"),
                    'enterprise_id' => $_POST['enterprise_id'],
                ];
                self::$data = ExaminationDAL::update($id, $data);
            } else {
                if (ExaminationDAL::getByName($_POST['name'])) {
                    Common::js_alert(code::ALREADY_EXISTING_DATA);
                    TigerDAL\CatchDAL::markError(code::$code[code::ALREADY_EXISTING_DATA], code::ALREADY_EXISTING_DATA, json_encode($_POST));
                    Common::js_redir(Common::getSession($this->class));
                }
                //Common::pr(UserDAL::getUser($_POST['name']));die;
                $data = [
                    'name' => $_POST['name'],
                    'percentage' => $_POST['percentage'],
                    'export_count' => $_POST['export_count'],
                    'total' => $_POST['total'],
                    'type' => $_POST['type'],
                    'add_by' => Common::getSession("id"),
                    'add_time' => date("Y-m-d H:i:s"),
                    'edit_by' => Common::getSession("id"),
                    'edit_time' => date("Y-m-d H:i:s"),
                    'delete' => 0,
                    'enterprise_id' => $_POST['enterprise_id'],
                ];
                self::$data = $id = ExaminationDAL::insertExamination($data);
            }
            if (self::$data) {
                    $_data = [
                        'add_by' => Common::getSession("id"),
                        'add_time' => date("Y-m-d H:i:s"),
                        'edit_by' => Common::getSession("id"),
                        'edit_time' => date("Y-m-d H:i:s"),
                        'delete' => 0,
                    ];
                    ExaminationTestDAL::save($_POST['test_add'], $id, $_data,$_POST['test_remove']);
                //Common::pr(Common::getSession($this->class));die;
                Common::js_redir(Common::getSession($this->class));
            } else {
                Common::js_alert('修改失败，请联系系统管理员');
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_UPDATE], code::CATEGORY_UPDATE, json_encode($ex));
        }
    }

    function deleteExamination() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data = ExaminationDAL::delete($id);
            }
            Common::js_redir(Common::getSession($this->class));
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_DELETE], code::CATEGORY_DELETE, json_encode($ex));
        }
    }

}
