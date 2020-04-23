<?php

namespace action\v4;

use mod\common as Common;
use TigerDAL\Api\AuthDAL;
use TigerDAL\Api\TokenDAL;
use TigerDAL\Api\WeChatDAL;
use TigerDAL\Api\BWeChatDAL;
use TigerDAL\Api\LogDAL;
use config\code;
use TigerDAL\Cms\UserInfoDAL;
use TigerDAL\Cms\UserDAL;
use mod\wechat;

class ApiAuth extends \action\RestfulApi {

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

    /** 
     * 微信授权登录
     * 传入参数 
     * token 本地验证
     * code 需要微信验证
     * 返回参数
     * success 是否成功
     * data 失败信息｜｜成功信息（用户基本信息）
     */
    function loginWithWeChatAuthAuthorize() {
        try {
            // 判断是否已经登录 否则进入code判断
            $UserInfoDAL=new UserInfoDAL();
            if(!empty($this->user_id)){
                // 判断是否是个人用户 查询出信息 否则返回该用户是企业用户 并提示前端进行页面跳转
                if(!empty($this->server_id)&&$this->server_id==\mod\init::$config['token']['server_id']['customer']){
                    $_dbUserInfo=$UserInfoDAL->getUser($this->user_id);
                    self::$data['success'] = true;
                    self::$data['data']=$_dbUserInfo;
                    self::$data['data']['token']=$this->header['token'];
                }else if(!empty($this->server_id)&&$this->server_id==\mod\init::$config['token']['server_id']['business']){
                    self::$data['success']=false;
                    self::$data['data']['code'] = "business_user";
                    self::$data['msg']="该用户为企业用户";
                    return self::$data;
                }
            }else if(!empty($this->get['code'])||!empty($this->get['openid'])){
                // 从微信的类包里面获取openid 然后查询表中该用户是否存在 
                // 若存在 则查询出数据并折算出token 返回
                // 否则 新建用户 并返回用户数据 并折算出token 返回
                $wechat= new wechat();
                if(empty($this->get['openid'])){
                    LogDAL::saveLog("DEBUG", "INFO", json_encode($wechat->code));
                    LogDAL::saveLog("DEBUG", "INFO", json_encode($wechat->appid));
                    LogDAL::saveLog("DEBUG", "INFO", json_encode($wechat->appsecret));
                    $_apiAccessToken=$wechat->beforeDb();
                    LogDAL::saveLog("DEBUG", "INFO", json_encode($wechat->code));
                    if($_apiAccessToken['errcode'] == 40029){
                        /** 微信返回错误 */
                        self::$data['success'] = false;
                        self::$data['data'] = $_apiAccessToken;
                        return self::$data;
                    }elseif($_apiAccessToken['errcode'] == 40163){
                        /** 微信返回错误 */
                        self::$data['success'] = false;
                        self::$data['data'] = $_apiAccessToken;
                        return self::$data;
                    }
                    $openid=$_apiAccessToken['openid'];
                }else{
                    $openid=$this->get['openid'];
                }
                //echo $openid;
                $WeChatDAL=new WeChatDAL();
                //获取db中 微信用户表数据
                $_dbUserWeChatInfo = $WeChatDAL->getOpenId($openid);     //根据OPENID查找数据库中是否有这个用户，如果没有就写数据库。继承该类的其他类，用户都写入了数据库中。  
                if (empty($_dbUserWeChatInfo)) {
                    // 根据openid 获取用户授权信息
                    $userInfo=$wechat->afterDb();
                    if (empty($userInfo) || empty($userInfo['openid'])) {
                        /** 微信返回错误 */
                        self::$data['success'] = false;
                        self::$data['data']['code'] = $_apiAccessToken;
                        self::$data['data']['userError'] = $userInfo;
                        return self::$data;
                    }
                    $_data = [
                        'openid' => $userInfo['openid'],
                        'nickname' => $userInfo['nickname'],
                        'sex' => $userInfo['sex'],
                        'language' => $userInfo['language'],
                        'city' => $userInfo['city'],
                        'province' => $userInfo['province'],
                        'country' => $userInfo['country'],
                        'headimgurl' => $userInfo['headimgurl'],
                        'privilege' => json_encode($userInfo['privilege']),
                        'add_time' => date("Y-m-d H:i:s"),
                        'edit_time' => date("Y-m-d H:i:s"),
                        'user_id' => $this->user_id,
                        'phone' => "",
                    ];
                    //数据记录到db中 微信用户表
                    $_dbUserWeChatInfo=$WeChatDAL->insertData($_data);
                }
                
                // 获取 微信表 & 用户表 数据

                $_dbUserInfo = $UserInfoDAL->getUserInfo($_dbUserWeChatInfo['user_id'],$_dbUserWeChatInfo['nickname']);
                if(empty($_dbUserWeChatInfo['user_id'])){
                    $_row['user_id']=$_dbUserInfo['id'];
                    $WeChatDAL->updateWeChatUserInfo($_dbUserWeChatInfo['id'],$_row);
                }
                //var_dump($_row);die;
                $_dbUserInfo=$UserInfoDAL->getUser($_dbUserInfo['id']);
                self::$data['success'] = true;
                self::$data['data'] = $_dbUserInfo;
                // 补上token
                self::$data['data']['token'] = TokenDAL::saveToken($_dbUserInfo['id'], \mod\init::$config['token']['server_id']['customer']);
                self::$data['data']['deathline'] = TokenDAL::getTimeOut();
                
            }
            
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        LogDAL::saveLog("DEBUG", "INFO", json_encode(self::$data));
        return self::$data;
    }

    /** 检查手机号是否已用 */
    function savePhone() {
        try {
            $phone = Common::specifyChar($this->post['phone']);
            $code = Common::specifyChar($this->post['code']);
            $AuthDAL = new AuthDAL();
            $check = $AuthDAL->checkPhone($phone, $code);
            if ($check !== true) {
                self::$data['success'] = false;
                self::$data['data']['code'] = $check;
                self::$data['msg'] = code::$code[$check];
                return self::$data;
            }
            $UserInfoDAL=new UserInfoDAL();
            $data=[
                'phone'=>$phone,
            ];
            $UserInfoDAL->update($this->user_id,$data);
            self::$data['data'] = $check;
            return self::$data;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
    }
    
    /** 企业主登录 */
    function signWithWeChatAuthAuthorize() {
        try {
            $UserDAL=new UserDAL();
            // 判断是否已经登录 否则进入code判断
            if(!empty($this->user_id)){
                // 判断是否是个人用户 查询出信息 否则返回该用户是企业用户 并提示前端进行页面跳转
                if(!empty($this->server_id)&&$this->server_id==\mod\init::$config['token']['server_id']['customer']){
                    self::$data['success']=false;
                    self::$data['data']['code'] = "customer_user";
                    self::$data['msg']="该用户为个人用户";
                    return self::$data;
                }else if(!empty($this->server_id)&&$this->server_id==\mod\init::$config['token']['server_id']['business']){
                    $_dbUserInfo=$UserDAL->getUser($this->user_id);
                    self::$data['success'] = true;
                    self::$data['data']=$_dbUserInfo;
                    self::$data['data']['token']=$this->header['token'];
                }
            }else if(!empty($this->get['code'])){
                // 从微信的类包里面获取openid 然后查询表中该用户是否存在 
                // 若存在 则查询出数据并折算出token 返回
                // 否则 新建用户 并返回用户数据 并折算出token 返回
                $wechat= new wechat();
                $_apiAccessToken=$wechat->beforeDb();
                if($_apiAccessToken['errcode'] == 40029){
                    /** 微信返回错误 */
                    self::$data['success'] = false;
                    self::$data['data'] = $_apiAccessToken;
                    return self::$data;
                }elseif($_apiAccessToken['errcode'] == 40163){
                    /** 微信返回错误 */
                    self::$data['success'] = false;
                    self::$data['data'] = $_apiAccessToken;
                    return self::$data;
                }
                $WeChatDAL=new BWeChatDAL();
                //获取db中 微信用户表数据
                $_dbUserWeChatInfo = $WeChatDAL->getOpenId($_apiAccessToken['openid']);     //根据OPENID查找数据库中是否有这个用户，如果没有就写数据库。继承该类的其他类，用户都写入了数据库中。  
                if (empty($_dbUserWeChatInfo)) {
                    // 根据openid 获取用户授权信息
                    $userInfo=$wechat->afterDb();
                    if (empty($userInfo) || empty($userInfo['openid'])) {
                        /** 微信返回错误 */
                        self::$data['success'] = false;
                        self::$data['data']['code'] = $_apiAccessToken;
                        self::$data['data']['userError'] = $userInfo;
                        return self::$data;
                    }
                    $_data = [
                        'openid' => $userInfo['openid'],
                        'nickname' => $userInfo['nickname'],
                        'sex' => $userInfo['sex'],
                        'language' => $userInfo['language'],
                        'city' => $userInfo['city'],
                        'province' => $userInfo['province'],
                        'country' => $userInfo['country'],
                        'headimgurl' => $userInfo['headimgurl'],
                        'privilege' => json_encode($userInfo['privilege']),
                        'add_time' => date("Y-m-d H:i:s"),
                        'edit_time' => date("Y-m-d H:i:s"),
                        'user_id' => $this->user_id,
                        'phone' => "",
                    ];
                    //数据记录到db中 微信用户表
                    $_dbUserWeChatInfo=$WeChatDAL->insertData($_data);
                }
                
                // 获取 微信表 & 用户表 数据
                // 这里有个问题 如果没有绑定过管理员 该微信号中的user_id为空 则直接返回openid
                if(empty($_dbUserWeChatInfo['user_id'])){
                    self::$data['success'] = true;
                    self::$data['data']['code'] = "empty_phone";
                    self::$data['data']['userError'] = $_dbUserWeChatInfo;
                    self::$data['data']['openid'] = $_dbUserWeChatInfo['openid'];
                    return self::$data;
                }
                $_dbUserInfo=$UserDAL->getUser($_dbUserWeChatInfo['user_id']);
                self::$data['success'] = true;
                self::$data['data'] = $_dbUserInfo;
                // 补上token
                self::$data['data']['token'] = TokenDAL::saveToken($_dbUserInfo['id'], \mod\init::$config['token']['server_id']['business']);
                self::$data['data']['deathline'] = TokenDAL::getTimeOut();
                
            }else if(!empty($this->get['openid'])&&!empty($this->get['phone'])&&!empty($this->get['phoneCode'])){
                $openid=$this->get['openid'];
                $phone=$this->get['phone'];
                $code=$this->get['phoneCode'];
                // 初次绑定手机号的使用方法
                // 通过微信openid获取微信数据 通过手机号 获取管理员数据 然后在微信表中绑定管理员id 并使用管理员id生成token 并返回
                $WeChatDAL=new BWeChatDAL();
                //获取db中 微信用户表数据
                $_dbUserWeChatInfo = $WeChatDAL->getOpenId($openid); 
                if(empty($_dbUserWeChatInfo)){
                    self::$data['success'] = false;
                    self::$data['data']['code'] = "error_openid";
                    self::$data['data']['userError'] = $_dbUserWeChatInfo;
                    return self::$data;
                }
                $AuthDAL = new AuthDAL();
                $check = $AuthDAL->bCheckPhone($phone, $code);
                if($check!==true){
                    self::$data['success'] = false;
                    self::$data['data']['code'] = "error_phone";
                    self::$data['data']['userError'] = $check;
                    return self::$data;
                }
                $_dbUser=$UserDAL->getByName($phone);
                if(empty($_dbUserWeChatInfo['user_id'])){
                    $_row['user_id']=$_dbUser['id'];
                    $WeChatDAL->updateWeChatUserInfo($_dbUserWeChatInfo['id'],$_row);
                }
                // 获取用户信息
                $_dbUserInfo=$UserDAL->getUser($_dbUser['id']);
                self::$data['success'] = true;
                self::$data['data'] = $_dbUserInfo;
                // 补上token
                self::$data['data']['token'] = TokenDAL::saveToken($_dbUserInfo['id'], \mod\init::$config['token']['server_id']['business']);
                self::$data['data']['deathline'] = TokenDAL::getTimeOut();
            }
            
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        LogDAL::saveLog("DEBUG", "INFO", json_encode(self::$data));
        return self::$data;
    }

    /******************************************************** */


    /** 检查手机号是否已用 */
    function checkPhone() {
        try {
            $phone = Common::specifyChar($this->post['phone']);
            $code = Common::specifyChar($this->post['code']);
            $AuthDAL = new AuthDAL();
            $check = $AuthDAL->checkPhone($phone, $code);
            if ($check !== true) {
                self::$data['success'] = false;
                self::$data['data']['code'] = $check;
                self::$data['msg'] = code::$code[$check];
                return self::$data;
            }
            self::$data['data'] = $check;
            return self::$data;
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
    }

    /** 注册 */
    function register() {
        try {
            $phone = Common::specifyChar($this->post['phone']);
            $code = Common::specifyChar($this->post['code']);
            $name = Common::specifyChar($this->post['name']);
            $password = Common::specifyChar($this->post['password']);
            $cfn_password = Common::specifyChar($this->post['cfn_password']);
            if (strlen($password) < 6) {
                self::$data['success'] = false;
                self::$data['data']['code'] = 'errorPasswordLength';
                self::$data['msg'] = code::$code['errorPasswordLength'];
                return self::$data;
            }
            if ($password != $cfn_password) {
                self::$data['success'] = false;
                self::$data['data']['code'] = 'errorPasswordDifferent';
                self::$data['msg'] = code::$code['errorPasswordDifferent'];
                return self::$data;
            }
            $AuthDAL = new AuthDAL();
            $check = $AuthDAL->checkPhone($phone, $code);
            if ($check !== true) {
                self::$data['success'] = false;
                self::$data['data']['code'] = $check;
                self::$data['msg'] = code::$code[$check];
                return self::$data;
            }
            $data = [
                'name' => $name,
                'phone' => $phone,
                'nickname' => '',
                'photo' => '',
                'brithday' => '',
                'province' => '',
                'city' => '',
                'district' => '',
                'email' => '',
                'sex' => '',
                'add_time' => date("Y-m-d H:i:s", time()),
                'edit_time' => date("Y-m-d H:i:s", time()),
                'user_id' => 0, //弃用字段
                'password' => md5($password),
                'last_login_time' => date("Y-m-d H:i:s", time()),
            ];
            $res = $AuthDAL->insert($data);
            if (!empty($res)) {
                self::$data['data'] = $res;
                if (empty($this->header['openid'])) {
                    $wechat = new WeChatDAL();
                    $openid = $this->header['openid'];
                    $result = $wechat->getOpenId($openid);     //根据OPENID查找数据库中是否有这个用户，如果没有就写数据库。继承该类的其他类，用户都写入了数据库中。  
                    LogDAL::saveLog("DEBUG", "INFO", json_encode($result));
                    if (empty($result)) {
                        $_data = [
                            'openid' => $openid,
                            'nickname' => '',
                            'sex' => '',
                            'language' => '',
                            'city' => '',
                            'province' => '',
                            'country' => '',
                            'headimgurl' => '',
                            'privilege' => '',
                            'add_time' => date("Y-m-d H:i:s"),
                            'edit_time' => date("Y-m-d H:i:s"),
                            'user_id' => $res,
                            'phone' => "",
                        ];
                        LogDAL::saveLog("DEBUG", "INFO", json_encode($_data));
                        $wechat->addWeChatUserInfo($_data);
                    }
                }
            } else {
                self::$data['success'] = false;
                self::$data['data']['code'] = "errorSql";
                self::$data['msg'] = code::$code['errorSql'];
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 用户登录 */
    function login() {
        try {
            $phone = Common::specifyChar($this->post['phone']);
            $password = Common::specifyChar($this->post['password']);

            $AuthDAL = new AuthDAL();
            $check = $AuthDAL->checkUser($phone, $password);
            if ($check['error'] == 1) {
                self::$data['success'] = false;
                self::$data['data']['code'] = $check['code'];
                self::$data['msg'] = code::$code[$check['code']];
            } else {
                if (!empty($this->header['openid'])) {
                    $wechat = new WeChatDAL();
                    $openid = $this->header['openid'];
                    $result = $wechat->getOpenId($openid);     //根据OPENID查找数据库中是否有这个用户，如果没有就写数据库。继承该类的其他类，用户都写入了数据库中。  
                    LogDAL::saveLog("DEBUG", "INFO", json_encode($result));
                    $_data = [
                        'user_id' => $check['data']['id'],
                    ];
                    $wechat->updateWeChatUserInfo($result['id'], $_data);
                }
                self::$data['data']['code'] = $check['code'];
                self::$data['data']['token'] = TokenDAL::saveToken($check['data']['id'], \mod\init::$config['token']['server_id']['customer']);
                self::$data['data']['deathline'] = TokenDAL::getTimeOut();
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 登出 */
    function logout() {
        try {
            $TokenDAL = new TokenDAL();
            $TokenDAL->delToken();
            if (empty($this->header['openid'])) {
                $wechat = new WeChatDAL();
                $openid = $this->header['openid'];
                $result = $wechat->getOpenId($openid);     //根据OPENID查找数据库中是否有这个用户，如果没有就写数据库。继承该类的其他类，用户都写入了数据库中。  
                LogDAL::saveLog("DEBUG", "INFO", json_encode($result));
                $_data = [
                    'user_id' => '',
                ];
                $wechat->addWeChatUserInfo($result['id'], $_data);
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }

    /** 重置密码 */
    function reset() {
        try {
            $phone = Common::specifyChar($this->post['phone']);
            $code = Common::specifyChar($this->post['code']);
            $password = Common::specifyChar($this->post['password']);
            $cfn_password = Common::specifyChar($this->post['cfn_password']);
            if (strlen($password) < 6) {
                self::$data['success'] = false;
                self::$data['data']['code'] = 'errorPasswordLength';
                self::$data['msg'] = code::$code['errorPasswordLength'];
                return self::$data;
            }
            if ($password != $cfn_password) {
                self::$data['success'] = false;
                self::$data['data']['code'] = 'errorPasswordDifferent';
                self::$data['msg'] = code::$code['errorPasswordDifferent'];
                return self::$data;
            }
            $AuthDAL = new AuthDAL();
            $user = $AuthDAL->getUserInfoByCode($phone, $code);
            if (is_string($user)) {
                self::$data['success'] = false;
                self::$data['data']['code'] = $user;
                self::$data['msg'] = code::$code[$user];
                return self::$data;
            }
            $data = [
                'edit_time' => date("Y-m-d H:i:s", time()),
                'password' => md5($password),
            ];
            $res = $AuthDAL->updateUserInfo($user['id'], $data);
            if (!empty($res)) {
                self::$data['data'] = $res;
            } else {
                self::$data['success'] = false;
                self::$data['data']['code'] = "errorSql";
                self::$data['msg'] = code::$code['errorSql'];
            }
        } catch (Exception $ex) {
            TigerDAL\CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
        }
        return self::$data;
    }


}
