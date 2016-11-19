<?php

class _logout extends Main
{
	public function initc()
	{
		
	}
	
	public function index()
	{
		$sessionid = session_id();
		$key = iredis::getFullKeyName(iredis::$User_Info, $sessionid);
		iredis::getInstance()->setex($key, 1, '');
		$url = $this->moduleUrl;
		$this->notify('退出成功~', $url, 2);
	}
}