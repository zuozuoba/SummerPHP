# SummerPHP
##### a small, concise and more static call PHP framework
##### newest version: https://gitee.com/myDcool/SummerPHP
##### documents: http://doc.hearu.top/index.html

# project structure
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

Request::isPost();
Request::getClientIp();
...

```

### Response information
```
Response::error('wrong param', $a, 50001);
Response::success($a, 'user info list', 20000);

Response::ini()->code(10000)->msg('user info list')->data($a)->json(); //call through chain

Response::redirect('login ok, visit home page 3sec later', 'http://doc.hearu.top', 3); //wait 3sce then jump to other url
Response::notify('some text here ...');
```

### get and format the data from mysql
```
$rs = Test::link('note')->fields('id,content')
        ->whereGE('id', 1)
        ->order('id desc')
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

### file log
```
FileLog::ini('summer/phplog/proj1')->prefix('prefix description of log')->info('log content'); // log file name: yyyy-mm-dd.log

FileLog::ini('summer/phplog/proj1', 'test')->prefix('prefix description of log')->info('log content'); //log file name: test.log
```

### Redis message queue

example here : /modules/cli/queue.php

- core class: core/RedisQueue::class
- usage: /modules/cli/queue.php

#### 1. push to the queue which name is $redisKeyName
``` 
RedisQueue::pushQueue($redisKeyName, $params)
``` 
- the message push to the queue is $params, encode with method json_encode, there are some imporent field in $params:

|name|data type |explain|
|---|---|---|
|_class|string|callback class name|
|_method|string|callback member function name|
|other params|-|-|
 
#### 2. pop, pop from queue with block

##### trigger from url
```
www.hearu.top/cli/queue/blockpop/queuekey/{redisKeyName} (starup a new process)
www.hearu.top/cli/queue/watch/queuekey/{redisKeyName} (there already a samename process, no need to starup)
```

##### trigger from cli
```
php cli.php -q cli/queue/blockpop/queuekey/{redisKeyName} (starup a new process)

php cli.php -q cli/queue/watch/queuekey/{redisKeyName} (there already a samename process, no need to starup)
```
- when the message pop from the queue, the program will take out the value of _class and _method and execute _method 

#### 3. whatch the pop process , restart them if they were gone (use crontab or supervisor as a watcher)
```
crontab: 1 * * * * php cli.php -q cli/queue/watch/queuekey/{redisKeyName}
```

#### 4. restart, manualy restart the pop process (when you update the code)

##### from url
```
://www.hearu.top/cli/queue/restart/queuekey/{redisKeyName}
```

##### from cli
```
php cli.php -q cli/queue/restart/queuekey/{redisKeyName}
```

#### attention : 
- if you trigger some function (such as block pop) from url, usually, it takes a long time and the browser will notice the 504 error, 
don't worry, just close the browser tab, the process is already run successfully on the web server

- if you don't specify the queue name, the program will use the default name


    
