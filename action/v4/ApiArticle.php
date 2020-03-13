<?php

namespace action\v4;

use mod\common as Common;
use TigerDAL\Api\TokenDAL;
use TigerDAL\Api\ArticleDAL;
use TigerDAL\Api\AccountDAL;
use TigerDAL\Api\ResumeDAL;;
use TigerDAL\Api\ExaminationDAL;
use TigerDAL\Api\ExamDAL;
use TigerDAL\Cms\ArticleDAL as cmsArticleDAL;
use config\code;

class ApiArticle extends \action\RestfulApi {

    public $user_id;
    public $server_id;

    /**
     * 主方法引入父类的基类
     * 责任是分担路由的工作
     */
    function __construct() {
        $path = parent::__construct();
        // 校验token
        $TokenDAL = new TokenDAL();
        $_token = $TokenDAL->checkToken();
        //Common::pr($_token);die;
        if ($_token['code'] != 90001) {
            $this->user_id = "";
            $this->server_id = "";
        } else {
            $this->user_id = $_token['data']['user_id'];
            $this->server_id = $_token['data']['server_id'];
        }
        if (!empty($path)) {
            $_path = explode("-", $path);
            $mod= $_path['2'];
            $res=$this->$mod();
            exit(json_encode($res));
        }
    }

    /** 职位 列表 */
    function supports() {
        $currentPage = isset($this->get['currentPage']) ? $this->get['currentPage'] : 1;
        $pagesize = isset($this->get['pagesize']) ? $this->get['pagesize'] : \mod\init::$config['page_width'];
        $keywords = isset($this->get['keywords']) ? $this->get['keywords'] : "";
        $city = isset($this->get['city']) ? $this->get['city'] : '';
        $type = isset($this->get['type']) ? $this->get['type'] : '';
        $salary = isset($this->get['salary']) ? $this->get['salary'] : '';
        $age = isset($this->get['age']) ? $this->get['age'] : '';
        $enterprise_id = isset($this->get['enterprise_id']) ? $this->get['enterprise_id'] : '';
        try {
            //轮播列表
            $res = ArticleDAL::getAll($currentPage, $pagesize, $keywords, $city, $type,$salary,$age,$enterprise_id);
            $total = ArticleDAL::getTotal($keywords, $city, $type,$salary,$age,$enterprise_id);

            if (!empty($res)) {
                foreach ($res as $k => $v) {
                    $_row = ResumeDAL::getResumeArticle($this->user_id, $v['id']);
                    $res[$k]['resume_article'] = (!empty($_row)) ? ($_row['delete'] == 0) ? 1 : 0 : 0;
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

    /** 职位 信息 */
    function support() {
        try {
            //轮播列表
            $res = ArticleDAL::getOne($this->get['id']);
            self::$data['data'] = $res;
            if($this->server_id==\mod\init::$config['token']['server_id']['customer']){
                $resRA = ResumeDAL::getResumeArticle($this->user_id, $this->get['id']);
                self::$data['data']['resume_article'] = (!empty($resRA)) ? ($resRA['delete'] == 0) ? 1 : 0 : 0;
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 编辑职位信息 */
    function updateSupport() {
        $id = isset($this->get['id']) ? $this->get['id'] : null;
        try {
            $media_id = 0;
            if ($id != null) {
                /** 更新操作 */
                $data = [
                    'name' => $this->post['name'],
                    'overview' => isset($this->post['overview']) ? $this->post['overview'] : '',
                    'detail' => isset($this->post['detail']) ? $this->post['detail'] : '',
                    'access' => isset($this->post['access']) ? $this->post['access'] : 0,
                    'source' => isset($this->post['source']) ? $this->post['source'] : '',
                    'edit_by' => $this->user_id,
                    'type' => isset($this->post['type']) ? $this->post['type'] : '',
                    'salary' => isset($this->post['salary']) ? $this->post['salary'] : '',
                    'province' => isset($this->post['province']) ? $this->post['province'] : '',
                    'city' => isset($this->post['city']) ? $this->post['city'] : '',
                    'district' => isset($this->post['district']) ? $this->post['district'] : '',
                    'address' => isset($this->post['address']) ? $this->post['address'] : '',
                    'age_min' => isset($this->post['age_min']) ? $this->post['age_min'] : '',
                    'age_max' => isset($this->post['age_max']) ? $this->post['age_max'] : '',
                    'education' => isset($this->post['education']) ? $this->post['education'] : '',
                    'tag' => isset($this->post['tag']) ? $this->post['tag'] : '',
                    'responsibilities' => isset($this->post['responsibilities']) ? $this->post['responsibilities'] : '',
                    'qualifications' => isset($this->post['qualifications']) ? $this->post['qualifications'] : '',
                ];
                self::$data['data'] = cmsArticleDAL::update($id, $data);
            } else {
                /** 新增操作 */
                $data = [
                    'name' => $this->post['name'],
                    'overview' => isset($this->post['overview']) ? $this->post['overview'] : '',
                    'detail' => isset($this->post['detail']) ? $this->post['detail'] : '',
                    'cat_id' => isset($this->post['cat_id']) ? $this->post['cat_id'] : 0,
                    'order_by' => 50,
                    'add_by' => $this->user_id,
                    'add_time' => date("Y-m-d H:i:s"),
                    'edit_by' => $this->user_id,
                    'edit_time' => date("Y-m-d H:i:s"),
                    'delete' => 0,
                    'access' => isset($this->post['access']) ? $this->post['access'] : 0,
                    'source' => isset($this->post['source']) ? $this->post['source'] : '',
                    'media_id' => $media_id,
                    'type' => isset($this->post['type']) ? $this->post['type'] : '',
                    'salary' => isset($this->post['salary']) ? $this->post['salary'] : '',
                    'province' => isset($this->post['province']) ? $this->post['province'] : '',
                    'city' => isset($this->post['city']) ? $this->post['city'] : '',
                    'district' => isset($this->post['district']) ? $this->post['district'] : '',
                    'address' => isset($this->post['address']) ? $this->post['address'] : '',
                    'age_min' => isset($this->post['age_min']) ? $this->post['age_min'] : '',
                    'age_max' => isset($this->post['age_max']) ? $this->post['age_max'] : '',
                    'education' => isset($this->post['education']) ? $this->post['education'] : '',
                    'tag' => isset($this->post['tag']) ? $this->post['tag'] : '',
                    'responsibilities' => isset($this->post['responsibilities']) ? $this->post['responsibilities'] : '',
                    'qualifications' => isset($this->post['qualifications']) ? $this->post['qualifications'] : '',
                    'enterprise_id' => !empty($this->post['enterprise_id']) ? $this->post['enterprise_id'] : 0,
                    'examination_id' => !empty($this->post['examination_id']) ? $this->post['examination_id'] : 0,
                ];
                self::$data['data'] = cmsArticleDAL::insert($data);
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 编辑职位信息 */
    function deleteSupport() {
        $id = isset($this->get['id']) ? $this->get['id'] : null;
        try {
            if ($id != null) {
                self::$data['data'] = cmsArticleDAL::delete($id);
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }
    
}
