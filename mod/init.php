<?php

namespace mod;

use action\cli\base;
use mod\common as Common;
use PHPMailer;
use TigerDAL\Cms\SystemDAL;

/**
 * OA系统
 * 
 * 网站初始化基类
 * 
 * 所有动作所需要的元素的初始化配置
 *
 * BuildTime 2010-3-30
 *
 * @since Ver 0.1
 */
class init {

    /** 系统配置文件 */
    public static $config;

    /**
     * 初始化系统基类，加载所有系统开销所需要的各种配置
     * 包括数据库配置，常用路径的地址
     * @param $config
     */
    function __construct($config) {
        //载入配置文件
        self::$config = $config;
    }

    function __destruct()
    {
        if (init::$config['debug'] === true) {
            echo 'get: ';
            Common::pr($_GET);
            echo 'post: ';
            Common::pr($_POST);
            echo 'config: ';
            Common::pr(init::$config);
            echo 'session: ';
            Common::pr($_SESSION);
            echo 'cookie: ';
            Common::pr($_COOKIE);
        }
    }


    /** 启动项 */
    public function run() {
        /** 初始化系统类 */
        $this->magicFunction();
        /** 判断语言 */
        $this->setLanguage(self::$config['language']['key']);

        /** 对应的过程名 */
        if (isset($_GET['a'])) {
            $_action = explode("/", $_GET['a']);
            if (!empty($_action['1'])) {
                $action = 'action\\' . $_action['0'] . '\\' . $_action['1'];
            } else {
                $action = 'action\\' . $_GET['a'];
            }
        } else {
            $action = 'action\\' . DEFAULT_ACTION;
        }
        /** 对应的模块名 */
        if (isset($_GET['m'])) {
            $mod = $_GET['m'];
        } else {
            $mod = null;
        }

        /** 自动产生初始化类 */
        $act = new $action();

        //Common::pr($actEval);die;
        if (isset($mod)) {
            $act->$mod();
        }
    }


    /** 关闭魔术引号（加速） */
    private function magicFunction() {
        if (get_magic_quotes_gpc()) {
            $_POST = array_map('stripslashes_deep', $_POST);
            $_GET = array_map('stripslashes_deep', $_GET);
            $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
            $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
        }
    }

    /** 关闭魔术引号（加速）
     * @param $value
     * @return array|string
     */
    private function stripslashes_deep($value) {
        $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);
        return $value;
    }

    /** 语言设置
     * @param $defaultLanguage
     */
    private function setLanguage($defaultLanguage) {
        session_start();
        if (empty($_SESSION['lang'])) {
            $_SESSION['lang'] = $defaultLanguage;
        }
        if (isset($_GET['lang'])) {
            $url = explode("&lang=", $_SERVER['REQUEST_URI']);
            $url = str_replace("-lang-" . $_GET['lang'], '', $url[0]);
            Common::js_redir($url);
        }
        self::$config['language']['common'] = include_once('./languages/' . $_SESSION['lang'] . '/common.php');
    }

    /** 设置模板
     * @param $tmp
     * @param $tmpName
     * @param bool $isContent
     * @return bool
     */
    public static function getTemplate($tmp, $tmpName, $isContent = false) {
        if (!$isContent) {
            $tmpPath = self::$config['template'] . '/' . $tmp . '/' . $tmpName . '.php';
            include_once($tmpPath);
            return true;
        } else {
            $tmpPath = self::$config['template'] . '/' . $tmp . '/' . $tmpName . '.xxx';
            include_once($tmpPath);
            return true;
        }
    }

    /*     * ************************************************************************************************************************************ */

    /* 通用树状结构 end */

    function inside_config() {
        $dbconfig = '';
        $products = SystemDAL::getAll();
        if (!empty($products)) {
            foreach ($products as $k => $v) {
                if (strpos($v['con_name'], '_arr') == false) {
                    $dbconfig[$v['con_name']] = $v['con_value'];
                } else {
                    $arr = explode(';', $v['con_value']);
                    $_arr = '';
                    foreach ($arr as $ka => $va) {
                        $_sarr = explode(':', $va);
                        $_arr[$_sarr[0]] = $_sarr[1];
                    }
                    $dbconfig[$v['con_name']] = $_arr;
                }
            }
        }
        return $dbconfig;
    }

    /**
     * 发送邮件
     * $maildetail 标题内容 (收件人地址$maildetail['user_email'],收件人姓名$maildetail['user_name'],邮件台头$maildetail['subject'],邮件详细$maildetail['body'])
     * @param $maildetail
     * @return bool
     * @throws \phpmailerException
     */
    function for_sm($maildetail) {

        $con = $this->inside_config();

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";
        $mail->IsSMTP();
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.gmail.com"; // SMTP servers
        $mail->Port = 465;
        $mail->SMTPAuth = true; // turn on SMTP authentication
        $mail->IsHTML(true); //开启html格式

        $mail->Username = $con['out_put_email']; // SMTP username
        $mail->Password = $con['out_put_password']; // SMTP password
        //$mail->SMTPDebug  = 2; 

        $mail->From = $con['company_email']; //从哪里发来
        $mail->FromName = $con['company_name']; //从哪里发来

        $mail->AddAddress($maildetail['user_email'], $maildetail['user_name']); //收件人地址
        $mail->AddReplyTo($con['company_email'], $con['company_name']); //对方可回复对象.

        $mail->Subject = $maildetail['subject'];
        $mail->Body = $maildetail['body']; //邮件正文
        //$data['con']=$mail;
        return $mail->Send();
    }


    /** 计划任务 *************************************************************************************** */

    /** 启动项 */
    public function cli() {
        /** 初始化系统类 */
        $this->magicFunction();
        /** 判断语言 */
        $this->setLanguage(self::$config['language']['key']);

        // 发送邮件
        $act = new base();
        $act->userEmail();
    }

}
