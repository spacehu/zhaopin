<?php

namespace mod;

use mod\common as Common;
use TigerDAL\Api\LogDAL;

class wechat {

    public $header;
    public $post;
    public $get;
    
    public $appid;                   //微信APPID，公众平台获取  
    public $appsecret;               //微信APPSECREC，公众平台获取  
    public $code;
    private $access_token;

    function __construct() {
        $this->header = Common::exchangeHeader();
        $this->post = Common::exchangePost();
        $this->get = Common::exchangeGet();
        $this->appid = init::$config['env']['wechat']['appid'];                   //微信APPID，公众平台获取
        $this->appsecret = init::$config['env']['wechat']['secret'];              //微信APPSECREC，公众平台获取
    }

    
    /**
     * 检测有无$_SESSION。如果有，直接忽略。
     * 如果没有$_SESSION，就依次执行getCode、getOpenId、getUserInfo来获取用户信息。目的是解决CODE只能获取一次，刷新页面openid会丢失的问题。  
     * 再判断是否在数据库中，没有则写入数据库。最后将open_id写入session。  
     */
    public function beforeDb() {
                                   
        //获取get的code
        $this->code = $this->getCode();
        //获取getOpenId和access_token
        $this->access_token = $this->getOpenId();
        return $this->access_token;
    }

    public function afterDb($access_token,$data){
        //获取微信api用户信息
        $userInfo = $this->getUserInfo($access_token);
        if (!empty($userInfo) && !empty($userInfo['openid'])) {
            return $userInfo;
        }
        return false;
    }
    /**
     * @explain 
     * 获取code,用于获取openid和access_token 
     * @remark 
     * code只能使用一次，当获取到之后code失效,再次获取需要重新进入 
     * 不会弹出授权页面，适用于关注公众号后自定义菜单跳转等，如果不关注，那么只能获取openid 
     * */
    public function getCode() {
        return $this->get["code"];
    }

    /**
     * @explain 
     * 用于获取公众号用户openid 
     * */
    public function getOpenId() {
        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appid . "&secret=" . $this->appsecret . "&code=" . $this->code . "&grant_type=authorization_code";
        //LogDAL::saveLog("DEBUG", "info", $access_token_url);
        $access_token_json = $this->https_request($access_token_url);
        $access_token_array = json_decode($access_token_json, TRUE);
        return $access_token_array;
    }

    /**
     * @explain
     * 通过code获取用户openid以及用户的微信号信息
     * @param $access_token
     * @return mixed
     * @remark
     * 获取到用户的openid之后可以判断用户是否有数据，可以直接跳过获取access_token,也可以继续获取access_token
     * access_token每日获取次数是有限制的，access_token有时间限制，可以存储到数据库7200s. 7200s后access_token失效
     */
    public function getUserInfo($access_token) {
        if(!empty($access_token)){
            $this->access_token=$access_token;
        }
        $userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $this->access_token['access_token'] . "&openid=" . $this->access_token['openid'] . "&lang=zh_CN";
        //LogDAL::saveLog("DEBUG", "info", $userinfo_url);
        $userinfo_json = $this->https_request($userinfo_url);
        $userinfo_array = json_decode($userinfo_json, TRUE);
        return $userinfo_array;
    }

    /**
     * 前端用 获取access_token 用 的 
     * @param type $access_token
     * @return type
     */
    public function getToken() {
        $userinfo_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appid . "&secret=" . $this->appsecret . "";
        LogDAL::saveLog("DEBUG", "INFO", $userinfo_url);
        $userinfo_json = $this->https_request($userinfo_url);
        LogDAL::saveLog("DEBUG", "INFO", $userinfo_json);
        $userinfo_array = json_decode($userinfo_json, TRUE);
        return $userinfo_array;
    }

    /**
     * 前端用 获取ticket 用 的 
     * @param type $access_token
     * @return type
     */
    public function getJsApiTicket($access_token) {
        $userinfo_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $access_token . "&type=jsapi";
        $userinfo_json = $this->https_request($userinfo_url);
        $userinfo_array = json_decode($userinfo_json, TRUE);
        return $userinfo_array;
    }

    /**
     * @explain
     * 发送http请求，并返回数据
     * @param $url
     * @param null $data
     * @return mixed
     */
    public function https_request($url, $data = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

}
