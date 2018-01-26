<?php
/**
 * 视图类
 * 可以设置两个数组,存放预先加载和随后加载的视图文件名
 */
class View 
{
	public static $instance = false;
	public static $arrSysVar = array();
	public static $arrTplVar = array(); //存储模板变量

	public static $preshow = array();
	public static $endshow = array();

	private function __construct()
	{}
	
	//备用初始化
	public static function init($arrSysVar)
	{
		self::$arrSysVar = $arrSysVar;
	}

	/**
     * 显示到浏览器
	 * 可以重写该方法, 多次调用fetch()来渲染多个页面, 如后台开发的时候,
	 * 顶部/左侧菜单栏/底部 可以统一渲染, 每次只用传入body页面
     * 
     */
	public static function show($filename='')
	{
		//获取渲染后的boday
		$filename = empty($filename) ? ACTION_NAME : $filename;
		$path = self::getRealPath($filename);
		$mainContent = self::fetch($path);

		//获取渲染后的header
		$preshow = '';
		foreach (self::$preshow as $v) {
			$path = self::getRealPath($v);
			$preshow .= self::fetch($path);
		}

		//获取渲染后的footer
		$endshow = '';
		foreach (self::$endshow as $v) {
			$path = $path = self::getRealPath($v);
			$endshow .= self::fetch($path);
		}

		//整合输出
		exit($preshow . $mainContent . $endshow);
	}

	public static function getRealPath($filename)
	{
		if (strpos($filename, '/')) {
			return VIEWPATH.$filename.TPL_FILE_EXTENSION;
		} else {
			return VIEWPATH.MODULE_NAME.'/'.$filename.TPL_FILE_EXTENSION;
		}
	}

    /**
     * 没有预渲染功能的显示函数
     * 用于显示登陆页或活动页等风格独特的页面
     */
	public static function display($filename='')
	{
		$filename = empty($filename) ? ACTION_NAME : $filename;
		$path = self::getRealPath($filename);
		$content = self::fetch($path);
		exit($content);
	}
	
	//输出内容到变量
	public static function fetch($filepath)
	{
		//将变量置为本函数可访问(用于解析模版)
		extract(array_merge(self::$arrSysVar, self::$arrTplVar));

		ob_start();
		ob_implicit_flush(0);

		//渲染传入的模版
		include_once($filepath);

		$content = ob_get_contents(); //输出到变量, 并清除缓存
		ob_end_clean();
		return $content;
	}

	//记录提前预渲染文件名
	public static function preShow($filename='')
	{
		if (empty($filename)) {
			self::$preshow = array();
		} else {
			self::$preshow[] = $filename;
		}
	}

	//记录最后渲染的文件名
	public static function endShow($filename='')
	{
		if (empty($filename)) {
			self::$endshow = array();
		} else {
			self::$endshow[] = $filename;
		}
	}
}
