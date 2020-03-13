<?php

namespace action;

use TigerDAL;
use TigerDAL\Cms\EnterpriseDAL;
use TigerDAL\Cms\StatisticsDAL;
use config\code;
use mod\common as Common;
use mod\csv as Csv;

use TigerDAL\Api\EnterpriseDAL as apiEnterpriseDAL;
use TigerDAL\Api\CourseDAL as apiCourseDAL;

class statistics {

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
            TigerDAL\CatchDAL::markError(code::$code[code::CATEGORY_INDEX], code::CATEGORY_INDEX, json_encode($ex));
        }
    }

    function staticPage() {
        Common::isset_cookie();

        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    /**
     * index function
     * get type
     * get startTime
     * get endTime
     * get action
     * get page
     * 
     * return 
     * $data['data']['pv']
     * $data['data']['uv']
     */
    function index() {
        Common::isset_cookie();
        try {
            $type = $_GET['type'];
            $_startTime = isset($_GET['startTime']) ? $_GET['startTime'] : date("Y-m-d", time());
            self::$data['startTime'] = $_startTime;
            $_endTime = isset($_GET['endTime']) ? $_GET['endTime'] : date("Y-m-d", strtotime("+1 day"));
            self::$data['endTime'] = $_endTime;
            if ($type == 'visit') {
                self::$data['data']['pv'] = StatisticsDAL::getPageView($_startTime, $_endTime);
                self::$data['data']['iv'] = StatisticsDAL::getIPView($_startTime, $_endTime);
                self::$data['data']['uv'] = StatisticsDAL::getUserView($_startTime, $_endTime);
            } else if ($type == 'action') {
                $_action = isset($_GET['action']) ? $_GET['action'] : 'index';
                self::$data['action'] = $_action;
                self::$data['actionList'] = \mod\init::$config['actionList'];
                self::$data['data']['pv'] = StatisticsDAL::getPageView($_startTime, $_endTime, $_action);
                self::$data['data']['iv'] = StatisticsDAL::getIPView($_startTime, $_endTime, $_action);
                self::$data['data']['uv'] = StatisticsDAL::getUserView($_startTime, $_endTime, $_action);
            } else if ($type == 'page') {
                $_url = isset($_GET['page']) ? $_GET['page'] : 'https://www.plbs.com';
                self::$data['page'] = $_url;
                self::$data['data']['pv'] = StatisticsDAL::getPageView($_startTime, $_endTime, '', $_url);
                self::$data['data']['iv'] = StatisticsDAL::getIPView($_startTime, $_endTime, '', $_url);
                self::$data['data']['uv'] = StatisticsDAL::getUserView($_startTime, $_endTime, '', $_url);
            }
            //Common::pr(self::$data);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::STATISTICS_INDEX], code::STATISTICS_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__ . '_' . $type);
    }

    /**
     * getBonus function
     * get startTime
     * get endTime
     * 
     * return
     * $data['data']
     */
    function getBonus() {
        Common::isset_cookie();
        try {
            $_source = isset($_GET['source']) ? $_GET['source'] : "thanksgiving";
            self::$data['source'] = $_source;
            $_startTime = isset($_GET['startTime']) ? $_GET['startTime'] : date("Y-m-d", time());
            //$_startTime = isset($_GET['startTime']) ? $_GET['startTime'] : "2017-01-01";
            self::$data['startTime'] = $_startTime;
            $_endTime = isset($_GET['endTime']) ? $_GET['endTime'] : date("Y-m-d", strtotime("+1 day"));
            self::$data['endTime'] = $_endTime;

            $currentPage = isset($_GET['currentPage']) ? $_GET['currentPage'] : 1;
            $pagesize = isset($_GET['pagesize']) ? $_GET['pagesize'] : \mod\init::$config['page_width'];
            self::$data['currentPage'] = $currentPage;
            self::$data['pagesize'] = $pagesize;

            self::$data['data'] = StatisticsDAL::getBonus($currentPage, $pagesize, $_startTime, $_endTime, $_source);
            self::$data['total'] = StatisticsDAL::getBonusTotal($_startTime, $_endTime, $_source);
            self::$data['sources'] = StatisticsDAL::getSource();

        // Common::pr(self::$data);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::STATISTICS_INDEX], code::STATISTICS_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    /**
     * getUser function 
     * 
     * 
     * 
     */
    function getStatisticsUser() {
        Common::isset_cookie();
        try {
            self::$data['data']['sex'] = StatisticsDAL::getSex();
            self::$data['data']['age'] = StatisticsDAL::getAge();
            self::$data['data']['region'] = StatisticsDAL::getRegion();

            // Common::pr(self::$data);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::STATISTICS_INDEX], code::STATISTICS_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    /** 成员在线学习 */
    function customerList() {
        Common::isset_cookie();
        try {
            if ($this->enterprise_id == '') {
                Common::js_alert_redir("您不是企业管理员无法查看企业统计数据", ERROR_405);
                exit;
            }
            $currentPage = isset($_GET['currentPage']) ? $_GET['currentPage'] : 1;
            $pagesize = isset($_GET['pagesize']) ? $_GET['pagesize'] : \mod\init::$config['page_width'];
            $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : "";
            $_startTime = isset($_GET['startTime']) ? $_GET['startTime'] : date("Y-m-d", time());
            $_endTime = isset($_GET['endTime']) ? $_GET['endTime'] : date("Y-m-d", strtotime("+1 day"));

            $data = apiEnterpriseDAL::getEnterpriseUserCourseExam($currentPage, $pagesize,  $this->enterprise_id, $keywords,$_startTime, $_endTime);
            self::$data['data'] = $data;
            $total=apiEnterpriseDAL::getEnterpriseUserCount($this->enterprise_id, $keywords,$_startTime, $_endTime);
            self::$data['total'] = $total;

            self::$data['currentPage'] = $currentPage;
            self::$data['pagesize'] = $pagesize;
            self::$data['keywords'] = $keywords;
            self::$data['endTime'] = $_endTime;
            self::$data['startTime'] = $_startTime;
            self::$data['class'] = $this->class;

            if(!empty($_GET['export'])&&$_GET['export']==2){
                $headlist=[
                    "姓名",
                    "部门",
                    "职位",
                    "企业必修课程数",
                    "学习进度",
                    "总学习时间",
                    "通过考试数",
                    "参与课程数",
                ];
                $_data=[];
                if(!empty($data)){
                    foreach($data as $k=>$v){
                        $_data[]=[
                            'name'=>$v['NAME'],
                            'edname'=>$v['edname'],
                            'epname'=>$v['epname'],
                            'enterpriseCourseCount'=>$v['enterpriseCourseCount'],
                            'progress'=>$v['progress'],
                            'hours'=>$v['hours'],
                            'passExamCount'=>$v['passExamCount'],
                            'joinCourseCount'=>$v['joinCourseCount'],
                        ];
                    }
                }
                $csv=new Csv();
                $csv->mkcsv($_data,$headlist,"customerList-".date("YmdHis"));
                exit();
            }
            //Common::pr(self::$data);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::STATISTICS_INDEX], code::STATISTICS_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    /** 成员在线学习 详细页 */
    function getStatisticsCustomer(){
        Common::isset_cookie();
        try {
            if ($this->enterprise_id == '') {
                Common::js_alert_redir("您不是企业管理员无法查看企业统计数据", ERROR_405);
                exit;
            }
            $id = $_GET['id'];

            // 获取用户独立信息：工号 姓名 部门 职位 联系方式（电话） 必修课数 选修课程数 学习进度 总学习时长（暂无） 考试通过数
            $data['info']=StatisticsDAL::getCustomerInfo($id);
            // Common::pr($data);die;
            // 获取用户课程列表信息：名称 是否企业必修课 学习进度 考试通过
            $data['courseList']=StatisticsDAL::getCustomerCourseList($id);
            self::$data['data'] = $data;
            self::$data['class'] = $this->class;

            if(!empty($_GET['export'])&&$_GET['export']==2){
                $headlist=[
                    [
                        "姓名",
                        "部门",
                        "职位",
                        "联系方式",
                        "企业必修课程数",
                        "学习进度",
                        "总学习时间",
                        "通过考试数",
                        "参与课程数",
                    ],
                    [
                        $data['info']['name'],
                        $data['info']['edname'],
                        $data['info']['epname'],
                        $data['info']['phone'],
                        $data['info']['enterpriseCourseCount'],
                        $data['info']['progress'],
                        $data['info']['hours'],
                        $data['info']['passExamCount'],
                        $data['info']['joinCourseCount'],
                    ],
                    [
                        "课程",
                        "类别",
                        "学习进度",
                        "考试通过",
                    ],
                ];
                $_data=[];
                if(!empty($data['courseList'])){
                    foreach($data['courseList'] as $k=>$v){
                        $_data[]=[
                            $v['name'],
                            !empty($v['eccid'])?"企业必修课程":"选修课",
                            $v['progress'],
                            !empty($v['passExamCount'])?"通过考试":"",
                        ];
                    }
                }
                $csv=new Csv();
                $csv->mkcsvMore($_data,$headlist,"getCustomer-".$id."-".$data['info']['name']."-".date("YmdHis"));
                exit();
            }
            //Common::pr(self::$data);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::STATISTICS_INDEX], code::STATISTICS_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }
    /** 员工信息维护 */
    function userList() {
        Common::isset_cookie();
        try {
            if ($this->enterprise_id == '') {
                Common::js_alert_redir("您不是企业管理员无法查看企业统计数据", ERROR_405);
                exit;
            }
            $currentPage = isset($_GET['currentPage']) ? $_GET['currentPage'] : 1;
            $pagesize = isset($_GET['pagesize']) ? $_GET['pagesize'] : \mod\init::$config['page_width'];
            $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : "";

            $data = StatisticsDAL::getUserList($currentPage, $pagesize, $keywords, $this->enterprise_id);
            self::$data['data'] = $data['data'];
            self::$data['total'] = $data['total'];

            self::$data['currentPage'] = $currentPage;
            self::$data['pagesize'] = $pagesize;
            self::$data['keywords'] = $keywords;
            self::$data['class'] = $this->class;
            //Common::pr(self::$data);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::STATISTICS_INDEX], code::STATISTICS_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    /** 在线课程学习 */
    function courseList() {
        Common::isset_cookie();
        try {
            if ($this->enterprise_id == '') {
                Common::js_alert_redir("您不是企业管理员无法查看企业统计数据", ERROR_405);
                exit;
            }
            $currentPage = isset($_GET['currentPage']) ? $_GET['currentPage'] : 1;
            $pagesize = isset($_GET['pagesize']) ? $_GET['pagesize'] : \mod\init::$config['page_width'];
            $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : "";
            $_startTime = isset($_GET['startTime']) ? $_GET['startTime'] : date("Y-m-d", time());
            $_endTime = isset($_GET['endTime']) ? $_GET['endTime'] : date("Y-m-d", strtotime("+1 day"));


            $data = apiEnterpriseDAL::getEnterpriseUserCourseProgresses($currentPage, $pagesize, $this->enterprise_id);
            self::$data['data'] = $data;
            $total=apiCourseDAL::getEnterpriseCoursesTotal($this->enterprise_id);
            self::$data['total'] = $total;

            self::$data['currentPage'] = $currentPage;
            self::$data['pagesize'] = $pagesize;
            self::$data['keywords'] = $keywords;
            self::$data['endTime'] = $_endTime;
            self::$data['startTime'] = $_startTime;
            self::$data['class'] = $this->class;

            if(!empty($_GET['export'])&&$_GET['export']==2){
                $headlist=[
                    "课程名称",
                    "参与人数",
                    "学习进度",
                    "考试通过率",
                ];
                $_data=[];
                if(!empty($data)){
                    foreach($data as $k=>$v){
                        $_data[]=[
                            $v['name'],
                            $v['joinPerson'],
                            $v['progressLesson']*100,
                            $v['progressExam']*100,
                        ];
                    }
                }
                $csv=new Csv();
                $csv->mkcsv($_data,$headlist,"courseList-".date("YmdHis"));
                exit();
            }

            //Common::pr(self::$data);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::STATISTICS_INDEX], code::STATISTICS_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    /** 在线课程学习 详细页 */
    function getStatisticsCourse(){
        Common::isset_cookie();
        try {
            if ($this->enterprise_id == '') {
                Common::js_alert_redir("您不是企业管理员无法查看企业统计数据", ERROR_405);
                exit;
            }
            $id = $_GET['id'];

            // 获取用户独立信息：工号 姓名 部门 职位 联系方式（电话） 必修课数 选修课程数 学习进度 总学习时长（暂无） 考试通过数
            $data['info']=StatisticsDAL::getCourseInfo($id);
            // Common::pr($data);die;
            // 获取用户课程列表信息：名称 是否企业必修课 学习进度 考试通过
            $data['courseList']=StatisticsDAL::getCourseCustomerList($id);
            self::$data['data'] = $data;
            self::$data['class'] = $this->class;

            if(!empty($_GET['export'])&&$_GET['export']==2){
                $headlist=[
                    [
                        "课程名称",
                        "参与人数",
                        "学习进度",
                        "考试通过率",
                    ],
                    [
                        $data['info']['name'],
                        $data['info']['joinPerson'],
                        $data['info']['progressLesson']*100,
                        $data['info']['progressExam']*100,
                    ],
                    [
                        "姓名",
                        "部门",
                        "职位",
                        "学习进度",
                        "考试通过",
                    ],
                ];
                $_data=[];
                if(!empty($data['courseList'])){
                    foreach($data['courseList'] as $k=>$v){
                        $_data[]=[
                            $v['name'],
                            $v['edname'],
                            $v['epname'],
                            $v['progressLesson']*100,
                            !empty($v['totalE'])?"YES":"NO",
                        ];
                    }
                }
                $csv=new Csv();
                $csv->mkcsvMore($_data,$headlist,"getCourse-".$id."-".$data['info']['name']."-".date("YmdHis"));
                exit();
            }
            //Common::pr(self::$data);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::STATISTICS_INDEX], code::STATISTICS_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }

    /** getCustomerList */
    function examinationList(){
        Common::isset_cookie();
        try {
            if ($this->enterprise_id == '') {
                Common::js_alert_redir("您不是企业管理员无法查看企业统计数据", ERROR_405);
                exit;
            }
            $currentPage = isset($_GET['currentPage']) ? $_GET['currentPage'] : 1;
            $pagesize = isset($_GET['pagesize']) ? $_GET['pagesize'] : \mod\init::$config['page_width'];
            $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : "";


            $data = StatisticsDAL::getExaminationList($currentPage, $pagesize, $this->enterprise_id);
            self::$data['data'] = $data;
            $total=StatisticsDAL::getExaminationListTotal($this->enterprise_id);
            self::$data['total'] = $total;
            //Common::pr(self::$data);die;
            self::$data['currentPage'] = $currentPage;
            self::$data['pagesize'] = $pagesize;
            self::$data['keywords'] = $keywords;
            self::$data['class'] = $this->class;

            if(!empty($_GET['export'])&&$_GET['export']==2){
                $headlist=[
                    "试卷名",
                    "通过人数",
                    "参与人数",
                    "考试通过率（人）",
                    "通过次数",
                    "参与次数",
                    "考试通过率（人）",
                ];
                $_data=[];
                if(!empty($data)){
                    foreach($data as $k=>$v){
                        $_data[]=[
                            $v['name'],
                            $v['totalEuPass'],
                            $v['totalEu'],
                            $v['totalEu']>0?($v['totalEuPass']/$v['totalEu'])*100:0,
                            $v['totalExPass'],
                            $v['totalEx'],
                            $v['totalEx']>0?($v['totalExPass']/$v['totalEx'])*100:0,
                        ];
                    }
                }
                $csv=new Csv();
                $csv->mkcsv($_data,$headlist,"examinationList-".date("YmdHis"));
                exit();
            }

            //Common::pr(self::$data);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::STATISTICS_INDEX], code::STATISTICS_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }
    
    /** getCustomerList */
    function getExamination(){
        Common::isset_cookie();
        try {
            if ($this->enterprise_id == '') {
                Common::js_alert_redir("您不是企业管理员无法查看企业统计数据", ERROR_405);
                exit;
            }
            $id=$_GET['id'];
            $data = StatisticsDAL::getExaminationOne($id);
            self::$data['data'] = $data;
            
            self::$data['class'] = $this->class;

            if(!empty($_GET['export'])&&$_GET['export']==2){
                $headlist=[
                    "学员名",
                    "得分",
                    "是否通过",
                    "考试时间",
                ];
                $_data=[];
                if(!empty($data)){
                    foreach($data as $k=>$v){
                        $_data[]=[
                            $v['uname'],
                            $v['point'],
                            $v['pass']==1?"通过":"未通过",
                            $v['add_time'],
                        ];
                    }
                }
                $csv=new Csv();
                $csv->mkcsv($_data,$headlist,"getExamination-id-".$id."-".date("YmdHis"));
                exit();
            }

            //Common::pr(self::$data);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::STATISTICS_INDEX], code::STATISTICS_INDEX, json_encode($ex));
        }
        \mod\init::getTemplate('admin', $this->class . '_' . __FUNCTION__);
    }
}
