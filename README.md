# SummerPHP
##### 模块扁平化, 调用简单, 小巧的PHP框架
##### 参考文档: http://doc.hearu.top/index.html

# 目录结构
```
Summer PHP Framework
|-- core    框架的核心类
|-- config  配置文件
|-- libs    第三方库
|-- model   模型类, 理论上用于写获取数据的具体逻辑, 只放置在根目录下, 任何控制器都可以调用到
|-- modules 项目模块
|-- view   视图文件
|-- static  静态文件存放
|-- cli.php  命令行下的入口文件 php cli.php -q m/c/a/
`-- index.php   入口文件
```

# 主要用法参考
### URL访问格式
```
//加载默认的代码
http://www.test.com/

//加载指定的代码
http://www.test.com/module/controller/action/key1/value1/key2/value2

//URI路由, 例如: 获取文章列表的第二页(需要在配置文件RoutConfig::$Path数组里写一行对应关系)
http://www.test.com/article_list_2  //对应路由规则 'article_list_(\d+)' => 'index/index/route/page/$1'

//二级域名路由, 例如跳转到不同的子站(需要在配置文件RouteConfig::$Domain数组里写一行对应关系)
http://doc.test.com/  //对应路由规则 'doc' => 'doc/index/index'
```    
### 获取参数
```
Request::Get('a', 'default');
Request::Post('a');
Request::Cookie('a');
Request::Route('a');
```

### 查询数据库
```
$rs = Test::link('note')->fields('id,content')
        ->whereGE('id', 1)
        ->limit(10)
        ->select()
        ->getAll();
    echo '<pre>';var_dump($rs, Test::$currentSql);
```

### Redis消息队列

```
例子在: /modules/cli/queue.php

1. push, 入队列
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
    