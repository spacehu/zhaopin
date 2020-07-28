<?php

namespace action;

use http\Exception;
use mod\common as Common;
use mod\csv;
use mod\init;
use TigerDAL\CatchDAL;
use TigerDAL\Cms\EnumDAL;
use TigerDAL\Cms\ArticleDAL;
use TigerDAL\Cms\EnterpriseDAL;
use TigerDAL\Cms\UserResumeArticleDAL;
use TigerDAL\Api\EnumLeoDAL;
use config\code;

class show {

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
            CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
    }

    function index() {
        Common::isset_cookie();
        Common::writeSession($_SERVER['REQUEST_URI'], $this->class);
        try {
            $currentPage = isset($_GET['currentPage']) ? $_GET['currentPage'] : 1;
            $pagesize = isset($_GET['pagesize']) ? $_GET['pagesize'] : init::$config['page_width'];
            $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : "";

            self::$data['currentPage'] = $currentPage;
            self::$data['pagesize'] = $pagesize;
            self::$data['keywords'] = $keywords;
            self::$data['class'] = $this->class;


            self::$data['data'] = ArticleDAL::getAll($currentPage, $pagesize, $keywords, '', $this->enterprise_id);
            self::$data['total'] = ArticleDAL::getTotal($keywords,'', $this->enterprise_id);

            init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::SHOW_INDEX], code::SHOW_INDEX, json_encode($ex));
        }
    }

    function getShow() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data['data'] = ArticleDAL::getOne($id);
                self::$data['region'] = EnumLeoDAL::GetRegionFamily(self::$data['data']['city']);
            } else {
                self::$data['data'] = null;
                self::$data['region'] = [];
            }
            //Common::pr(self::$data['data']);die;
            self::$data['class'] = $this->class;
            self::$data['enterprise_id'] = $this->enterprise_id;
            self::$data['enumList'] = EnumDAL::getAllDecode(['薪资','工作经验','行业']);
            //Common::pr(self::$data['enumList']);die;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::SHOW_INDEX], code::SHOW_INDEX, json_encode($ex));
        }
        init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function updateShow() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            $media_id = 0;
            if ($id != null) {
                /** 更新操作 */
                $data = [
                    'name' => $_POST['name'],
                    'overview' => isset($_POST['overview']) ? $_POST['overview'] : '',
                    'detail' => isset($_POST['detail']) ? $_POST['detail'] : '',
                    'cat_id' => isset($_POST['cat_id']) ? $_POST['cat_id'] : 0,
                    'access' => isset($_POST['access']) ? $_POST['access'] : 0,
                    'source' => isset($_POST['source']) ? $_POST['source'] : '',
                    'media_id' => $media_id,
                    'edit_by' => Common::getSession("id"),
                    'type' => isset($_POST['type']) ? $_POST['type'] : '',
                    'salary' => isset($_POST['salary']) ? $_POST['salary'] : '',
                    'province' => isset($_POST['province']) ? $_POST['province'] : '',
                    'city' => isset($_POST['city']) ? $_POST['city'] : '',
                    'district' => isset($_POST['district']) ? $_POST['district'] : '',
                    'address' => isset($_POST['address']) ? $_POST['address'] : '',
                    'age_min' => isset($_POST['age_min']) ? $_POST['age_min'] : '',
                    'age_max' => isset($_POST['age_max']) ? $_POST['age_max'] : '',
                    'education' => isset($_POST['education']) ? $_POST['education'] : '',
                    'tag' => isset($_POST['tag']) ? $_POST['tag'] : '',
                    'responsibilities' => isset($_POST['responsibilities']) ? $_POST['responsibilities'] : '',
                    'qualifications' => isset($_POST['qualifications']) ? $_POST['qualifications'] : '',
                    'enterprise_id' => !empty($_POST['enterprise_id']) ? $_POST['enterprise_id'] : 0,
                    'examination_id' => !empty($_POST['examination_id']) ? $_POST['examination_id'] : 0,
                ];
                self::$data = ArticleDAL::update($id, $data);
            } else {
                /** 新增操作 */
                $data = [
                    'name' => $_POST['name'],
                    'overview' => isset($_POST['overview']) ? $_POST['overview'] : '',
                    'detail' => isset($_POST['detail']) ? $_POST['detail'] : '',
                    'cat_id' => isset($_POST['cat_id']) ? $_POST['cat_id'] : 0,
                    'order_by' => 50,
                    'add_by' => Common::getSession("id"),
                    'add_time' => date("Y-m-d H:i:s"),
                    'edit_by' => Common::getSession("id"),
                    'edit_time' => date("Y-m-d H:i:s"),
                    'delete' => 0,
                    'access' => isset($_POST['access']) ? $_POST['access'] : 0,
                    'source' => isset($_POST['source']) ? $_POST['source'] : '',
                    'media_id' => $media_id,
                    'type' => isset($_POST['type']) ? $_POST['type'] : '',
                    'salary' => isset($_POST['salary']) ? $_POST['salary'] : '',
                    'province' => isset($_POST['province']) ? $_POST['province'] : '',
                    'city' => isset($_POST['city']) ? $_POST['city'] : '',
                    'district' => isset($_POST['district']) ? $_POST['district'] : '',
                    'address' => isset($_POST['address']) ? $_POST['address'] : '',
                    'age_min' => isset($_POST['age_min']) ? $_POST['age_min'] : '',
                    'age_max' => isset($_POST['age_max']) ? $_POST['age_max'] : '',
                    'education' => isset($_POST['education']) ? $_POST['education'] : '',
                    'tag' => isset($_POST['tag']) ? $_POST['tag'] : '',
                    'responsibilities' => isset($_POST['responsibilities']) ? $_POST['responsibilities'] : '',
                    'qualifications' => isset($_POST['qualifications']) ? $_POST['qualifications'] : '',
                    'enterprise_id' => !empty($_POST['enterprise_id']) ? $_POST['enterprise_id'] : 0,
                    'examination_id' => !empty($_POST['examination_id']) ? $_POST['examination_id'] : 0,
                ];
                self::$data = ArticleDAL::insert($data);
            }
            if (self::$data) {
                //Common::pr(Common::getSession($this->class));die;
                Common::js_redir(Common::getSession($this->class));
            } else {
                Common::js_alert('修改失败，请联系系统管理员');
            }
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::SHOW_UPDATE], code::SHOW_UPDATE, json_encode($ex));
        }
    }

    function deleteShow() {
        Common::isset_cookie();
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            if ($id != null) {
                self::$data = ArticleDAL::delete($id);
            }
            Common::js_redir(Common::getSession($this->class));
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::SHOW_DELETE], code::SHOW_DELETE, json_encode($ex));
        }
    }

    function getResumeList() {
        Common::writeSession($_SERVER['REQUEST_URI'], $this->class);
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            $currentPage = isset($_GET['currentPage']) ? $_GET['currentPage'] : 1;
            $pagesize = isset($_GET['pagesize']) ? $_GET['pagesize'] : init::$config['page_width'];

            self::$data['currentPage'] = $currentPage;
            self::$data['pagesize'] = $pagesize;
            self::$data['class'] = $this->class;
            self::$data['id'] = $id;


            self::$data['data'] = UserResumeArticleDAL::getAll($currentPage, $pagesize, $id);
            self::$data['total'] = UserResumeArticleDAL::getTotal($id);

            init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::SHOW_INDEX], code::SHOW_INDEX, json_encode($ex));
        }
    }

    function getUserResume() {
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
        try {
            self::$data['data'] = $data = UserResumeArticleDAL::getOne($user_id);
            //Common::pr($data);die;
            if(!empty($_GET['export'])&&$_GET['export']==2){
                $headlist=[
                    ['简历编号',$data['id']],
                ];
                $_data=[
                    ['姓名',$data['name']],
                    ['生日',$data['brithday']],
                    ['Email',$data['email']],
                    ['电话号',$data['phone']],
                    ['自我简介',$data['about_self']],
                ];
                if(!empty($data['school'])){
                    $_data[]=['教育经历'];
                    $_data[]=['毕业学校','攻读专业','就读年份（起）','就读年份（止）','最高学位'];
                    foreach($data['school'] as $k=>$v){
                        $_data[]=[
                            $v['school'],$v['profession'],$v['start_time'],$v['end_time'],$v['education'],
                        ];
                    }
                }
                if(!empty($data['company'])){
                    $_data[]=['工作经历'];
                    $_data[]=['所在公司','所在职位','在职时间（起）','在职时间（止）','工作内容'];
                    foreach($data['company'] as $k=>$v){
                        $_data[]=[
                            $v['company'],$v['job'],$v['start_time'],$v['end_time'],$v['infomation'],
                        ];
                    }
                }
                $csv=new Csv();
                $csv->mkcsvMore($_data,$headlist,"getCustomerResume-".$user_id."-".$data['name']."-".date("YmdHis"));
                exit();
            }
            self::$data['class'] = $this->class;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::SHOW_INDEX], code::SHOW_INDEX, json_encode($ex));
        }
        init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    function deleteUserResumeArticle() {
        $ura_id = isset($_GET['ura_id']) ? $_GET['ura_id'] : null;
        try {
            self::$data['data'] = UserResumeArticleDAL::sendResume($ura_id);
            //Common::pr(self::$data['data']);die;
            Common::js_redir(Common::getSession($this->class));
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::SHOW_INDEX], code::SHOW_INDEX, json_encode($ex));
        }
    }

}
