<?php

namespace TigerDAL\Cms;

use TigerDAL\BaseDAL;

class UserResumeArticleDAL {

    public static function getAll($currentPage, $pagesize, $article_id) {
        $base = new BaseDAL();
        $limit_start = ($currentPage - 1) * $pagesize;
        $limit_end = $pagesize;
        $sql = "select r.*,ura.id as ura_id from " . $base->table_name("user_resume") . " as r "
                . "left join " . $base->table_name("user_resume_article") . " as ura on ura.user_resume_id=r.id "
                . "where r.`delete`=0 and ura.`delete`=0 and ura.article_id=" . $article_id . " "
                . "order by r.edit_time desc limit " . $limit_start . "," . $limit_end . " ;";
        $res = $base->getFetchAll($sql);
        return $res;
    }

    /** 获取数量 */
    public static function getTotal($article_id) {
        $base = new BaseDAL();
        $sql = "select count(1) as total "
                . "from " . $base->table_name("user_resume") . " as r "
                . "left join " . $base->table_name("user_resume_article") . " as ura on ura.user_resume_id=r.id "
                . "where r.`delete`=0 and ura.`delete`=0 and ura.article_id=" . $article_id . " "
                . "limit 1 ;";
        return $base->getFetchRow($sql)['total'];
    }

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

    /** 投递简历 */
    public static function sendResume($user_id, $article_id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name('user_resume_article') . " where user_id=" . $user_id . " and article_id=" . $article_id . " ;";
        $row = $base->getFetchRow($sql);
        if (empty($row)) {
            $resume=self::getOne($user_id);
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

}
