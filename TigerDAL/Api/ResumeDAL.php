<?php

namespace TigerDAL\Api;

use TigerDAL\BaseDAL;

class ResumeDAL {

    /** 获取用户信息 */
    public static function getOne($user_id) {
        $base = new BaseDAL();
        $sql = "select r.* from " . $base->table_name("user_resume") . " as r "
                . "where r.`delete`=0 and r.user_id=" . $user_id . "  ;";
        $res = $base->getFetchRow($sql);
        if (!empty($res)) {
            $sql = "select r.* from " . $base->table_name("user_resume_school") . " as r "
                    . "where r.`delete`=0 and r.user_id=" . $user_id . " and r.user_resume_id=" . $res['id'] . " ;";
            $res['school'] = $base->getFetchAll($sql);

            $sql = "select r.* from " . $base->table_name("user_resume_company") . " as r "
                    . "where r.`delete`=0 and r.user_id=" . $user_id . " and r.user_resume_id=" . $res['id'] . " ;";
            $res['company'] = $base->getFetchAll($sql);

            $sql = "select r.* from " . $base->table_name("user_resume_project") . " as r "
                    . "where r.`delete`=0 and r.user_id=" . $user_id . " and r.user_resume_id=" . $res['id'] . " ;";
            $res['project'] = $base->getFetchAll($sql);
        }
        return $res;
    }

    /** 写入简历信息 */
    public static function saveOne($_data) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("user_resume") . " as r where r.user_id=" . $_data['user_id'] . " ;";
        //echo $sql;
        $row = $base->getFetchRow($sql);
        if (!empty($row)) {
            $base->update($row['id'], $_data, "user_resume");
            $resume_id = $row['id'];
        } else {
            $base->insert($_data, "user_resume");
            $resume_id = $base->last_insert_id();
        }
        return $resume_id;
    }

    /** 写入简历信息 */
    public static function saveOneSchool($_school) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("user_resume_school") . " as r where r.user_id=" . $_school['user_id'] . " and r.id=" . $_school['id'] . " ;";
        $row = $base->getFetchRow($sql);
        if (!empty($row)) {
            $base->update($row['id'], $_school, "user_resume_school");
            $resume_id = $row['id'];
        } else {
            unset($_school['id']);
            $base->insert($_school, "user_resume_school");
            $resume_id = $base->last_insert_id();
        }
        return $resume_id;
    }

    /** 写入简历信息 */
    public static function saveOneCompany($_company) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("user_resume_company") . " as r where r.user_id=" . $_company['user_id'] . " and r.id=" . $_company['id'] . " ;";
        $row = $base->getFetchRow($sql);
        if (!empty($row)) {
            $base->update($row['id'], $_company, "user_resume_company");
            $resume_id = $row['id'];
        } else {
            unset($_company['id']);
            $base->insert($_company, "user_resume_company");
            $resume_id = $base->last_insert_id();
        }
        return $resume_id;
    }

    /** 写入简历信息 */
    public static function saveOneProject($_project) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("user_resume_project") . " as r where r.user_id=" . $_project['user_id'] . " and r.id=" . $_project['id'] . " ;";
        $row = $base->getFetchRow($sql);
        if (!empty($row)) {
            $base->update($row['id'], $_project, "user_resume_project");
            $resume_id = $row['id'];
        } else {
            unset($_project['id']);
            $base->insert($_project, "user_resume_project");
            $resume_id = $base->last_insert_id();
        }
        return $resume_id;
    }

    /** 投递简历 */
    public static function sendResume($user_id, $article_id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name('user_resume_article') . " where user_id=" . $user_id . " and article_id=" . $article_id . " ;";
        $row = $base->getFetchRow($sql);
        if (empty($row)) {
            $resume = self::getOne($user_id);
            $_data = [
                'user_id' => $user_id,
                'user_resume_id' => $resume['id'],
                'article_id' => $article_id,
                'add_time' => date("Y-m-d H:i:s"),
                'edit_time' => date("Y-m-d H:i:s"),
                'delete' => 0,
            ];
            $base->insert($_data, 'user_resume_article');
        } else {
            if ($row['delete'] == 0) {
                $_data = ['delete' => '1'];
            } else {
                $_data = ['delete' => '0'];
            }
            $base->update($row['id'], $_data, 'user_resume_article');
        }
        return true;
    }

    /** 投递的状态 */
    public static function getResumeArticle($user_id, $article_id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("user_resume_article") . "  "
                . "where user_id=" . $user_id . " and article_id=" . $article_id . " ;";
        return $base->getFetchRow($sql);
    }

}
