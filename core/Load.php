<?php
defined('ENV') || exit('illegal Access! @110');
class Load
{
	//autoload用, 加载核心类文件
	//参数可以是路径 a/b/c 则会寻找core/a/b/c.php
	public static function Core($name)
	{
		$name = ltrim($name, '/');
		$realpath = COREPATH.$name.PHP_FILE_EXTENSION;
		if (file_exists($realpath)) {
			include_once($realpath);
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
		}
	}
	
	//autoload用, 加载配置文件
	//参数可以是路径 a/b/c 则会寻找config/a/b/c.php
	public static function Config($name)
	{
		// 将配置文件放在不同的目录 由全局变量ENV来确定
		$realpath = self::getConfigPath($name).PHP_FILE_EXTENSION;
		if (file_exists($realpath)) {
			include_once($realpath);
		}
	}

	public static function getConfigPath($name)
	{
		return CONFIGPATH.ENV.'/'.$name;
	}

	//加载不区分生产/测试的配置文件
	public static function PublicConfig($name)
    {
        $realpath = CONFIGPATH.$name.PHP_FILE_EXTENSION;
        if (file_exists($realpath)) {
            include_once($realpath);
        }
    }

	//autoload用, 只加载Model文件, 并不会实例化
	//参数可以是路径 a/b/c 则会寻找model/a/b/c.php
	public static function Model($name)
	{
		$realpath = MODELPATH.$name.PHP_FILE_EXTENSION;
		if (file_exists($realpath)) {
			require_once($realpath);
		}
	}

	//加载自定义的基类控制器
	public static function BaseCtrl($name)
	{
		$realpath = MODULEPATH.MODULE_NAME.'/'.$name.PHP_FILE_EXTENSION;
		if (file_exists($realpath)) {
			require_once($realpath);
		}
	}

}