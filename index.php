<?php
date_default_timezone_set('Asia/Shanghai');
header("Content-type: text/html; charset=utf-8");

//检测服务器负载
$load = sys_getloadavg();
if ($load[0] > 90) {
    header('HTTP/1.1 503 Too busy, try again later');
    die('服务器忙, 请稍后再试.');
}

define('ENV', 'pro');

//要在这里宏定义ROOT因为本文件是入口文件,所有的include/require相对路径时都是以此文件所在目录为基准目录
define('ROOT', __DIR__.DIRECTORY_SEPARATOR);
define('COREPATH', 		ROOT.'core/'); //框架核心目录
define('LIBPATH', 		ROOT.'libs/'); //库文件目录
define('MODULEPATH', 	ROOT.'modules/'); //模块目录
define('MODELPATH', 	ROOT.'model/'); //model目录
define('VIEWPATH', 	    ROOT.'view/'); //视图目录
define('CONFIGPATH', 	ROOT.'config/'); //配置文件目录
define('STATICPATH', 	ROOT.'static/'); //静态文件目录: js/css/image
define('LOGPATH', 	    ROOT.'log/'); //日志文件目录

define('PHPCLI', 'php7'); //PHP可执行文件路径

//下边参数的定义是以nginx为webserver获取的
$scheme = empty($_SERVER['REQUEST_SCHEME']) ? 'http' : $_SERVER['REQUEST_SCHEME'];
define('HTTP_HOST',		$_SERVER['HTTP_HOST']); //http_host在PHP内置的server中包含了端口, 在nginx中没有包含端口
define('BASEURL',		$scheme.'://'.HTTP_HOST.'/');
define('REQUEST_URI',	$_SERVER['REQUEST_URI']); //包含了?后的get参数, 也包含问号前的斜线
define('HTTP_REFERER',	empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER']);
define('DOCUMENT_URI',	$_SERVER['DOCUMENT_URI']);
define('REQUEST_TIME', 	$_SERVER['REQUEST_TIME']);
define('REQUEST_TIME_FLOAT',$_SERVER['REQUEST_TIME_FLOAT']);
define('SEPARTOR', '__'); //全局分隔符

define('PHP_FILE_EXTENSION',	'.php'); //PHP文件的后缀, 也可以是.class.php
define('TPL_FILE_EXTENSION',	'.html'); //模版文件的后缀, 也可以是.php结尾

define('UNIQID', uniqid()); //PHP基于微秒的唯一值(字母和数字的组合16位), 可用于文件日志的跟踪编号, 便于筛选,跟踪

require_once(COREPATH.'Main.php');//读取核心控制器基类文件
require_once(COREPATH.'Load.php'); //自动加载类

//自动加载函数
spl_autoload_register(array('Load', 'Core'));	//核心类
spl_autoload_register(array('Load', 'Lib'));	//第三方类库
spl_autoload_register(array('Load', 'Config'));	//配置文件
spl_autoload_register(array('Load', 'Model'));	//模型类
spl_autoload_register(array('Load', 'BaseCtrl')); //自定义基类控制器

register_shutdown_function('shutdown');
function shutdown() {
    $bitmask = E_ERROR | E_WARNING; //记录这几种错误
    $last_error = error_get_last();
    if(($last_error['type'] & $bitmask) > 0) {
        FileLog::ini('error')->info(implode(SEPARTOR, error_get_last()), 'error_shutdown');
    }
}

Main::_run(); //在Main类中动态加载其它controller类
exit;