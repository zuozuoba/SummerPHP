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
	
更新日期 2017-7-10
    