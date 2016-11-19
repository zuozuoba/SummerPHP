<h2>css/js/等静态文件</h2>

<p>需要配置nginx服务器进行路由重写</p>
<pre>
     location ~ \.ico|jpg|gif|png|js|css|txt|csv|woff2|zip$ {
            root /path/to/docroot/static;
            #expires 1h;
        }
</pre>
<p>这里static目录没有跟PHP代码(modules)放在一起, 服务器资源比较充足的时候可以把这个文件夹部署到其它机器上去, 做CDN什么的</p>