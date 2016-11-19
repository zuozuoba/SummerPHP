<h2>Hello World</h2>

<h3>1. 创建控制器文件</h3>
<pre>
/modules/index/controller/index.php
</pre>

<h3>2. 编辑控制器类代码</h3>
<pre>
&lt;?php
	class _index extend Main  //注意, 以下划线开头
	{
		public function index()
		{
			View::$arrTplVar['foo'] = 'Hello World!';
			View::display();
		}
	}
</pre>

<h3>3. 创建模版文件</h3>
<pre>
/modules/index/views/index/index.php
</pre>

<h3>4. 编辑模版代码</h3>
<pre>
&lt;?= $foo ?&gt;
</pre>

<h3>5. 浏览器访问</h3>
<pre>
::/www.yourwebsit.com/
</pre>
