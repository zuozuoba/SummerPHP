<?php
/**
* Redis
*/
class IRedis extends Redis
{
    public static $instance;
    
    /**
     * 确保每次处理PHP请求时, 每个reids host只有一个连接
     * @param string $hostName RedisConfig 中的配置项, 以host为单元分组
     * @return mixed
     */
	public static function getInstance($hostName='host1')
    {
        if (empty(IRedis::$instance[$hostName])) {
            self::$instance[$hostName] = new IRedis($hostName);
        }
        return self::$instance[$hostName];
    }
    
    /**
     * 因为Redis的构造函数是public所以这里也是public的
     * IRedis constructor.
     * @param $hostName
     */
	public function __construct($hostName)
	{
		parent::__construct();
        list($host, $port, $auth) = RedisConfig::$$hostName;
		
		$this->connect($host, $port);
		$this->auth($auth);
	}

    /**
     * desc 获取存储在redis中的键名, 补上前缀和后缀
     * 所有的键名都写在同一个地方(例如 config/RedisConfig.php)方便键名统一管理
     * @param string|array  $key 键名后缀(以数组形式传递, 最终转化为以下划线形式链接)
     * @throws Exception
     * @return string
     */
    public static function getFullKeyName($key)
    {
        //键名不能为空
        if (empty($key)) {
            throw new Exception('Redis键名传入为空');
        }

        //变量后缀用下划线链接
        //字符串与变量用冒号隔开, 管理软件默认分割符
        if (is_array($key)) {
            $key = implode(':', $key);
        }

        return $key;
    }
    
}