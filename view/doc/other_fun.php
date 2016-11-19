<h2>辅助函数</h2>

<h3>获取客户端ip</h3>
<pre>
    Fun::getClientIp();
</pre>

<h3>获取服务器ip</h3>
<pre>
    Fun::getServerIp();
</pre>

<h3>curl</h3>
<pre>
    Fun::curlPost();
    Fun::curlGet();
    Fun::curlPostRetry(); //带有重试机制
    Fun::curlGetRetry(); //带有重试机制
</pre>

<h3>生成随机字符</h3>
<pre>
    Fun::randCode();
</pre>

<h3>匹配汉字</h3>
<pre>
    Fun::getCn();
</pre>

<h3>二维数组排序</h3>
<pre>
    Fun::sort2DArray();
</pre>

<h3>数字去重并排序</h3>
<pre>
    Fun::BitMapSort(); //使用的是位图排序算法, 默认支持最大6400以内的数字排序
</pre>