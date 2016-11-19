<h2>控制器的使用</h2>

<h3>控制器类文件的位置</h3>
<p>比如有一个名叫"test"的模块, 他的路径应该是: </p>
<pre>
/modules/test/
</pre>

<p>如果我有一个叫"index"的控制器, 那么他应该在:</p>
<pre>
/modules/test/index.php
</pre>
<p>其中控制器文件的后缀是".php", 控制器文件的名字区分大小写, 建议都用小写, 省的添麻烦</p>


<h3>控制器类的命名规则</h3>
<pre>
class _index extends Main
{
	public function initc()
	{

	}

	public function index()
	{
		View::$arrTplVar['a'] = 111; //给模版变量a赋值为111
		View::show('test');  //显示test模版, 留空默认显示index模版
		// View::display(); //show的别名
	}
}
</pre>
<p>
	注意: <br>
	1. 如果我的控制器文件名叫"index.php", 那么类的名字就是"_index", 前边有一个下划线;<br>
	2. 如果有需要默认执行的代码, 放到 initc() 方法中;<br>
	3. 这样做主要是因为PHP面向对象的特性, 如果有构造函数, 或者有的方法名跟类名相同的时候,<br>
	   如果不调用父类的构造函数, 父类里构造函数产生的数据都会被覆盖;<br>
	4. 所有的控制器都要继承"Main"这个"总控制器(父类)";
</p>