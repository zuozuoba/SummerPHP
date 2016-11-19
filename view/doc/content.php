<!--右侧内容-->
<h3>下载</h3>
<a href="<?= $baseUrl?>src/Finger.zip">Finger_UTF-8.zip(2016-07-08更新)</a>
<h3>目录结构</h3>
<pre>
Summer PHP Framework
|-- core    框架的核心类
|-- libs    第三方库
|-- config  配置文件
|-- model   模型类, 理论上用于写获取数据的具体逻辑, 只放置在根目录下, 任何控制器都可以调用到
|-- modules 项目模块, 存放controller
|-- view    视图/模板文件
|-- static  静态文件存放
`-- index.php   入口文件
</pre>

<h3>详细结构</h3>
<pre>
Finger PHP Framework
|-- core    框架的核心类
|   |--Main.php     //核心类, 所有控制器都要继承她
|   |--View.php     //视图类, 显示html页面
|   |--Load.php     //加载类, 加载core, libs, model
|   |--Route.php    //路由类, 支持二级域名路由以及URI
|   |--DBmysql.php  //mysql类
|   |--Safe.php     //安全类
|   `--Model.php    //模型类, 本框架弱化了模型类功能
|
|-- config  配置文件
|   |--dev
|   |   |-- Config.php  //普通配置文件
|   |   `--DBConfig.php //数据库配置文件
|   `--pro
|
|-- libs    第三方库
|   |--Fun.php      //功能函数集合
|   |--Area.php     //省市联动
|   |--iredis.php   //redis
|   |--QiNiu.php    //七牛云存储
|   |--QQ.php       //QQ第三方登录SDK
|   |--QRcode.php   //二维码
|   |--Pinyin       //拼音转汉字
|   `-- ...
|
|-- model 模型类, 理论上用于写获取数据的具体逻辑, 只放置在根目录下, 任何控制器都可以调用到
|   |--Test.php     //Test模型
|   `--User.php
|
|-- modules 项目模块
|   |-- index
|   |   |-- index.php //index控制器
|   |   `-- login.php //login控制器
|   `-- test
|
|-- view      //存放所有视图模版
|   |-- index
|   |   |--header.php //页面顶部模版
|   |	|--body.php //页面内容模版
|   |   `--footer.php //页面底部模版
|   `-- login
|       `--header.php
|
|-- static  静态文件存放
|   |--myUI
|   |   |--css
|   |   |   |--common.css
|   |   |   |`--app.css
|   |   |--js
|   |   |   |--common.js
|   |   |   `--app.js
|   |   `--image
|   |       |--common.png
|   |       `--app.png
|   `--otherUI
`-- index.php   //入口文件
</pre>
