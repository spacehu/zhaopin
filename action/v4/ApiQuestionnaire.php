<?php

namespace action\v4;

use mod\common as Common;
use TigerDAL\Api\CourseDAL;
use TigerDAL\Api\LessonDAL;
use TigerDAL\Cms\LessonImageDAL;
use TigerDAL\Api\TestDAL;
use TigerDAL\Api\QuestionnaireDAL;
use TigerDAL\Api\AccountDAL;
use config\code;

class ApiQuestionnaire extends \action\RestfulApi {

    public $openid;
    public $server_id;

    /**
     * 主方法引入父类的基类
     * 责任是分担路由的工作
     */
    function __construct() {
        $path = parent::__construct();
        // 校验openid和eCode
        $this->openid = "";
        $this->server_id = "";
        if (!empty($path)) {
            $_path = explode("-", $path);
            $mod= $_path['2'];
            $res=$this->$mod();
            exit(json_encode($res));
        }
    }


    /** 
     * 试卷info
     * 获取问卷接口 校验是否参与过问卷 条件需要确认是以签到还是答卷
     *
     */
    function questionnaire() {
        // 校验 是否参与过问卷 条件需要确认是已经签到还是参与答卷
        if (empty($this->get['questionnaire_id'])) {
            self::$data['success'] = false;
            self::$data['data']['error_msg'] = 'emptyparameter';
            self::$data['msg'] = code::$code['emptyparameter'];
            return self::$data;
        }
        // 
        $signed="是否签到";
        $signed_q="是否参与了问卷";

        try {
            // 获取问卷详细 和 问卷的试题
            $_obj = QuestionnaireDAL::getOne($this->get['questionnaire_id']);
            $res = TestDAL::getQuestionnaire($this->get['questionnaire_id']);
            //print_r($res);die;
            self::$data['data']['info'] = $_obj;
            self::$data['data']['list'] = $res;
            self::$data['data']['total'] = count($res);
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

}
