<?php

namespace action\v4;

use action\RestfulApi;
use http\Exception;
use mod\common;
use mod\init;
use TigerDAL\Api\AuthDAL;
use TigerDAL\Api\TokenDAL;
use TigerDAL\Api\EnterpriseDAL;
use TigerDAL\Api\CourseDAL;
use TigerDAL\Api\TestDAL;
use TigerDAL\Api\AccountDAL;
use TigerDAL\Api\LessonDAL;
use TigerDAL\Api\ResumeDAL;
use TigerDAL\Api\LogDAL;
use TigerDAL\CatchDAL;
use TigerDAL\Cms\UserDAL;
use TigerDAL\Cms\UserInfoDAL as cmsUserInfoDAL;
use TigerDAL\Cms\EnterpriseDAL as cmsEnterpriseDAL;
use TigerDAL\Cms\UserResumeArticleDAL as cmsUserResumeArticleDAL;
use config\code;

class ApiAccount extends RestfulApi {

    public $user_id;
    public $server_id;

    /**
     * 主方法引入父类的基类
     * 责任是分担路由的工作
     */
    function __construct() {
        $path = parent::__construct();
        $TokenDAL = new TokenDAL();
        $_token = $TokenDAL->checkToken();
        //Common::pr($_token);die;
        if ($_token['code'] != 90001) {
            self::$data['success'] = false;
            self::$data['data']['error_msg'] = 'tokenerror';
            self::$data['data']['code'] = $_token['code'];
            self::$data['msg'] = code::$code['tokenerror'];
            exit(json_encode(self::$data));
        }
        $this->user_id = $_token['data']['user_id'];
        $this->server_id = $_token['data']['server_id'];
        if (!empty($path)) {
            $_path = explode("-", $path);
            $mod= $_path['2'];
            $res=$this->$mod();
            exit(json_encode($res));
        }
    }

    /*  post   * ****************************************************************************** */

    /** 参与课程 */
    function course() {
        try {
            //轮播列表
            $_data = [
                'user_id' => $this->user_id,
                'course_id' => $this->post['course_id'],
                'status' => 1,
                'add_time' => date("Y-m-d H:i:s"),
                'edit_time' => date("Y-m-d H:i:s"),
                'delete' => 0,
            ];
            $res = CourseDAL::joinCourse($_data);

            //print_r($res);die;
            self::$data['data'] = $res;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 参与课时 */
    function lesson() {
        try {
            //轮播列表
            $_data = [
                'user_id' => $this->user_id,
                'lesson_id' => $this->post['lesson_id'],
                'status' => 1,
                'add_time' => date("Y-m-d H:i:s"),
                'edit_time' => date("Y-m-d H:i:s"),
                'delete' => 0,
            ];
            $res = LessonDAL::joinLesson($_data);

            //print_r($res);die;
            self::$data['data'] = $res;
        } catch (Exception $ex) {
           CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 参与考试 */
    function testing() {
        try {
            //轮播列表
            $_data = [
                'user_id' => $this->user_id,
                'course_id' => !empty($this->post['course_id']) ? $this->post['course_id'] : 0,
                'aws' => (array) $this->post['aws'],
                'time' => date("Y-m-d H:i:s"),
                'examination_id' => !empty($this->post['examination_id']) ? $this->post['examination_id'] : 0,
            ];
            $res = TestDAL::joinTest($_data);

            //print_r($res);die;
            self::$data['data'] = $res;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 收藏 */
    function favorite() {
        try {
            //轮播列表
            $res = AccountDAL::doFavorites($this->user_id, $this->post['article_id']);

            //print_r($res);die;
            self::$data['data'] = $res;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 绑定企业 */
    function enterprise() {
        try {
            //轮播列表
            $res = AccountDAL::doEnterpriseRelation($this->user_id, $this->post['code']);
            if ($res === "errorCode") {
                self::$data['success'] = false;
                self::$data['data']['error_msg'] = 'errorCode';
                self::$data['data']['code'] = $res;
                self::$data['msg'] = code::$code[$res];
                return self::$data;
            }
            //print_r($res);die;
            self::$data['data'] = $res;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 解绑企业 */
    function unEnterprise() {
        try {
            //轮播列表
            $res = AccountDAL::unEnterpriseRelation($this->user_id, $this->post['enterprise_id']);
            //print_r($res);die;
            self::$data['data'] = $res;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 投递简历 */
    function sendResume() {
        try {
            //轮播列表
            $res = ResumeDAL::sendResume($this->user_id, $this->post['article_id']);
            if($res===code::NULL_DATA){
                self::$data['success']=false;
                self::$data['data']['error_msg'] = 'emptyUserResume';
                self::$data['data']['code'] = $res;
                self::$data['msg'] = code::$code[$res];
                return self::$data;
            }
            self::$data['data'] = $res;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 员工：更新简历 */
    function updateResume() {
        try {
            $_data = [
                'user_id' => $this->user_id,
                'serialization' => "",
                'add_time' => date("Y-m-d H:i:s"),
                'edit_time' => date("Y-m-d H:i:s"),
                'name' => isset($this->post['name']) ? $this->post['name'] : "",
                'brithday' => isset($this->post['brithday']) ? $this->post['brithday'] : "",
                'sex' => isset($this->post['sex']) ? $this->post['sex'] : "",
                'education' => isset($this->post['education']) ? $this->post['education'] : "",
                'age_work' => isset($this->post['age_work']) ? $this->post['age_work'] : "",
                'phone' => isset($this->post['phone']) ? $this->post['phone'] : "",
                'email' => isset($this->post['email']) ? $this->post['email'] : "",
                'delete' => 0,
                'about_self' => isset($this->post['about_self']) ? $this->post['about_self'] : "",
            ];
            $resume_id = ResumeDAL::saveOne($_data);
            if (!empty($resume_id)) {
                if (!empty($this->post['school'])) {
                    foreach ($this->post['school'] as $k => $v) {
                        $v = (array) $v;
                        $_school = [
                            'id' => isset($v['id']) ? $v['id'] : 0,
                            'user_id' => $this->user_id,
                            'user_resume_id' => $resume_id,
                            'add_time' => date("Y-m-d H:i:s"),
                            'edit_time' => date("Y-m-d H:i:s"),
                            'school' => isset($v['school']) ? $v['school'] : "",
                            'profession' => isset($v['profession']) ? $v['profession'] : "",
                            'start_time' => isset($v['start_time']) ? $v['start_time'] : "",
                            'end_time' => isset($v['end_time']) ? $v['end_time'] : "",
                            'delete' => isset($v['delete']) ? $v['delete'] : 0,
                            'education' => isset($v['education']) ? $v['education'] : "",
                        ];
                        ResumeDAL::saveOneSchool($_school);
                    }
                }
                if (!empty($this->post['company'])) {
                    foreach ($this->post['company'] as $k => $v) {
                        $v = (array) $v;
                        $_company = [
                            'id' => isset($v['id']) ? $v['id'] : 0,
                            'user_id' => $this->user_id,
                            'user_resume_id' => $resume_id,
                            'add_time' => date("Y-m-d H:i:s"),
                            'edit_time' => date("Y-m-d H:i:s"),
                            'company' => isset($v['company']) ? $v['company'] : "",
                            'job' => isset($v['job']) ? $v['job'] : "",
                            'start_time' => isset($v['start_time']) ? $v['start_time'] : "",
                            'end_time' => isset($v['end_time']) ? $v['end_time'] : "",
                            'infomation' => isset($v['infomation']) ? $v['infomation'] : "",
                            'delete' => isset($v['delete']) ? $v['delete'] : 0,
                        ];
                        ResumeDAL::saveOneCompany($_company);
                    }
                }
                if (!empty($this->post['project'])) {
                    foreach ($this->post['project'] as $k => $v) {
                        $v = (array) $v;
                        $_project = [
                            'id' => isset($v['id']) ? $v['id'] : 0,
                            'user_id' => $this->user_id,
                            'user_resume_id' => $resume_id,
                            'add_time' => date("Y-m-d H:i:s"),
                            'edit_time' => date("Y-m-d H:i:s"),
                            'name' => isset($v['name']) ? $v['name'] : "",
                            'start_time' => isset($v['start_time']) ? $v['start_time'] : "",
                            'end_time' => isset($v['end_time']) ? $v['end_time'] : "",
                            'overview' => isset($v['overview']) ? $v['overview'] : "",
                            'delete' => isset($v['delete']) ? $v['delete'] : 0,
                        ];
                        ResumeDAL::saveOneProject($_project);
                    }
                }

                self::$data['data'] = $resume_id;
            }
            //print_r($res);die;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 编辑用户信息 头像路径，手机号，验证码，姓名，（原密码），密码，确认密码 
     * name
     * 
     * photo
     * 
     * password
     * new_password
     * new_password_cfn
     * 
     * phone
     * code
     */
    function updateUsersInfo() {
        $AuthDAL = new AuthDAL();
        try {
            //密码
            if (!empty($this->post['new_password'])) {
                $_check = $AuthDAL->checkPassword($this->user_id, $this->post['password']);
                if ($_check['error'] == 1) {
                    self::$data['success'] = false;
                    self::$data['data']['error_msg'] = $_check['code'];
                    self::$data['data']['code'] = $_check['code'];
                    self::$data['msg'] = code::$code[$_check['code']];
                    return self::$data;
                }
                if ($this->post['new_password'] !== $this->post['new_password_cfn']) {
                    self::$data['success'] = false;
                    self::$data['data']['error_msg'] = "errorPasswordDifferent";
                    self::$data['data']['code'] = "errorPasswordDifferent";
                    self::$data['msg'] = code::$code["errorPasswordDifferent"];
                    return self::$data;
                }
                $_data['password'] = md5($this->post['new_password']);
            }
            //姓名
            if (!empty($this->post['name'])) {
                $_data['name'] = $this->post['name'];
            }
            //头像
            if (!empty($this->post['photo'])) {
                $photo = $_data['photo'] = $this->post['photo'];
                //用户文件夹
                $md5Uid = md5($this->user_id);
                //制作绝对路径
                $path = $_SERVER['DOCUMENT_ROOT'] . init::$config['env']['user_path'] . "/" . $md5Uid;
                //遍历删除 不是.和..的文件
                foreach (scandir($path) as $filename) {
                    if ($filename == '.' || $filename == '..') {
                        continue;
                    }
                    if ($_SERVER['DOCUMENT_ROOT'] . $photo != $path . '/' . $filename) {
                        unlink($path . '/' . $filename);
                    }
                }
            }
            //手机号
            if (!empty($this->post['phone'])) {
                $check = $AuthDAL->checkPhone($this->post['phone'], $this->post['code']);
                if ($check !== true) {
                    self::$data['success'] = false;
                    self::$data['data']['code'] = $check;
                    self::$data['msg'] = code::$code[$check];
                    return self::$data;
                }
                $_data['phone'] = $this->post['phone'];
            }
            if (!empty($_data)) {
                $res = $AuthDAL->updateUserInfo($this->user_id, $_data);
                self::$data['data'] = $res;
            } else {
                self::$data['data'] = "noobj";
            }
            //print_r($res);die;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 提交用户图片 */
    function uploadPhoto() {
        try {
            $photo = $_FILES['photo'];
            LogDAL::save(date("Y-m-d H:i:s") . "-------------------------------------" . json_encode($_FILES) . "", "DEBUG");
            $path = init::$config['env']['user_path'] . '/' . md5($this->user_id);
            $name = date("YmdHis") . ".jpg";
            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . init::$config['env']['user_path'])) {
                mkdir($_SERVER['DOCUMENT_ROOT'] . init::$config['env']['user_path'], 0777);
            }
            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $path)) {
                mkdir($_SERVER['DOCUMENT_ROOT'] . $path, 0777);
            }
            $os = move_uploaded_file($photo['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $path . '/' . $name);
            LogDAL::save(date("Y-m-d H:i:s") . "-------------------------------------" . json_encode($os) . "", "DEBUG");
            self::$data['data'] = $path . '/' . $name;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 编辑 企业||用户 信息 */
    function updateInfo() {
        try {
            //轮播列表
            $res="";
            switch ($this->server_id) {
                case init::$config['token']['server_id']['customer']:
                    self::updateUsersInfo();
                    break;
                case init::$config['token']['server_id']['business']:
                    $res = EnterpriseDAL::getByUserId($this->user_id);
                    if(!empty($this->post['email'])){
                        $_userData["email"]=$this->post['email'];
                        UserDAL::update($this->user_id,$_userData);
                    }
                    $_enterpriseData="";
                    if(!empty($this->post['enterprise_name'])){
                        $_enterpriseData['name']=$this->post['enterprise_name'];
                    }
                    if(!empty($this->post['user_nane'])){
                        $_enterpriseData['username']=$this->post['user_nane'];
                    }
                    if(!empty($_enterpriseData)){
                        //var_dump($_enterpriseData);
                        cmsEnterpriseDAL::update($res['id'],$_enterpriseData);
                    }
                    $res=true;
                    break;
                case init::$config['token']['server_id']['management']:
                    break;
                default:
                    break;
            }
            self::$data['data']['userType'] = $this->server_id;
            //print_r($res);die;
            self::$data['data']['res'] = $res;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /*  get   * ******************************************************************************* */

    /** 企业||用户 信息 */
    function info() {
        try {
            //轮播列表
            $AuthDAL = new AuthDAL();
            $res="";
            switch ($this->server_id) {
                case init::$config['token']['server_id']['customer']:
                    $res = $AuthDAL->getUserInfo($this->user_id);
                    $res['subInfo']['joinCourse'] = AccountDAL::getCoursesTotal($this->user_id);
                    $res['subInfo']['passCourse'] = AccountDAL::getCoursesPass($this->user_id);
                    $res['subInfo']['failCourse'] = AccountDAL::getCoursesFailed($this->user_id);
                    break;
                case init::$config['token']['server_id']['business']:
                    $res = EnterpriseDAL::getByUserId($this->user_id);
                    $res['subInfo']['enterpriseUserCount'] = EnterpriseDAL::getEnterpriseUserCount($res['id']);
                    $res['subInfo']['joinCourseUserCount'] = EnterpriseDAL::getJoinCourseUserCount($res['id']);
                    $res['subInfo']['courseCount'] = CourseDAL::getEnterpriseCoursesTotal($res['id']);
                    break;
                case init::$config['token']['server_id']['management']:
                    break;
                default:
                    break;
            }
            self::$data['data']['userType'] = $this->server_id;
            //print_r($res);die;
            self::$data['data']['userInfo'] = $res;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 员工：参与过的课程列表 */
    function courses() {
        try {
            //轮播列表
            if ($this->server_id != init::$config['token']['server_id']['customer']) {
                self::$data['success'] = false;
                self::$data['data']['code'] = "errorType";
                self::$data['msg'] = code::$code["errorType"];
                return self::$data;
            }
            $currentPage = isset($this->get['currentPage']) ? $this->get['currentPage'] : 1;
            $pagesize = isset($this->get['pagesize']) ? $this->get['pagesize'] : init::$config['page_width'];
            $res = AccountDAL::getCourses($currentPage, $pagesize, $this->user_id);
            $resT = AccountDAL::getCoursesTotal($this->user_id);
            self::$data['data']['userType'] = $this->server_id;
            //print_r($res);die;
            self::$data['data']['list'] = $res;
            self::$data['data']['total'] = $resT;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 员工：收藏的文章列表 */
    function favorites() {
        try {
            //轮播列表
            if ($this->server_id != init::$config['token']['server_id']['customer']) {
                self::$data['success'] = false;
                self::$data['data']['code'] = "errorType";
                self::$data['msg'] = code::$code["errorType"];
                return self::$data;
            }
            $currentPage = isset($this->get['currentPage']) ? $this->get['currentPage'] : 1;
            $pagesize = isset($this->get['pagesize']) ? $this->get['pagesize'] : init::$config['page_width'];
            $res = AccountDAL::getFavorites($currentPage, $pagesize, $this->user_id);
            $resT = AccountDAL::getFavoritesTotal($this->user_id);

            self::$data['data']['userType'] = $this->server_id;
            //print_r($res);die;
            self::$data['data']['list'] = $res;
            self::$data['data']['total'] = $resT;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 员工：企业专用课程列表 */
    function enterpriseCourses() {
        $currentPage = isset($this->get['currentPage']) ? $this->get['currentPage'] : 1;
        $pagesize = isset($this->get['pagesize']) ? $this->get['pagesize'] : init::$config['page_width'];
        try {
            //轮播列表

            $res = CourseDAL::getEnterpriseCourse($currentPage, $pagesize, $this->user_id);
            $total = CourseDAL::getEnterpriseCourseTotal($this->user_id);

            //print_r($res);die;
            self::$data['data']['list'] = $res;
            self::$data['data']['total'] = $total;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 员工：简历 */
    function getResume() {
        try {
            //轮播列表
            if ($this->server_id != init::$config['token']['server_id']['customer']) {
                self::$data['success'] = false;
                self::$data['data']['code'] = "errorType";
                self::$data['msg'] = code::$code["errorType"];
                return self::$data;
            }
            $res = ResumeDAL::getOne($this->user_id);

            self::$data['data']['userType'] = $this->server_id;
            //print_r($res);die;
            self::$data['data']['info'] = $res;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 员工：获取已经投递过简历的职位列表 */
    function getResumedSupports(){
        try {
            //轮播列表
            if ($this->server_id != init::$config['token']['server_id']['customer']) {
                self::$data['success'] = false;
                self::$data['data']['code'] = "errorType";
                self::$data['msg'] = code::$code["errorType"];
                return self::$data;
            }
            $start_time=!empty($this->get['start_time'])?$this->get['start_time']:"";
            $end_time=!empty($this->get['end_time'])?$this->get['end_time']:"";
            $res = ResumeDAL::getResumeArticles($this->user_id,$start_time,$end_time);
            $total = ResumeDAL::getResumeArticlesTotal($this->user_id,$start_time,$end_time);

            self::$data['data']['userType'] = $this->server_id;
            //print_r($res);die;
            self::$data['data']['info'] = $res;
            self::$data['data']['total'] = !empty($total)?(int)$total:0;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 员工：获取已经投递过简历的职位数量 */
    function getResumedSupportsTotal(){
        try {
            //轮播列表
            if ($this->server_id != init::$config['token']['server_id']['customer']) {
                self::$data['success'] = false;
                self::$data['data']['code'] = "errorType";
                self::$data['msg'] = code::$code["errorType"];
                return self::$data;
            }
            $start_time=!empty($this->get['start_time'])?$this->get['start_time']:"";
            $end_time=!empty($this->get['end_time'])?$this->get['end_time']:"";
            $total = ResumeDAL::getResumeArticlesTotal($this->user_id,$start_time,$end_time);

            self::$data['data']['userType'] = $this->server_id;
            //print_r($res);die;
            self::$data['data']['total'] = !empty($total)?(int)$total:0;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 企业主：员工学习进度 */
    function personalProgresses() {
        $currentPage = isset($this->get['currentPage']) ? $this->get['currentPage'] : 1;
        $pagesize = isset($this->get['pagesize']) ? $this->get['pagesize'] : init::$config['page_width'];
        $enterprise_id = EnterpriseDAL::getByUserId($this->user_id)['id'];
        try {
            //轮播列表

            $res = EnterpriseDAL::getEnterpriseUserCourseExam($currentPage, $pagesize, $enterprise_id);
            $resT = EnterpriseDAL::getEnterpriseUserCount($enterprise_id);

            //print_r($res);die;
            self::$data['data']['list'] = $res;
            self::$data['data']['total'] = $resT;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 企业主：课程参与度 */
    function courseProgresses() {
        $currentPage = isset($this->get['currentPage']) ? $this->get['currentPage'] : 1;
        $pagesize = isset($this->get['pagesize']) ? $this->get['pagesize'] : init::$config['page_width'];
        $enterprise_id = EnterpriseDAL::getByUserId($this->user_id)['id'];
        try {
            //轮播列表

            $res = EnterpriseDAL::getEnterpriseUserCourseProgresses($currentPage, $pagesize, $enterprise_id);
            $resT = CourseDAL::getEnterpriseCoursesTotal( $enterprise_id);

            //print_r($res);die;
            self::$data['data']['list'] = $res;
            self::$data['data']['total'] = $resT;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 企业主：考试合格率 */
    function testProgresses() {
        $currentPage = isset($this->get['currentPage']) ? $this->get['currentPage'] : 1;
        $pagesize = isset($this->get['pagesize']) ? $this->get['pagesize'] : init::$config['page_width'];
        $enterprise_id = EnterpriseDAL::getByUserId($this->user_id)['id'];
        try {
            //轮播列表

            $res = EnterpriseDAL::getEnterpriseUserExamPass($currentPage, $pagesize, $enterprise_id);
            $resT = CourseDAL::getEnterpriseCoursesTotal($enterprise_id);

            //print_r($res);die;
            self::$data['data']['list'] = $res;
            self::$data['data']['total'] = $resT;
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 企业主：已投递的简历列表 */
    function getResumedList() {
        $currentPage = isset($this->get['currentPage']) ? $this->get['currentPage'] : 1;
        $pagesize = isset($this->get['pagesize']) ? $this->get['pagesize'] : init::$config['page_width'];
        $enterprise_id = EnterpriseDAL::getByUserId($this->user_id)['id'];
        try {
            //轮播列表

            self::$data['data']['list'] = cmsUserInfoDAL::getAll($currentPage, $pagesize, '', $enterprise_id);
            self::$data['data']['total'] = cmsUserInfoDAL::getTotal('', $enterprise_id);

        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }
    /** 企业主：已投递的简历 */
    function getResumed() {
        $user_id = $this->get['user_id'];
        try {
            self::$data['data'] = cmsUserResumeArticleDAL::getOne($user_id);
        } catch (Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

}
