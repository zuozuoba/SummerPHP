# SummerPHP
##### a small, concise and more static call PHP framework
##### documents: http://doc.hearu.top/index.html

# project list
```
Summer PHP Framework
|-- core    core of this framework
|-- config  configure file
|-- libs    other tool class
|-- model   logic of get data 
|-- modules all controllers class here
|-- view   tpl directory
|-- static  static file like css, js, image etc.
|-- cli.php  interface of cli php cli.php -q m/c/a/
`-- index.php   interface of web
```

# main function usage reference
### format of URL to visit the action
```
//run the default action: /index/index/index
http://www.test.com/

//run the other action module/controller/action with params key1 and key2
http://www.test.com/module/controller/action/key1/value1/key2/value2

//use uri to route(rewrite to the real action use short writing), 
// for example: to get the second page of article list 
// now you need to add a key=>value item in the array of file RoutConfig: member variable array $Path
http://www.test.com/article_list_2  //which the key=>value item is 'article_list_(\d+)' => 'index/index/route/page/$1'

//use second-level domain to route , 
//for example: you need redirect to the different sub site accroding to the different second-level domain 
// and you need to add a key=>value item in the array of file RoutConfig: member variable array $Domain
http://doc.test.com/  //which the key=>value item is 'doc' => 'doc/index/index'
```    
### get the request params
```
Request::Get('a', 'default');
Request::Post('a');
Request::Cookie('a');
Request::Route('a');
```

### get and format the data from mysql
```
$rs = Test::link('note')->fields('id,content')
        ->whereGE('id', 1)
        ->limit(10)
        ->select()
        ->getAll();
    echo '<pre>';var_dump($rs, Test::$currentSql);
   
$sql = 'select * from user';
Test::link('user')
         ->query($sql)
         ->data()
         ->array_column('age', 'username')
         ->array_sum()
         ->pre();
```

### Redis message queue

```
example here : /modules/cli/queue.php

1. push to the queue which name is 'redisKey'
代码调用Lib: RedisQueue::pushQueue(redisKey, Json)
其中Json: _class和_method是必传的, 用于回调
 
2. pop, 阻塞出队列
> URL: www.summer.com/cli/queue/blockpop/queuekey/{redisKey} (直接启动一个新的进程)
> URL: www.summer.com/cli/queue/watch/queuekey/{redisKey} (有同名的进程就不再启动新的)

> CLI: php cli.php -q cli/queue/blockpop/queuekey/{redisKey} (直接启动一个新的进程)
> CLI: php cli.php -q cli/queue/watch/queuekey/{redisKey} (有同名的进程就不再启动新的)

3. 监控pop进程, 挂掉后拉起(需要在cli下配合crontab或者supervisor使用)
crontab: 1 * * * * php cli.php -q cli/queue/watch/queuekey/{redisKey}

4. restart, 如果更新代码后需要重启, 重新pop
> URL: www.summer.com/cli/queue/restart/queuekey/{redisKey}
> CLI: php cli.php -q cli/queue/restart/queuekey/{redisKey}

 注意: 
1. 通过浏览器里访问来触发相关命令的, 访问后会出现一直等待响应到超时的情况
不用担心, 访问后关掉页面即可, 因为pop程序是while(true), 已经在后台运行了

2. 如果不传递queuekey(队列名字), 则使用默认队列名字

```
    
