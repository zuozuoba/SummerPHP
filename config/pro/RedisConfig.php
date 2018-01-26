<?php

class RedisConfig
{
    static $dev = array('127.0.0.1', 6379, 'test');
    static $pro = array('127.0.0.1', 6379, 'test');
    
    static $Key_Safe_Token = 'Safe_Token'; //比对后不删除
    static $User_Info = 'User_Info'; //用户信息
    static $ADMIN_LOGIN = 'AdminLogin'; //管理员登陆信息缓存
    
}