<h2>路由</h2>

<h3>默认module, controller, action</h3>
<pre>
::/www.yoursite.com/                ==> ::/www.yoursite.com/index/index/index

::/www.yoursite.com/test            ==> ::/www.yoursite.com/test/index/index

::/www.yoursite.com/test/foo        ==> ::/www.yoursite.com/test/foo/index

::/www.yoursite.com/test/foo/bar    ==> ::/www.yoursite.com/test/foo/bar
</pre>

<h3>路径参数</h3>
<pre>
::/www.yoursite.com/test/foo/bar/a/1/b/2    ==> ::/www.yoursite.com/test/foo/bar?a=1&b=2
::/www.yoursite.com/test/foo/bar/a/1/b/     ==> ::/www.yoursite.com/test/foo/bar?a=1&b=0  //不成对的参数最后补0
</pre>

<h3>正则参数</h3>

<h4>域名匹配</h4>
<pre>
//配置文件:::子域名路由配置 /config/RouteConfig::$Domain
['love_(\d+)_([a-z]+)'] = 'test/index/love/id/$1/name/$2';

//如下域名则会匹配到的参数为:
::/love_4_ever.yoursite.com/
	module      => test
	controller  => index
	action      => love
	id          => 4
	name		=> ever
</pre>
注: 用于有多个子站点的网站: apple.site.com bananer.site.com等等

<h4>URI路径匹配</h4>
<pre>
//配置文件:::路径(URI)路由配置 /config/RouteConfig::$Path
['#love_(\d+)_([a-z]+)#'] = 'index/index/love/id/$1/name/$2';

//如下URL会匹配到参数为:
::/www.yoursite.com/love_4_ever
	module      =>index
	controller  =>index
	action      =>love
	id          =>4
	name        =>ever
</pre>

<p>注意: 如果同时有域名和URI都匹配到了, URI匹配到的数据会覆盖掉通过域名匹配到的数据</p>
<p>如果路径(URI)路由匹配到, 就不再走默认的路由</p>
<p>不管域名有没有正则匹配到, 如果路径(URI)没匹配到的话, 还是会走默认路由的</p>
<p>也就是说, 优先级为:路径(URI)正则匹配 > 默认路由匹配 > 域名正则匹配</p>