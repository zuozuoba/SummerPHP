<?php
class Main
{
	public static  $instance = false;
	
	public $data = array();

	public $document_uri = ''; //不包含 '?'及其以后的请求参数
	public $request_uri = '';
	public $subdomain = '';

	public $module = false;
	public $controller = false;
	public $action = false;
	
	public $baseUrl 		= false;
	public $moduleUrl 		= false;
	public $controllerUrl 	= false;
	public $actionUrl 		= false;

	public $route = '';
	public $isRouteMatch = false;
	public $arrRouteArg = array();

	public $ispost = false;
	public $ismobile = false;

	public $view = null; //对象

	public $pathinfo = false;
	public $pathdata = array();

	public $intVisitTime = '';
	public $floatVisitTime = '';
	
	//单例模式
	public static function getInstance() 
	{
		if (!Main::$instance) {
			Main::$instance = new Main();
		}
		return Main::$instance;
	}

	// 注意: 这个构造函数里的任何一个函数
	// 在构造函数执行结束之前不能 直接或间接的调用本构造函数
	// 否则会出现无限递归调用
	private function __construct() 
	{
		$arrHost = explode('.', DOMAIN);
		$this->subdomain = $arrHost[0];

		$this->document_uri = str_replace('.html', '', DOCUMENT_URI);
		$this->document_uri = trim($this->document_uri, '/'); //去掉URI两边的斜线
		$this->document_uri = Safe::check($this->document_uri, Safe::$Check_DEFAULT);

		//路由, 分析URL,初始化变量,供其它成员函数使用
		$this->route();
		//初始化常用成员变量
		$this->initSysVar();
		//初始化视图变量
		$this->initView();

		//不能像下边这样写
		// $this->fun(); 其中,fun() {....; Main::getInstance(); ....} 
		// 因为单例模式只会在完整执行一次构造函数后才生成一个单例
		// 在完整执行一次构造函数前, 存放单例的变量Main::$Instance的值仍然是false
		// 也就是说执行$this->fun()中的Main::getInstance()时,仍然会再次调用构造函数
		// 如此往复, 导致Main::getInstance()一直不能执行 return 语句而无限递归下去
	}
	
	public function __destruct()
	{}
	
	//可以自己实现路由类, 只要要返回 module, controller, action, 以及匹配到的参数就行了
	//返回是否匹配了路由, 匹配路由的名字, 路由指向的module,controller,action, 路由错误信息,方便调试
	public function route()
	{
		$this->route = new Route($this->subdomain, $this->document_uri);
		
		$this->isRouteMatch = $this->route->ismatch;

		$this->module 		= $this->route->module;
		$this->controller 	= $this->route->controller;
		$this->action 		= $this->route->action;

		$this->setData();
	}
	
	//初始化常用的系统变量
	public function initSysVar()
	{
		$this->baseUrl 		= BASEURL;
		$this->moduleUrl 	= $this->baseUrl.$this->module.'/';
		$this->controllerUrl = $this->moduleUrl.$this->controller.'/';
		$this->actionUrl 	= $this->controllerUrl.$this->action.'/';

		$this->intVisitTime = REQUEST_TIME;
		$this->floatVisitTime = REQUEST_TIME_FLOAT;

//		$this->ismobile = $this->isMobile();
	}
	
	//获得get,post,url中的数据
	private function setData()
	{
		$this->data = array_merge($_COOKIE, $_GET, $_POST, $this->route->args);
		$this->ispost = count($_POST);
	}

	//获取请求数据
	public function getData($name, $safe='')
	{
		$safe = empty($safe) ? Safe::$Check_DEFAULT : $safe;
		return isset($this->data[$name]) ? Safe::check($this->data[$name], $safe) : NULL;
	}
	
	//初始化视图插件
	public function initView()
	{
		$arrSysVar = array(
			'baseUrl' 		=> $this->baseUrl,
			'moduleUrl' 	=> $this->moduleUrl,
			'controllerUrl' => $this->controllerUrl,
			'actionUrl' 	=> $this->actionUrl,
			);
		View::init($this->module, $this->controller, $this->action, $arrSysVar);
	}
	
	//包含请求的文件
	//new出controller对应的类
	//执行类的 initc()函数
	//执行action对应的函数
	public function run()
	{
		if ($this->action == 'initc') {
			Fun::notify('非法访问', $this->moduleUrl, '2');
		}

		//包含类文件
		include_once(MODULEPATH.$this->module.'/'.$this->controller.PHP_FILE_EXTENSION);
		
		//new类对象
		$controller = '_'.$this->controller;//注意此处控制器名前有下划线
		$obj = new $controller;
		
		//执行initc()默认函数
		if (method_exists($obj, 'initc')) {
			//由于不能写子类的构造函数因此每次都执行这个initc()函数
			//c是controller的意思,为了不覆盖掉main类里的init()
			$obj->initc();
		}
		//执行action
		call_user_func_array(array($obj, $this->action), $this->arrRouteArg);
	}
}