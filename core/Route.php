<?php
class Route
{
	public $subdomain = '';
	public $uri = '';

	public $ismatch = false;
	public $DomainMatchName = false;
	public $UriMatchName = false;

	public $module = 'index';
	public $controller = 'index';
	public $action = 'index';

	public $domainRouter = array();
	public $pathRouter = array();

	public $args = array();

	public $error = '';


	public function __construct($subdomain, $uri)
	{
		$this->subdomain = $subdomain;
		$this->uri = $uri;

		$this->domainRouter = RouteConfig::$Domain;
		$this->pathRouter = RouteConfig::$Path;

		//先检查子域名路由
		//如果子域名(通常是二级域名)中只有www就不再匹配
		if (!empty($this->domainRouter) && ($this->subdomain != 'www')) {
			$this->DomainMatchName = $this->preg_parse($this->domainRouter, $this->subdomain);
		}

		//再检查uri路由
		//如果uri中不含有"/"才会走正则匹配
		if (!empty($this->pathRouter) && !empty($this->uri) && (strpos($this->uri, '/') === false)) {
			$this->UriMatchName = $this->preg_parse($this->pathRouter, $this->uri);
		}

		//如果URI没有匹配到, 就需要按照普通的分析方法分析URI
		//此时会覆盖掉二级域名分析的结果
		if (empty($this->UriMatchName)) {
			$this->analysisPath($this->uri);
		}

		return $this;
	}

	//正则匹配分析
	//1. 如果没有通配符, 判断router数组存在的话就直接
	public function preg_parse($router, $subject)
	{
		$match_route = false;
		$path = '';
		if (!empty($router[$subject])) {
			$match_route = $subject;
			$path = $router[$subject]; //单纯的字符串, 没有正则表达式
		} else {
			foreach ($router as $pattern => $route) {
				$pattern = '#'.$pattern.'#';

				preg_match($pattern, $subject, $matches);
				
				$countMatch = count($matches) - 1; //匹配到的数据个数
				$countDollar = substr_count($route, '$'); //待匹配的字符串中'$'的个数

				if ($countMatch == $countDollar) {
					$this->ismatch = true;
					$match_route = $pattern;
					// ["abc_123_456_789" => "$1/$2/$3/id/$4"]
					// 变成 ['$1' => 'abc', '$2' => 123, '$3' => 456, $4 => 789]
					foreach ($matches as $key => $value) {
						$arrMatchArg['$'.$key] = $value;
					}

					// 接上一步: "$1/$2/$3/id/$4"
					// 变成 "abc/123/456/id/789", 方便下一步解析出m c a
					$path = str_replace(array_keys($arrMatchArg), array_values($arrMatchArg), $route);
					break;
				}
			}
		}

		$this->analysisPath($path);

		return $match_route;
	}

	//分析路径参数
	public function analysisPath($path)
	{
		//获得module/controller/action
		$arrPathInfo = explode('/', $path);//存放URL中以正斜线隔开的内容的数组

		!empty($arrPathInfo[0]) && ($this->module = $arrPathInfo[0]);
		!empty($arrPathInfo[1]) && ($this->controller = $arrPathInfo[1]);
		!empty($arrPathInfo[2]) && ($this->action = $arrPathInfo[2]);

		//存放除module/controller/action之外的参数
		// /a/1/b/2/c/3 ==> ?a=1&b=2&c=3
		// 当键和值不成对出现时,默认最后一个键的值为0
		// 参数中不要出现数字键,否则在合并post,get参数时会出错
		if (count($arrPathInfo) > 3) {
			$arrPath = array_slice($arrPathInfo, 3);
			$intArgNum = count($arrPath);
			if ($intArgNum % 2 != 0) {
				$arrPath[] = 0;//最后补一个0
			}

			for ($i=0; $i<$intArgNum; $i=$i+2) {
				$this->args[$arrPath[$i]] = $arrPath[$i+1];
			}
		}
	}

}