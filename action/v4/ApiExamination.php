<?php

namespace action\v4;

use mod\common as Common;
use TigerDAL\Api\TokenDAL;
use TigerDAL\Api\CourseDAL;
use TigerDAL\Api\LessonDAL;
use TigerDAL\Cms\LessonImageDAL;
use TigerDAL\Api\TestDAL;
use TigerDAL\Api\ExaminationDAL;
use TigerDAL\Api\AccountDAL;
use config\code;

class ApiExamination extends \action\RestfulApi {

    public $user_id;
    public $server_id;

    /**
     * 主方法引入父类的基类
     * 责任是分担路由的工作
     */
    function __construct() {
        $path = parent::__construct();
        $this->post = Common::exchangePost();
        $this->get = Common::exchangeGet();
        $this->header = Common::exchangeHeader();
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
            $actEval = "\$res = \$this ->" . $_path['2'] . "();";
            eval($actEval);
            exit(json_encode($res));
        }
    }

    /** 试卷list */
    function examinations() {
        $currentPage = isset($this->get['currentPage']) ? $this->get['currentPage'] : 1;
        $pagesize = isset($this->get['pagesize']) ? $this->get['pagesize'] : \mod\init::$config['page_width'];
        $keywords = isset($this->get['keywords']) ? $this->get['keywords'] : "";
        try {
            $enterprise_id = AccountDAL::getEnterpriseUser($this->user_id)['enterprise_id'];
            $res = ExaminationDAL::getAll($currentPage, $pagesize, $keywords, $enterprise_id);
            $total = ExaminationDAL::getTotal($keywords, $enterprise_id);
            if ($total > 0) {
                $eids = AccountDAL::getExamListByExamination($this->user_id);
                if (!empty($eids)) {
                    foreach ($eids as $k => $v) {
                        $_rows[$v['examination_id']] = ["percentage" => $v['percentage'], "maxPoint" => $v['maxPoint']];
                    }
                    foreach ($res as $k => $v) {
                        if (!empty($_rows[$v['id']])) {
                            if ((int) $_rows[$v['id']]["maxPoint"] > (int) $v['percentage']) {
                                $res[$k]['passStatus'] = "1";
                            } else {
                                $res[$k]['passStatus'] = "2";
                            }
                            $res[$k]['maxPoint'] = (int) $_rows[$v['id']]["maxPoint"];
                        }
                    }
                }
            }
            //print_r($res);die;
            self::$data['data']['list'] = $res;
            self::$data['data']['total'] = $total;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 试卷info */
    function examination() {
        if (empty($this->get['examination_id'])) {
            self::$data['success'] = false;
            self::$data['data']['error_msg'] = 'emptyparameter';
            self::$data['msg'] = code::$code['emptyparameter'];
            return self::$data;
        }
        try {
            self::$data['data']['info'] = ExaminationDAL::getOne($this->get['examination_id']);
            $_obj = self::$data['data']['info'];
            if (self::$data['data']['info']['type'] == "random") {
                $res = TestDAL::getRandExamination($this->get['examination_id'], $_obj['export_count']);
            } else if (self::$data['data']['info']['type'] == "regularize") {
                $res = TestDAL::getRandExamination($this->get['examination_id'], $_obj['total']);
            }
            //print_r($res);die;
            self::$data['data']['list'] = $res;
            self::$data['data']['total'] = count($res);
            self::$data['data']['max'] = $_obj['export_count'];
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

}
