<?php

class User extends Model
{
    public static $UserCookieName = 'SUMMERUSER';
    /**
     * 验证登录信息
     * @param string $mobile
     * @param string $password
     *
     * @return array
     */
    public static function verifyUserLogin($mobile, $password)
    {
        $sec_mobile = Safe::encrypt($mobile);
        $sec_passwd = Fun::saltmd5($password);
        $user = self::link('user')
            ->fields('uid, username, mobile')
            ->where(['mobile' => $sec_mobile, 'password' => $sec_passwd])
            ->getOne();

        return !empty($user) ? $user : [];
    }

    /**
     * desc 根据手机号获取uid
     * @param $mobile
     * @param $field
     * @return string
     */
    public static function getUserInfoByMobile($mobile, $field='')
    {
        $mobile = Safe::encrypt($mobile);
        $rs = self::link('user')
            ->where(['mobile' => $mobile])
            ->getOne();

        return (!empty($field) && !empty($rs[$field])) ? $rs[$field] : $rs;
    }
    
    /**
     * desc 根据用户id获取用户信息
     * @param $uid
     * @param string $field
     * @return array|mixed
     */
    public static function getUserInfoById($uid, $field='')
    {
        $rs = self::link('user')
            ->fields('rowid as uid, username, create_time')
            ->where(['rowid' => $uid])
            ->getOne();
        
        return (!empty($field) && !empty($rs[$field])) ? $rs[$field] : $rs;
    }

    /**
     * desc 记录用户登录信息
     * @param $user
     * @param $expire
     */
    public static function setUserCookie($user, $expire=0)
    {
        $json = json_encode($user);
        $secret = Safe::encrypt($json);
        $secret = urlencode($secret);
        $arr = explode('.', $_SERVER['SERVER_NAME']);
        $domain = $arr[count($arr)-2].'.'.$arr[count($arr)-1]; //全域名有效
        
        if (!empty($expire)) {
            setcookie('SUMMERUSER', $secret, 0, '/', $domain, FALSE, TRUE); //关闭浏览器即失效
        } else {
            setcookie('SUMMERUSER', $secret, time()+$expire, '/', $domain, FALSE, TRUE);
        }
        
    }

    /**
     * desc 获取用户信息
     * @param string $filed
     * @return string|array
     */
    public static function getUserCookie($filed='')
    {
        if (!empty(Request::Cookie(self::$UserCookieName))) {
            $secret = urldecode(Request::Cookie(self::$UserCookieName));
            $json = Safe::encrypt($secret, FALSE);
            $user = json_decode($json, TRUE);

            if (!empty($filed) && !empty($user[$filed])) {
                return $user[$filed];
            } else {
                return $user;
            }
        } else {
            return '';
        }

    }

    /**
     * desc 清空用户登录信息
     */
    public static function clearUserCookie()
    {
        $arr = explode('.', $_SERVER['SERVER_NAME']);
        $domain = $arr[count($arr)-2].'.'.$arr[count($arr)-1]; //全域名有效
        setcookie(self::$UserCookieName, '-1', time()-3600, '/', $domain,FALSE, TRUE);
    }

}