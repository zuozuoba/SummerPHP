<?php

class _login
{
	public $userinfo = array();

	public function initc()
	{
		$userinfo = $this->isLogin();
		if ($userinfo) {
			$this->userinfo = $userinfo;
		}
	}

	//是否登录
	public function isLogin()
	{
		$rs = User::getUserCookie();
		return !empty($rs) ? $rs : FALSE;
	}

	//登录
	public function index()
	{
		if (!empty($this->userinfo)) {
			Response::redirect('您已经登录过了~', BASEURL.'home');
		}

		if (Request::isPost()) {
			Safe::Check_Token_Once(); //防止重复提交
			$mobile = Request::Post('mobile');
			$password = Request::Post('password');
			$remember_me  = Request::Post('remember_me');
			$expire = $remember_me ? 86400*7 : 0;

			$user = User::verifyUserLogin($mobile, $password);
			if ($user) {
				//写入cookie
				User::setUserCookie($user, $expire);

				//跳转到来源页面
                Response::redirect('登录成功', BASEURL);
			} else {
                Response::redirect('登录失败, 请重新登录', BASEURL.'login');
			}
		} else {
			View::show('login');
		}
	}
}