<?php
/**
 * 视图类
 * 可以设置两个数组,存放预先加载和随后加载的视图文件名
 */
class View 
{
	public static $instance = false;
	public static $prefix = '';
	public static $module = '';
	public static $controller = '';
	public static $action = '';
	public static $arrSysVar = array();
	public static $arrTplVar = array(); //存储模板变量


	public static $preshow = array();
	public static $endshow = array();

	//单例模式
	public static function getInstance($module, $controller, $action, $arrSysVar)
	{
		if (!View::$instance) {
			View::$instance = new View($module, $controller, $action, $arrSysVar);
		}
		return Main::$instance;
	}

	private function __construct($module, $controller, $action, $arrSysVar)
	{
		self::$module 		= $module;
		self::$controller 	= $controller;
		self::$action 		= $action;
		self::$arrSysVar 	= $arrSysVar;
		self::$prefix 		= VIEWPATH.self::$module.'/';
	}
	
	//备用初始化
	public static function init($module, $controller, $action, $arrSysVar)
	{
		self::$module 		= $module;
		self::$controller 	= $controller;
		self::$action 		= $action;
		self::$arrSysVar 	= $arrSysVar;
		self::$prefix 		= VIEWPATH.self::$module.'/';
	}

	//显示到浏览器
	//可以重写该方法, 多次调用fetch()来渲染多个页面, 如后台开发的时候,
	//顶部/左侧菜单栏/底部 可以统一渲染, 每次只用传入body页面
	public static function show($filename='')
	{
		$filename = empty($filename) ? self::$action : $filename;
		// header('Content-Type: ---'.'; charset=utf-8');
		// header('Cache-control: ---');
		// header('X-Powered-By:zhangzhibin');
		$preshow = implode('', self::$preshow);
		$endshow = implode('', self::$endshow );
		$content = $preshow . self::fetch($filename) . $endshow;
		exit($content);
	}

	public static function display($filename='')
	{
		self::show($filename);
	}
	
	//输出内容到变量
	public static function fetch($filename='')
	{
		$filename = !empty($filename) ? $filename : self::$action;
		$filepath = self::$prefix.$filename.TPL_FILE_EXTENSION;
		
		extract(self::$arrTplVar, EXTR_OVERWRITE); //将普通变量置为全局可访问, 覆盖之前有的
		extract(self::$arrSysVar, EXTR_OVERWRITE); //将系统变量置为全局可访问, 覆盖之前有的

		ob_start();
		ob_implicit_flush(0);

		//渲染传入的模版
		require_once($filepath);

		$content = ob_get_contents(); //输出到变量, 并清除缓存
		ob_end_clean();
		return $content;
	}

	public static function preshow($filename='')
	{
		if (empty($filename)) {
			self::$preshow = array();
		} else {
			self::$preshow[] = self::fetch($filename);
		}
	}

	public static function endshow($filename='')
	{
		if (empty($filename)) {
			self::$endshow = '';
		} else {
			self::$endshow[] = self::fetch($filename);
		}
	}
}
