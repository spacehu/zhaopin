<?php

namespace TigerDAL\Cms;

use TigerDAL\BaseDAL;

class QuestionnaireTestDAL {

    /** 获取用户信息列表 */
    public static function getAll($questionnaire_id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("questionnaire_test") . " where `delete`=0 and questionnaire_id=" . $questionnaire_id . " order by edit_time desc  ;";
        return $base->getFetchAll($sql);
    }

    /** 获取用户信息 */
    public static function getOne($id) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("questionnaire_test") . " where `delete`=0 and id=" . $id . "  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 获取用户信息 */
    public static function getByName($name) {
        $base = new BaseDAL();
        $sql = "select * from " . $base->table_name("questionnaire_test") . " where `delete`=0 and name='" . $name . "'  limit 1 ;";
        return $base->getFetchRow($sql);
    }

    /** 插入 */
    public function insertQuestionnaireTest($data) {
        $base = new BaseDAL();
        self::insert($data);
        return $base->last_insert_id();
    }

    /** 新增用户信息 */
    public static function insert($data) {
        $base = new BaseDAL();
        if (is_array($data)) {
            return $base->insert($data,'questionnaire_test');
        } else {
            return true;
        }
    }

    /** 更新用户信息 */
    public static function update($id, $data) {
        $base = new BaseDAL();
        if (is_array($data)) {
            return $base->update($id,$data,'questionnaire_test');
        } else {
            return true;
        }
    }

    /** 删除用户信息 */
    public static function delete($id) {
        $base = new BaseDAL();
        $sql = "update " . $base->table_name('questionnaire_test') . " set `delete`=1  where id=" . $id . " ;";
        return $base->query($sql);
    }

    /** 保存最新值 其他直接删除 */
    public static function save($_data, $aid, $_sourseData,$_removeData) {
        $base = new BaseDAL();
        if (!empty($_removeData)) {
            $sql = "delete from " . $base->table_name('questionnaire_test') . " where questionnaire_id=".$aid." and `test_id` in (" . $_removeData . ");";
            //echo $sql;
            $base->query($sql);
        }
        if (!empty($_data)) {
            $arr=explode(",",$_data);
            foreach ($arr as $v) {
                if ($v != 0) {
                    $os = $_sourseData;
                    array_unshift($os, $aid, $v);
                    print_r($os);
                    self::insert($os);
                }
            }
        }
        //die;
        return true;
    }

}
