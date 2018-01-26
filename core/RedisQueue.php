<?php

trait RedisQueue
{
    public static $defaultQueue = 'defaultQueue';
    /**
     * 往队列塞消息/不存在就创建 (样例)
     * @param string $key
     * @param string|json|array $value
     * @return int
     */
    public static function pushQueue($key, $value)
    {
        $key = !empty($key) ? $key : self::$defaultQueue;
        if (is_array($value)) {
            $value = json_encode($value);
        }
        return IRedis::getInstance()->lPush($key, $value);
    }

	//删除队列
	public static function delQueue($key)
	{
        $key = !empty($key) ? $key : self::$defaultQueue;
		return IRedis::getInstance()->del($key);
	}
    
    //消费队列
    public static function blockPopQueue($key, $holdTime = 10)
    {
        $key = !empty($key) ? $key : self::$defaultQueue;
        while (true) {
            list($keyName, $jsonValue) = IRedis::getInstance()->brPop($key, $holdTime); //https://github.com/phpredis/phpredis/#blpop-brpop
            $arr = json_decode($jsonValue, true);
            if (!empty($arr)) {
//                FileLog::ini('queue')->info($jsonValue);
                if (!empty($arr['_class']) && !empty($arr['_method'])) {
                    $class = $arr['_class']; //必须有_class
                    $method = $arr['_method']; //必须有_method
                    $class::$method($arr); //调用指定的 class::method 去处理
                } else {
//                    FileLog::ini('queue/error')->info($jsonValue); //取消注释即可用
                }
            }
        }
    }
    
    //监控消费进程是否在运行
    //配合crontab 或 supervisor等进程监控程序使用
    public static function watchPopQueue($key)
    {
        $key = escapeshellcmd($key);
        $key = !empty($key) ? $key : self::$defaultQueue;
        $rootDir = ROOT;
        $phpCli = PHPCLI;
        $command = "ps -ef | grep php | grep blockpop | grep $key";
        exec($command, $output);
        if (count($output) < 2) {
            $command = "cd $rootDir && $phpCli cli.php -q cli/queue/blockpop/queuekey/$key &";
            exec($command, $rs);
            var_dump($rs);
        } else {
            var_dump($output);
        }
    }
    
    /**
     * 退出pop进程
     * @param string $key 名字不能为空
     * @return bool
     */
    public static function killPopQueue($key)
    {
        if (empty($key)) {
            return false;
        }
        $key = escapeshellcmd($key);
        // $key = !empty($key) ? $key : self::$defaultQueue;
        $command = "ps -ef | grep php | grep blockpop | grep $key | awk '{print $2}' | xargs kill ";
        exec($command, $output);
        var_dump($output);
    }
    
    /**
     * 批量退出pop进程
     * @param array $arrKeys
     * @return bool
     */
    public static function mKillPopQueue($arrKeys = array())
    {
        if (empty($key)) {
            return false;
        }
        
        foreach ($arrKeys as $key) {
            $key = escapeshellcmd($key);
            self::killPopQueue($key);
        }
    }
    
	public static function mWatchPopQueue($arrKeys = array())
	{
        //pclose(popen("/home/xinchen/backend.php &", 'r'));
        foreach ($arrKeys as $v) {
            //启动后会while(true), 建议写shell脚本, 并配合crontab去批量监控
        }
	}

	
}