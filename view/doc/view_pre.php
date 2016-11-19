<h2>预渲染</h2>

<h3>用途</h3>
一个网页经常分为四个部分:
<ol style="list-style: decimal">
	<li>顶部导航(header)</li>
	<li>两边菜单栏(sidebar)</li>
	<li>主要内容(content)</li>
	<li>底部友情链接(footer)</li>
</ol>

<p>而其中第1, 2, 4是固定不变的, 只有第3个"content"是变化的, 就像一个网站的管理后台那样</p>
<p>为了方便, 把不变化的header, sidebar, footer抽出来, 由特定的函数隐式的去渲染</p>
<p>而我们在敲代码渲染的时候只用渲染content, 这样就减轻了代码量, 而且方便管理</p>


<h3>如何使用</h3>
<p>加入我们吧一个页面拆分成四部分: header.php, sidebar.php, content.php, footer.php那么代码如下:</p>
<pre>
class _index extends Main
{
	public function initc()
	{
		View::preshow('header');
		View::preshow('sidebar');
		View::endshow('footer');
	}

	public function artcile_detail()
	{
		View::show('content');
	}

	public function goods_detail()
	{
		View::show('content');
	}

	public function other()
	{
		View::preshow(); //空参数则不再渲染header.php 以及 sidebar.php
		View::ednshow(); //空参数则不再渲染footer.php
		View::show('foo');
	}
}
</pre>
<ol>
	注意几点:
	<li>因为方法initc()是必定会执行的, 因此预渲染动作就放在这里执行</li>
	<li>在article_detail和goods_detail两个action中只用渲染content就可以了</li>
	<li>other()这个action如果不想预渲染, 只用传递空参数就可以了</li>
</ol>

