<?php

class _logout
{
	public function initc()
	{
		
	}
	
	public function index()
	{
		User::clearUserCookie();
		Response::redirect('退出成功', HTTP_REFERER);
	}
}