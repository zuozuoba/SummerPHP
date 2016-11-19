<link rel="stylesheet" href="<?= $docui ?>css/sider.css">
<div class="am-g">
	<div class="am-u-lg-3 am-u-md-3 am-u-sm-3" style="border-right:1px solid lightgrey">
		<ul class="am-nav am-link-muted">
			<li class="am-nav-header">开始</li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>">简介</a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>hello">快速入门<br><span class="am-nav-en am-text-xs"></span></a></li>
			
            <li class="am-nav-header">控制器</li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>controller">基本使用<br><span class="am-nav-en am-text-xs">extents Main</span></a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>maind">核心控制器<br><span class="am-nav-en am-text-xs">/core/Main.php</span></a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>safe">安全<br><span class="am-nav-en am-text-xs">Safe::check(param, item)</span></a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>router">路由<br><span class="am-nav-en am-text-xs">Route(domin, uri)</span></a></li>

			<li class="am-nav-header">视图</li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>view_base">基本使用<br><span class="am-nav-en am-text-xs">$this->show('body')</span></a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>view_pre">预渲染<br><span class="am-nav-en am-text-xs">$this->preshow() &nbsp; $this->endshow()</span></a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>view_static">css/js<br><span class="am-nav-en am-text-xs"></span></a></li>

			<li class="am-nav-header">数据库连接</li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>db">基本使用<br><span class="am-nav-en am-text-xs">Test::link('user')->get()</span></a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>db_conf">配置文件<br><span class="am-nav-en am-text-xs">DBConfig::getDBInfo()</span></a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>db_get">获取记录<br><span class="am-nav-en am-text-xs">get();getOne();getOneField();getFields()</span></a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>db_where">Where条件<br><span class="am-nav-en am-text-xs">setWhere**()</span></a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>db_add">插入<br><span class="am-nav-en am-text-xs">insert(['age'=>1]) insertm('id,age',[[1,1],[2,2]])</span></a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>db_up_del">更新/删除<br><span class="am-nav-en am-text-xs">update(['age'=>'age+1']) delete()</span></a></li>

			<li class="am-nav-header">辅助函数</li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>other_getdoc">获取代码注释信息<br><span class="am-nav-en am-text-xs">Fun::getMethodDoc()</span></a></li>
			<li class="li-sider-item"><a href="<?= $controllerUrl ?>other_fun">其它<br><span class="am-nav-en am-text-xs">Fun::foo()</span></a></li>
		</ul>
	</div>
	<div class="am-u-lg-9 am-u-md-9 am-u-sm-9" >

