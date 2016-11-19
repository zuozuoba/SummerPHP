<?php
class Load
{
	public static $libs = [];
	public static $models = [];
	
	//autoload用, 加载核心类文件
	//参数可以是路径 a/b/c 则会寻找core/a/b/c.php
	public static function Core($name)
	{
		$name = ltrim($name, '/');
		$realpath = COREPATH.$name.PHP_FILE_EXTENSION;
		if (file_exists($realpath)) {
			include_once($realpath);
		} else {
			self::error('FIEL NOT EXISTS!');
		}
	}


	//autoload用, 加载库文件
	//参数可以是路径 a/b/c 则会寻找libs/a/b/c.php
	public static function Lib($name)
	{
		$name = ltrim($name, '/');
		$realpath = LIBPATH.$name.PHP_FILE_EXTENSION;
		if (file_exists($realpath)) {
			include_once($realpath);
		} else {
			self::error('FIEL NOT EXISTS!');
		}
	}
	
	//autoload用, 加载配置文件
	//参数可以是路径 a/b/c 则会寻找config/a/b/c.php
	public static function Config($name)
	{
		// 将配置文件放在不同的目录 由全局变量ENV来确定
		$realpath = CONFIGPATH.ENV.'/'.$name.PHP_FILE_EXTENSION;
		if (file_exists($realpath)) {
			include_once($realpath);
		} else {
			self::error('FIEL NOT EXISTS!');
		}
	}
	
	//autoload用, 只加载Model文件, 并不会实例化
	//参数可以是路径 a/b/c 则会寻找model/a/b/c.php
	public static function Model($name)
	{
		$realpath = MODELPATH.$name.PHP_FILE_EXTENSION;
		if (file_exists($realpath)) {
			require_once($realpath);
		} else {
			self::error('FIEL NOT EXISTS!');
		}
	}

	//获取libs下的库文件
	//参数可以是路径 a/b/c 则会寻找libs/a/b/c.php
	public static function getLib($name, $arg = '')
	{
		$name = ltrim($name, '/');
		if (empty(self::$libs[$name])) {
			$realpath = LIBPATH.$name.PHP_FILE_EXTENSION;
			if (file_exists($realpath)) {
				include_once($realpath);
			}
			$arrClassName = explode('/', $name );
			$strClassName = end($arrClassName);

			self::$libs[$name] = new $strClassName($arg);
		}

		$obj = &self::$libs[$name];
		if (method_exists($obj, 'init')) {
			$obj->init($arg);
		}
		return $obj;
	}

	//是Model的别名, 但是调用了init()函数
	//实例化了对应的Model类
	public static function getModel($name, $arg='')
	{
		if (empty(self::$models[$name])) {
			$realpath = MODELPATH.$name.PHP_FILE_EXTENSION;
			if (file_exists($realpath)) {
				include_once($realpath);
				$arrClassName = explode('/', $name );
				$strClassName = end($arrClassName);

				self::$models[$name] = new $strClassName($arg);
				// 重新调用初始化函数, 给一次机会
				if (method_exists(self::$models[$name], 'init')) {
					self::$models[$name]->init($arg);
				}
				return self::$models[$name];
			} else {
				self::error('FIEL NOT EXISTS!');
			}
		}
	}


	public static function error($msg)
	{
		//if IS_TEST
		return json_encode(array('code' => '-1', 'msg' => $msg));
	}

}