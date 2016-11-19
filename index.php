<?php
date_default_timezone_set('Asia/Shanghai');
header("Content-type: text/html; charset=utf-8");

//要在这里宏定义ROOT因为本文件是入口文件,所有的include/require相对路径时都是以此文件所在目录为基准目录
$root = str_replace('\\', '/', dirname(__FILE__));
define('ROOT', $root);
define('COREPATH', 		ROOT.'/core/'); //框架核心目录
define('LIBPATH', 		ROOT.'/libs/'); //库文件目录
define('CACHEPATH', 	ROOT.'/cache/'); //缓存目录
define('MODULEPATH', 	ROOT.'/modules/'); //模块目录
define('MODELPATH', 	ROOT.'/model/'); //model目录
define('VIEWPATH', 	    ROOT.'/view/'); //model目录
define('CONFIGPATH', 	ROOT.'/config/'); //配置文件目录
define('STATICPATH', 	ROOT.'/static/'); //静态文件目录: js/css/image

define('DOMAIN', $_SERVER['HTTP_HOST']); //http_host在PHP内置的server中包含了端口, 在nginx中没有包含端口

$base_url = ($_SERVER['SERVER_PORT'] == '80') ? 'http://'.DOMAIN.'/' : 'http://'.DOMAIN.':'.$_SERVER['SERVER_PORT'].'/';
define('BASEURL', $base_url); //也可能是https

define('REQUEST_URI',	$_SERVER['REQUEST_URI']); //包含了?后的get参数
define('HTTP_REFERER',	empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER']);

$arrPathInfo = explode('?', $_SERVER['REQUEST_URI']);
define('DOCUMENT_URI',	$arrPathInfo[0]);


define('REQUEST_TIME', 	$_SERVER['REQUEST_TIME']);
define('REQUEST_TIME_FLOAT',$_SERVER['REQUEST_TIME_FLOAT']);

define('VIEW_FLODER_NAME', 'view'); //视图目录的名字
define('PHP_FILE_EXTENSION', '.php'); //PHP文件的后缀
define('TPL_FILE_EXTENSION', '.php'); //模版文件的后缀, 可以改为html, 但需要修改PHP/nginx的配置项使之识别这种后缀的文件

define('ENV', 'dev');
// define('ENV', 'pro');

require_once(COREPATH.'Main.php');//读取单一入口的超级父类文件
require_once(COREPATH.'Load.php'); //公共函数文件

//自动加载函数
spl_autoload_register(array('Load', 'Core'));
spl_autoload_register(array('Load', 'Lib'));
spl_autoload_register(array('Load', 'Config'));
spl_autoload_register(array('Load', 'Model'));

$main = Main::getInstance();
$main->run(); //在Main类中动态加载其它controller类
exit;

//1.在构造函数中(直接或间接)得到URL中的module/controller/action
//2.利用上一步构造好的变量去加载需要的东西