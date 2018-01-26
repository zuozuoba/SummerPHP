<?php

/**
 * 通过URL触发队列相关操作
 * todo 验证签名 Sign::compareMd5Sign($appi, $appkey, $arr)
 * Class _queue
 */
class _queue
{
    use RedisQueue;
    
    /**
     * push
     * 可以像本函数一样通过URL host/_class/className/_method/methodName/queuekey/redisKeyName/other/111
     * 也可以直接调用Redis的lpush函数将数据打入对应的队列中
     */
    public function push()
    {
        $key = Request::Route('queuekey');
        
        $value = [
            '_class' => 'Test', //必须有
            '_method' => 'queue', //必须有
            'other' => 111
        ];
    
        $rs = self::pushQueue($key, Route::$args);
        var_dump($rs);exit;
    }
    
    /**
     * pop
     * 阻塞运行, 被Lib->RedisQueue::watchPopQueue 监控
     * 函数名不能改, 被Lib->RedisQueue::watchPopQueue 监控程序使用
     */
    public function blockpop()
    {
        $key = Request::Route('queuekey');
        self::blockPopQueue($key);
    }
    
    /**
     * 退出pop进程
     */
    public function kill()
    {
        $key = Request::Route('queuekey');
        self::killPopQueue($key);
    }
    
    /**
     * 拉起pop进程
     * 浏览器访问时不会主动退出, 可随时停止访问, 被拉起的linux pop进程不会退出
     * 配合crontab 或 supervisor等进程监控程序使用 1 * * * * /bin/sh php7 -q cli/queue/watch/queuekey/xxx
     */
    public function watch()
    {
        $key = Request::Route('queuekey');
        self::watchPopQueue($key);
    }
    
    /**
     * 重启pop进程
     * 浏览器访问时不会主动退出, 可随时停止访问, 被拉起的linux pop进程不会退出
     */
    public function restart()
    {
        $key = Request::Route('queuekey');
        self::killPopQueue($key);
        self::watchPopQueue($key);
    }
    
    
}