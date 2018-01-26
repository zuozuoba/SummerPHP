<?php
class Config
{
    public static $dian_ping = array(
        'appkey' => '',
        'secret' => '',
        'format' => 'json',
    );

    public static $qi_niu = array(
        'accessKey' => '',
        'secretKey' => '',
        'bucket' => '',
        'upToken' => '',
    );

    public static $qq = array(
        'appid' => '',
        'appkey' => '',
        'scope' => 'get_user_info',
        'callback' => '',
        'state_pre' => 'qq_sdk_state',
    );

    public static $we_chat = array(
        'AppID' => '',
        'AppSecret' => '',
        'EncodingAESKey' => '',
        'callback' => '',
        'token' => '',
    );

    public static $encryption = array(
        'key' => '',
    );

    //来源页面URL, 攻击者可以伪造,但也要做基本防护
    public static $refer_allow = array(
        'abc.com',
    );
}