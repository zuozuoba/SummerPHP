<?php
/**
* Redis
*/
class iredis extends Redis
{
    public static $instance;
	public static function getInstance()
    {
        if (!iredis::$instance) {
            self::$instance = new iredis();
        }
        return self::$instance;
    }
    
	public function __construct()
	{
		parent::__construct();
		$config = Config::$redis;
		
		$this->connect($config['host'], $config['port']);
		$this->auth($config['auth']);
	}

    public static $REDIS_PRE = 'php:Finger_';

    public static $Key_Safe_Token = 'Safe_Token'; //比对后不删除

	public static $User_Info = 'User_Info'; //用户信息

    public $key = '';

    /**
     * desc 获取存储在redis中的键名, 补上前缀和后缀
     * 所有的键名都写在本类中当作成员变量, 不要在其他地方定义, 方便键名统一管理
     * 键名前缀默认为$REDIS_PRE, 暂不接受自定义
     * @param string $key redis键名
     * @param array  $end 键名后缀(以数组形式传递, 最终转化为以下划线形式链接)
     * @param string $pre 键名前缀
     * @return string
     */
    public static function getFullKeyName($key, $end=[], $pre='')
    {
        //键名不能为空
        if (empty($key)) {
            return false;
        }

        //变量后缀用下划线链接
        //字符串与变量用冒号隔开, 管理软件默认分割符
        $strEnd = '';
	    if (!empty($end)) {
		    if (is_array($end)) {
			    $strEnd = ':'. implode('_', $end);
		    } else {
			    $strEnd = ':' . $end;
		    }
	    }

        $strPre = $pre ? $pre : self::$REDIS_PRE;

        return $strPre.$key.$strEnd;
    }

    public function setFullKeyName($key, $end=[], $pre)
    {
        $this->key = self::getFullKeyName($key, $end, $pre);
        /*
         * 赋给成员变量, 方便自定义函数使用
         * $obj = iredis::getInstance()->setFullKeyName($abc);
         * $obj->myFunc()
         * {
         *      $this->set($this->key, '1234');
         * }
         *
        */
    }

	public function putWeixinVoice($username, $textOfVoice)
	{
		$this->lpush($username, $textOfVoice);
	}

	public function getWeixinVoice($user)
	{
		return $this->rpop($user);
	}
    
}