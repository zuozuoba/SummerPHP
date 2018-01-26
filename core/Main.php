<?php
class Main
{
	// 注意: 这个构造函数里的任何一个自定义函数
	// 在构造函数执行结束之前不能 直接或间接的调用本构造函数
	// 否则会出现无限递归调用
	private function __construct()
	{}

	//核心
	//include请求的控制器文件
	//new出controller对应的类
	//执行类的 initc() 函数
	//执行action对应的函数
	public static function _run()
	{
	    try {
            $arrHost = explode('.', HTTP_HOST);
            $subDomain = $arrHost[0];

            $documentUri = str_replace('.html', '', DOCUMENT_URI);//支持伪静态，但nginx的location配置也要做相应改动
            $documentUri = trim($documentUri, '/'); //去掉URI两边的斜线
            $documentUri = Safe::check($documentUri, Safe::$Check_DEFAULT);

            //路由, 分析URL,初始化变量,供其它成员函数使用
            //返回是否匹配了路由, 匹配路由的名字, 路由指向的module,controller,action, 路由错误信息等, 方便调试
            Route::parseURI($documentUri, $subDomain);

            define('MODULE_NAME', 		Route::$module);
            define('CONTROLLER_NAME', 	Route::$controller);
            define('ACTION_NAME', 		Route::$action);

            define('MODULE_URL', 		BASEURL.MODULE_NAME.DIRECTORY_SEPARATOR);
            define('CONTROLLER_URL', 	MODULE_URL.CONTROLLER_NAME.DIRECTORY_SEPARATOR);
            define('ACTION_URL', 		CONTROLLER_URL.ACTION_NAME.DIRECTORY_SEPARATOR);

            define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST'));
            
            if (CONTROLLER_NAME == 'favicon.ico') {
                exit(200);
            }
            
            if (ACTION_NAME == 'initc') {
                Response::redirect('非法访问(initc)', MODULE_URL, '2');
            }

            //包含控制器类文件
            include_once(MODULEPATH.MODULE_NAME.DIRECTORY_SEPARATOR.CONTROLLER_NAME.PHP_FILE_EXTENSION);

            //new控制器类
            $controller = '_'.CONTROLLER_NAME;/****注意此处控制器名前有下划线, 为了防止method和class的名字一样****/
            $obj = new $controller; //创建控制器对象

            //执行控制器的initc()这个默认方法
            if (method_exists($obj, 'initc')) {
                //由于不能写子类的构造函数因此每次都执行这个initc()函数
                //c是controller的意思,为了不覆盖掉main类里的init()
                $obj->initc();
            }

            //执行控制器的action方法
            call_user_func(array($obj, ACTION_NAME));
            //call_user_func_array(array($obj, ACTION_NAME), array());

        } catch (Exception $e) {
            $msg = [];
            $msg[] = $e->getMessage();
            $msg[] = $e->getFile();
            $msg[] = $e->getLine();
            $msg[] = $e->getTraceAsString();

//            FileLog::ini('error')->info(implode(SEPARTOR, $msg));

			Response::ini($msg)->json();
        }

	}
 
	//跟 _run()类似, 不过是用于cli环境
    public static function _runcli()
    {
        try {
            
            $documentUri = str_replace('.html', '', DOCUMENT_URI);//支持伪静态，但nginx的location配置也要做相应改动
            $documentUri = trim($documentUri, '/'); //去掉URI两边的斜线
            $documentUri = Safe::check($documentUri, Safe::$Check_DEFAULT);
            
            //路由, 分析URL,初始化变量,供其它成员函数使用
            //返回是否匹配了路由, 匹配路由的名字, 路由指向的module,controller,action, 路由错误信息等, 方便调试
            Route::parseURI($documentUri, '');
            
            define('MODULE_NAME', 		Route::$module);
            define('CONTROLLER_NAME', 	Route::$controller);
            define('ACTION_NAME', 		Route::$action);
            
            if (ACTION_NAME == 'initc') {
                exit('非法访问(initc)');
            }
            
            //包含控制器类文件
            include_once(MODULEPATH.MODULE_NAME.DIRECTORY_SEPARATOR.CONTROLLER_NAME.PHP_FILE_EXTENSION);
            
            //new控制器类
            $controller = '_'.CONTROLLER_NAME;/****注意此处控制器名前有下划线, 为了防止method和class的名字一样****/
            $obj = new $controller; //创建控制器对象
            
            //执行控制器的initc()这个默认方法
            if (method_exists($obj, 'initc')) {
                //由于不能写子类的构造函数因此每次都执行这个initc()函数
                //c是controller的意思,为了不覆盖掉main类里的init()
                $obj->initc();
            }
            
            //执行控制器的action方法
            call_user_func(array($obj, ACTION_NAME));
            //call_user_func_array(array($obj, ACTION_NAME), array());
            
        } catch (Exception $e) {
            $msg = [];
            $msg[] = $e->getMessage();
            $msg[] = $e->getFile();
            $msg[] = $e->getLine();
            $msg[] = $e->getTraceAsString();
            
            FileLog::ini('error')->info(implode(SEPARTOR, $msg));
        }
        
    }

    //服务器负载
    public static function serverLoad()
	{
		$load = sys_getloadavg();
		if ($load[0] > 80) {
			header('HTTP/1.1 503 Too busy, try again later');
			die('服务器忙, 请稍后再试.');
		}
	}

}