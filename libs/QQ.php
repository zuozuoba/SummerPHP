<?php
/**
* 一些常用的函数
*/
class QQ
{
    public $appid = '';
    public $appkey = '';
    public $scope = ''; //权限用逗号隔开
    public $callback = '';
    public $cookierand = '';

    public $get_auth_code_url = 'https://graph.qq.com/oauth2.0/authorize';
    public $get_access_token_url = 'https://graph.qq.com/oauth2.0/token';
    public $get_openid_url = 'https://graph.qq.com/oauth2.0/me';
    public $get_user_info_url = 'https://graph.qq.com/user/get_user_info';

    public $access_token = '';
    public $openid = '';

    public $objRedis = false;
    public $state_pre = '';

    public function __construct($callback_url, $cookierand)
    {
		$config = Config::$qq;
		$this->appid = $config['appid'];
		$this->appkey = $config['appkey'];
		$this->scope = $config['scope'];
		$this->state_pre = $config['state_pre'];
        $this->callback = $callback_url ? $callback_url : $config['callback_url'];
        $this->cookierand = $cookierand;
		
        $this->objRedis = iredis::getInstance();
    }

    public function login()
    {
        //-------生成唯一随机串防CSRF攻击
        $state = md5(uniqid(rand(), TRUE));
        $this->objRedis->hset($this->state_pre, $this->cookierand, $state);
        
        //-------构造请求参数列表
        $arrQueryArg = array(
            'response_type' => 'code',
            'client_id' => $this->appid,
            'redirect_uri' => $this->callback,
            'state' => $state,
            'scope' => $this->scope
        );
        $uri = http_build_query($arrQueryArg);
        $login_url =  $this->get_auth_code_url.'?'.$uri;
        header("Location:$login_url");
    }

    public function callback()
    {
        $state = $this->objRedis->hget($this->state_pre, $this->cookierand);

        //--------验证state防止CSRF攻击
        if($_GET['state'] != $state){
            exit('登录过期~');
        }

        //-------请求参数列表
        $arrTokenArg = array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->appid,
            'redirect_uri' => $this->callback,
            'client_secret' => $this->appkey,
            'code' => $_GET['code']
        );

        //------构造请求access_token的url
        $uri = http_build_query($arrTokenArg);
        $token_url =  $this->get_access_token_url.'?'.$uri;
        $response = Fun::getInstance()->curl_get($token_url);

        $params = array();
        parse_str($response, $params);

        //---------获取openid
        $openid_url =  $this->get_openid_url.'?access_token='.$params['access_token'];
        $response = Fun::getInstance()->curl_get($openid_url);
        //--------检测错误是否发生
        $response = str_replace(array('callback(', ')', ';'), '', $response);
        $response = trim($response);
        $user = json_decode($response, true);

        return array(
            'access_token' => $params['access_token'],
            'openid' => $user['openid']
            );

    }

    public function get_user_info()
    {
        $qq_safe_info = $this->callback();
        $access_token = $qq_safe_info['access_token'];
        $openid = $qq_safe_info['openid'];
        $user_info_url = "{$this->get_user_info_url}?access_token={$access_token}&oauth_consumer_key={$this->appid}&openid={$openid}&format=json";
        $info = Fun::getInstance()->curl_get($user_info_url);

        return array(
            'openid' => $openid,
            'user_info' => $info
            );
    }
}