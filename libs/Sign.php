<?php

/**
 * Class Sign
 * 签名参数中必须有time 和 sign 两个参数
 */

class Sign
{
    public static $timeout = 600; //签名超时时间
    public static $error = '';
    
    //生成签名
    public static function getMd5Sign($appid, $arr)
    {
        // todo 获取 appid => appkey 数据库或者配置文件
        $appkey = '';
        ksort($arr);
        $string = http_build_query($arr);
        return md5($appkey.$string);
    }
    
    //比对签名
    public static function compareMd5Sign($appid, $arr)
    {
        if (REQUEST_TIME - $arr['time'] >= self::$timeout) {
            self::$error = '签名过期';
        }
        $sign = $arr['sign'];
        unset($arr['sign']);
        
        $testSign = self::getMd5Sign($appid, $arr);
        
        if (strcmp($testSign, $sign) == 0) {
            return true;
        } else {
            self::$error = '签名错误';
            return false;
        }
    }
}