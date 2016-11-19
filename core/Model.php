<?php

/**
* 业务处理基类
* 连接数据库
* 获得分页信息
*/
class Model extends DBmysql
{
	public $main = NULL;

	public function getMain()
	{
		return Main::getInstance();
	}
	
	//分页函数
	public function setpage($num, $currentpage, $baseurl, $pagesize='')
	{
		$pagesize 		= empty($pagesize) ? 10 : $pagesize;			//每页显示多少条记录
		$totalpage 		= ceil($num / $pagesize);						//总共多少页
		$currentpage 	= empty($currentpage) ? 1 : $currentpage;		//当前页
		$lastpage 		= (($currentpage-1) <= 0)? 1: $currentpage-1;	//最后一页
		$nextpage 		= (($currentpage+1)>$totalpage) ? $totalpage : $currentpage+1;//下一页
		// $baseurl 	= empty($baseurl) ? $this->actionUrl : $baseurl;//不带分页的跳转链接
		$firstpageurl 	= $baseurl;
		$lastpageurl 	= $baseurl."/page/{$lastpage}";
		$nextpageurl 	= $baseurl."/page/{$nextpage}";
		$finalpageurl 	= $baseurl."/page/{$totalpage}";
		
		$pageinfo = array(
				'num'			=> $num,
				'currentpage' 	=> $currentpage,
				'totalpage' 	=> $totalpage,
				'firstpageurl' 	=> $firstpageurl,
				'lastpageurl' 	=> $lastpageurl,
				'nextpageurl' 	=> $nextpageurl,
				'finalpageurl' 	=> $finalpageurl);
		
		$start = ($currentpage - 1)*$pagesize;
		$limit = $start.','.$pagesize;
		return array(
			'page' => $pageinfo,
			'limit' => $limit);
	}
}