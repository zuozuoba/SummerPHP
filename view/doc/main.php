<h2>控制器基类(Main)</h2>

<h3>代码文件的位置</h3>
<pre>
/core/Main.php
</pre>

<h3>作用</h3>
<ol style="list-style: decimal">
	<li>初始化数据
		<ol style="list-style: circle">
			<li>记录URL中匹配到的参数</li>
			<li>记录路由中匹配到的参数</li>
			<li>记录当前实际访问的模块名, 控制器名, 方法名以及分别对应的URL</li>
			<li>是否是post请求</li>
			<li>是否是手机</li>
			<li>请求到达的时间戳</li>
		</ol>
	</li>
	<li>提供获取数据的方法</li>
	<li>初始化视图类(将初始化的数据赋值到视图类中, 可以在模版中直接访问)</li>
	<li>加载并调用控制器代码</li>
</ol>

<h3>获取请求数据</h3>
<pre>
class _index extends Main
{
	/**
	 * 文章详情页
	 */
	public function article()
	{
		$articleid = $this->getData('articleid', Safe::$Check_INT);
	}
}
</pre>
<p>
	注意: <br>
	1. 这里的getData()函数是获取所有参数的函数, _GET, _POST, URL匹配的参数, 路由匹配的参数等;<br>
	2. 第二个参数是对数据安全性进行验证, 这里是验证id是否是整数, 如果不传, 则会进行默认的安全性验证;<br>
		如果想同时验证两者可以传入: "Safe::$Check_INT | Safe::$Check_Default" (逻辑与操作)
</p>

<h3>URL路径参数</h3>
<pre>
http://www.com/test/index/index/articleid/123/type/1
</pre>
<p> 这里的我们从URL中获取的参数有: </p>
<ol style="list-style: decimal">
	<li>$this->module: test</li>
	<li>$this->controller: index</li>
	<li>$this->action: index</li>
	<li>$this->moduleUrl: http://www.com/test/</li>
	<li>$this->controllerUrl: http://www.com/test/index/</li>
	<li>$this->actionUrl: http://www.com/test/index/index/</li>
	<li>$articleid: 123</li>
	<li>$type: 1</li>
</ol>

<p>其中, $article 和 $type 在视图模版中用法为: </p>
<pre>
&lt;?= $article.'-'.$type ?&gt;
</pre>

<p>在控制器中: </p>
<pre>
$articleid  = $this->getData('articleid', Safe::$Check_INT);
$type       = $this->getData('type', Safe::$Check_INT);
</pre>