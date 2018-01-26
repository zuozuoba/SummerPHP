<?php

/**
 * Class Safe 验证类
 * 规范: 添加一个static变量要对应添加一个同名的方法
 * 。尽量用post: 绝大部分的xss攻击是通过 <img> <script> <a> <link> 等标签的 src 属性或href属性发起的"get"请求
 * 。值中不能有\0截断符
 * 。添加验证码: 防止外部构造表单请求, 要用在关键的地方(登录注册等), 不要影响到用户
 * 。检测refer: 防止外部构造表单请求, 防止jsonp攻击, csrf攻击
 * 。检测refer: 在返回头中构造白名单, header('Access-Control-Allow-Origin: http://www.a.com')
 * 。一次性token: 用完就释放, 防止表单重复提交, 防止csrf攻击
 * 。include, file_get_content等使用时如果参数是URL,就得验证是否是白名单内的域名, 否则会包含/引入危险的文件
 * 。对参数进行url_decode 防止 相对路径攻击(../../....)和javascript变相攻击
 * 。set cookie 的时候最好设置httponly, 这样就只能通过抓包来获取cookie注入脚本的方法就不行了
 * 。获取cookie时限制跟ip相关, 这样通过xss的方法获取的cookie就不能被乱用了
 * 。html<base href=""> 标签可以指定本页面以后相对路径的根路径URL, 所以链接都要用全URL路径
 * 。表单中过滤掉html注释符 "<!--" "-->", 防止表单回填的时候被攻击
 * 。表单中过滤掉select insert replace delete
 *
 * //非代码级别安全设置
 * 。上传的临时文件夹不要有可执行的权限，最好用第三方存储
 *
 */
class Safe
{
	public static $Instance = false;

//    public static $Check_Token      = 1;
//    public static $Check_Token_Once = 2;
//    public static $Check_Refer      = 4;
    public static $Check_Path       = 8; //防止相对路径包含
    public static $Check_JavaScript = 16; //防止有'script'
    public static $Check_SQLCHAR    = 32; //防止有'sql'
    public static $Check_POST       = 64; //限制post方式提交
    public static $Check_INT       	= 128; //限制数字
    public static $Check_Mobile     = 256; //限制手机号
    public static $Check_Empty    	= 512; //数据必传

	//self::$Check_DEFAULT = self::$Check_Path | self::$Check_JavaScript | self::$Check_SQLCHAR;
	public static $Check_DEFAULT = 56;//默认检查项

	private function __construct()
	{}
	
	public static function getInstance()
	{
		if (!self::$Instance) {
			self::$Instance = new Safe;
		}
		return self::$Instance;
	}

    /**
     * @param String $data 明文/密文
     * @param bool|true $encode true:加密; false:解密
     * @return mixed|string 密文/明文
     */
    public static function encrypt($data, $encode = true)
    {
        $config = Config::$encryption;
        $privateKey = $config['key'];
        if ($encode) {//加密
            $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_ECB);
            return base64_encode($encrypted);
        } else { //解密
            $encryptedData = base64_decode($data);
            $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_ECB);
            return trim($decrypted);
        }
    }

    /**
	 * desc 分发到相应的函数去校验, 暂时只支持2维数组
     * @param array|string $param
     * @param string $check_item 二进制字符串 e.g Safe::Check_Token | Safe::Check_Refer
	 * @return string|array 返回被检测的数据
     */
    public static function check($param, $check_item='')
    {
		if (empty($check_item)) {
			return $param;
		}

		$isSafe = true;

        $vars = get_class_vars(__CLASS__);//获取本类中定义的静态变量
	    unset($vars['Check_DEFAULT']);

        foreach ($vars as $method_name => $mask) {
            if (strpos($method_name, 'Check_') !== false) { //筛选出以Check_开头的变量
                $result = $mask & $check_item;
                if ($result) {
                    if (is_array($param)) {
                        foreach ($param as $v) {
                            $isSafe = self::$method_name($v);
							if ($isSafe !== TRUE) {
								self::error(array($method_name, $v, $isSafe));
							}
                        }
                    } else {
                        $isSafe = self::$method_name($param);
						if ($isSafe !== TRUE) {
							self::error(array($method_name, $param, $isSafe));
						}
                    }
                }
            }
        }

		return $param;
    }

    /**
     * @param int $len 生成token字符串的长度
     * @param int $expire 有效时间, 单位秒
     * @param int $order 使用token的次数, 有时候表单提交是分步骤的, 一个token可能被用多次, $order记录次数
     * @return string 返回token
     */
    public static function Create_Token($len=10, $expire=600, $order=1)
    {
	    $str = md5(REQUEST_TIME_FLOAT.mt_rand(1000,9999));
	    $token = mb_substr($str, 0, $len, 'UTF-8');

	    //将token写入redis
        $key = IRedis::getFullKeyName(array(RedisConfig::$Key_Safe_Token, $token));
		IRedis::getInstance()->setex($key, $expire, $order);

		//将Token写入cookie
		$arr = explode('.', $_SERVER['SERVER_NAME']);
		$domain = $arr[count($arr)-2].'.'.$arr[count($arr)-1]; //全域名有效

		setcookie('SUMMERTOKEN', $token, time()+$expire, '/', $domain, FALSE, TRUE);

        return $token;
    }

    /**
     * @param int $order 第几个步骤
     * @return bool|string 返回token对应的值
     * desc 检测token值是否存在
     */
    public static function Check_Token($order=1)
    {
		$token = !empty($_COOKIE['SUMMERTOKEN']) ? $_COOKIE['SUMMERTOKEN'] : '1';

        $key = IRedis::getFullKeyName(array(RedisConfig::$Key_Safe_Token, $token));
        $rs = IRedis::getInstance()->get($key);

		return ($rs == $order) ? TRUE : '您的操作已过期, 请刷新页面后重新操作~';
    }

    /**
     * @return bool|string 返回token对应的值
     * desc 检测token值是否存在
     */
    public static function Check_Token_Once()
    {
		$token = !empty($_COOKIE['SUMMERTOKEN']) ? $_COOKIE['SUMMERTOKEN'] : '1';
        $key = IRedis::getFullKeyName(array(RedisConfig::$Key_Safe_Token, $token));
        $rs = IRedis::getInstance()->get($key);

        if ($rs) {
			IRedis::getInstance()->del($key);
            return TRUE;
        } else {
            return '您的操作已过期, 请刷新页面后重新操作~';
        }

    }

    /**
     * @return bool
     * desc 检测请求来源是否是在白名单内
     */
    public static function Check_Refer()
    {
        $server_referer = parse_url(HTTP_REFERER);//来源URL
	    $arrHost= explode('.', $server_referer['host']);

	    $top = array_pop($arrHost);
	    $second = array_pop($arrHost);

	    $target = $second.'.'.$top;

	    if (in_array($target, Config::$refer_allow) === FALSE) {
			return '未知来源';
	    } else {
		    return TRUE;
	    }
    }

    /**
     * @param $param
     * @return bool
     * desc 检测提交的数据中是否包含有 script 脚本
     */
    public static function Check_JavaScript($param)
    {
		is_array($param) && ($param = implode('',$param));
        $param = urldecode($param);
        if (strpos($param, '<script') !== false) {
            return '检测到非法输入!';
        } else {
	        return TRUE;
        }

    }

    /**
     * @param $param
     * @return bool
     * desc 检测提交的数据中是否包含有相对路径
     */
    public static function Check_Path($param)
    {
		is_array($param) && ($param = implode('',$param));
        $param = urldecode($param);
        if (strpos($param, '../') !== false) {
            return '检测到非法输入!';
        } else {
	        return TRUE;
        }
    }

	/**
	 * @param $param
	 * @return bool
	 * desc 检测提交的数据中是否包含有相对路径
	 */
	public static function Check_SQLCHAR($param)
	{
		is_array($param) && ($param = implode('',$param));
		$param = urldecode($param);
		$param = strtolower($param);
		$danger = array(
			'select', 'delete', 'update', 'drop', 'insert', 'truncate', '*',
			//'"', '`', '#'
		);

		foreach ($danger as $v) {
			if (strpos($param, $v) !== FALSE) {
				return '检测到非法输入!';
			}
		}

		return TRUE;
	}

	/**
	 * @param $param
	 * @return bool
	 * desc 检测参数是否为整数
	 */
	public static function Check_INT($param)
	{
		return ((int)$param == $param) ? TRUE : '参数应为整数!';
	}

	/**
	 * @return bool
	 * desc 检测是否是post提交
	 * 本函数不能检验参数是否是在$_POST中
	 */
	public static function Check_POST()
	{
		if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0) {
			return TRUE;
		} else {
			return '提交方式不支持!';
		}
	}

	public static function Check_Empty($param)
	{
		if (strlen($param)) {
			return TRUE;
		} else {
			return '输入不能为空!';
		}
	}

	/**
	 * @param $param
	 * @return bool
	 * desc 检测参数是否为合法手机号
	 */
	public static function Check_Mobile($param)
	{
		if (strlen($param) !== 11) {
			return '手机号长度应为11位数字';
		}

		preg_match('/^1[3|4|5|7|8]\d{9}$/', $param, $match);
		if (empty($match[0])) {
			return '手机号长度应为11位数字';
		}

		return TRUE;
	}

	public static function error($result)
	{
		//if IS_TEST
		$msg = empty($result[2]) ? '检测到不安全输入!'.$result[1] : $result[2];
		exit(json_encode(array('code' => '-1', 'msg' => $msg, 'result' => $result)));
	}

}