<h2>视图</h2>

<h3>模版文件存放的位置</h3>
<pre>
//如果模块的路径是:
/modules/test/

//那么视图文件存放的路径就是:
/modules/views/test/xxx.php

</pre>

<h3>模版文件命名</h3>
<pre>
//文件名无所谓, 按照通常的命名方法就好, 但是后缀名是可以自定义的:

//框架的入口文件index.php中有全局的宏定义
define('TPL_FILE_EXTENSION', '.php'); //如果这样定义, 那所有的模版文件的后缀都应该以".php"结尾

//如果想要后缀为".html" 方便前端使用:
define('TPL_FILE_EXTENSION', '.html');

后缀这里没有做兼容处理, 选择太多不如只有一种选择
</pre>

<h3>渲染模版的函数</h3>
<pre>
View::show('foo'); //用静态调用一是为了减少代码量, 二是不再new, 不再频繁的向操作系统申请内存

//或者
View::display('foo');
</pre>

<h3>控制器中渲染模版</h3>
<pre>
//1. 模版名字跟action名字是一样的, 这样就可以不用传递参数
public function foo() //action名字是'foo'
{
	View::show(); // 也可以这样写: $this->show('foo');
}

//2. 模版名字跟action	名字不一样
public function foo()
{
	View::show('foo_body'); //此时就必须传入参数了
}

</pre>