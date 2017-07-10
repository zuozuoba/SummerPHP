# SummerPHP
模块扁平化, 调用简单, 小巧的PHP框架
文档: http://www.zhangzhibin.com/

# 目录结构

> core    框架的核心类

> libs    第三方库

> config  配置文件

> model   模型类, 放这里是为了任何控制器都可以访问到,减少复杂度

> modules 项目模块, 存放controller

> view    视图文件

> static  静态文件(js,css,img...)

> index.php   入口文件

> cli.php 命令行下的入口文件 php cli.php -q m/c/a/

#用法参考
### URL格式
    //加载默认的代码
    http://www.test.com/
    
    //加载指定的代码
    http://www.test.com/module/controller/action/key1/value1/key2/value2
    
    //URI路由, 例如: 获取文章列表的第二页(需要在配置文件写一行对应关系)
    http://www.test.com/article_list_2
    
    //二级域名路由, 例如跳转到不同的子站(需要在配置文件写一行对应关系)
    http://book.test.com/
    http://fruit.test.com/
    
### 控制器:
##### 命名
    <?php
    class _article extends BaseAdmin //注意,控制器类名前是有下划线的，BaseAdmin在同一目录下
    {}

##### 位置
    modules/admin/article.php

### 视图:
##### 命名
    login.html
##### 位置
    view/user/login.html
##### 对应控制器代码
    View::show('login');
    位置: modules/user/login.php
##### 加载其它目录的视图文件
    View::show('public/header');
    位置: view/public/header.html
##### 模版赋值
    View::$arrTplVar['aaa'] = '123';
##### 自动预渲染
    //渲染login.html之前, 先渲染, header.html nav.html sidebar.html
    View::preShow('public/header');
    View::preShow('public/nav');
    View::preShow('public/siderbar');
    
    //渲染login.html之后, 再渲染footer.html
    View::endShow('public/footer');
    
    //注意: 假如页面主体内容是写在login.html中的,
    //那么, action中只用写一行View::show('login');
    //程序会自动先渲染View::preShwo()函数指定的模版
    //然后再渲染lgoin.html模版
    //最后又自动渲染View::endShow()函数指定的模版
    //而View::preShow()和View::endShow()必须要在action执行前提前执行
##### 不使用预渲染
    //单独渲染login.html页面
    View::display('login');

### 配置文件:
##### 命名
    不限制, 以.php结尾
    例Config.php
##### 位置
    config/pro/...
    config/dev/....
##### 加载方式
    Config::$a
    DBConfig::$a
    RouteConfig::$a

### 模型(数据库):
##### 命名
    <?php
    class Article extends Model
    {}
##### 位置
    model/Article.php
##### 数据库
###### select
    //查询多行
	$rs = Article::link('article_cats')
        ->setWhere(['status' => '> -1'])
        ->setOrder('create_time desc')
        ->select();
    //查询一行
    Article::link('article_cats')->setWhere(['id' => $id])->getOne();
   
    
###### insert
        $article_cats = [
            'title'		=> self::getData('title'),
            'status'	=> Admin::$Status_Hide,
            'create_time'	=> date('Y-m-d H:i:s')
        ];

        $rs = Article::link('article_cats')->insert($article_cats);
###### update
    $article_cats = [
        'title'		=> self::getData('title'),
        'update_time'	=> date('Y-m-d H:i:s')
    ];
    Article::link('article_cats')->setWhere(['id' => $id])->update($article_cats);
###### replace
###### delete
###### count
     Article::link('article')->setWhere(['cat_id' => $id, 'status' => '> 1'])->getCount();

### 第三方库:
##### 命名
    不限制, 以.php结尾
    Fun.php
##### 位置
    /libs
##### 使用方法
    Fun::out();
    Fun::redirect();
##### 公共类
    Fun.php //公共方法
    IRedis.php //redis类
    QiNiu.php //七牛云存储
    QQIM.php //QQ云通信
    NEIM.php //网易云通信
