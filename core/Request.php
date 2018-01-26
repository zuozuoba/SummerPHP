<?php

/**
 * desc 获取请求参数, 只检查数据的合法性, 并不改变数据内容
 * Class Request
 */
class Request
{
    public static $data = false;
    public $isValid = false;

    public static function Post($key, $default=NULL, $check='')
    {
        self::$data = isset($_POST[$key]) ? $_POST[$key] : $default;
        self::valid($check);

        return self::$data;
    }

    public static function Get($key, $default=NULL, $check='')
    {
        self::$data = isset($_GET[$key]) ? $_GET[$key] : $default;
        self::valid($check);

        return self::$data;
    }

    public static function Cookie($key, $default=NULL, $check='')
    {
        self::$data = isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
        self::valid($check);

        return self::$data;
    }

    public static function Route($key, $default=NULL, $check='')
    {
        self::$data = isset(Route::$args[$key]) ? Route::$args[$key] : $default;
        self::valid($check);

        return self::$data;
    }
    
    public static function Server($key, $default=NULL)
    {
        self::$data = isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
        return self::$data;
    }
    
    public static function Session($key, $default=NULL)
    {
        self::$data = isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
        return self::$data;
    }

    /**
     * desc 检测数据的合法性
     * @param int $check Safe类里定义的检测类型
     */
    public static function valid($check)
    {
        if (empty($check)) {
            $check = Safe::$Check_DEFAULT;
        }
        Safe::check(self::$data, $check);
    }

    public static function isMobile()
    {
        //...
    }
    
    public static function isPost()
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public static function Url()
    {
        return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
    
    /**
     * 一般获得用户IP都是使用$_SERVER['REMOTE_ADDR']这个环境变量，但是此变量只会纪录最后一个主机IP，所以当用户浏览器有设定Proxy时，就无法取得他的真实IP。
     * 这时可以使用另一个环境变量$_SERVER['HTTP_X_FORWARDED_FOR']，它会纪录所经过的主机IP，但是只有在用户有透过Proxy时才会产生,
     * 而且需要webserver的支持
     * @return string 客户端ip
     */
    public static function getClientIp()
    {
        $REMOTE_ADDR = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
        // $HTTP_CLIENT_IP = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : false;
        $ARR_HTTP_X_FORWARDED_FOR = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) : array('0');
        $HTTP_X_FORWARDED_FOR = $ARR_HTTP_X_FORWARDED_FOR['0'];
    
        $ip = $HTTP_X_FORWARDED_FOR ? $HTTP_X_FORWARDED_FOR : $REMOTE_ADDR;
        // $ip = $HTTP_CLIENT_IP ? $HTTP_CLIENT_IP : $REMOTE_ADDR;
    
        return $ip ? $ip : '0';
    }
    
    /**
     * @return string 服务器ip
     */
    public static function getServerIp()
    {
        $SERVER_ADDR = !empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : false;
        return $SERVER_ADDR ? $SERVER_ADDR : '0';
    }
}

//http://www.summer.com/index/index/req/a/4route/b?a=1get

//echo '<pre>';
//var_dump(Request::Get('a'));
//var_dump(Request::Get('b'));
//var_dump(Request::Post('a'));
//var_dump(Request::Cookie('a'));
//var_dump(Request::Route('a'));

//string(4) "1get"
//bool(false)
//bool(false)
//bool(false)
//string(6) "4route"