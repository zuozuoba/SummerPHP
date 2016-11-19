<?php

class _login extends Main
{
	public $userinfo = array();

	public function initc()
	{
		$userinfo = $this->isLogin();
		if ($userinfo) {
			$this->userinfo = $userinfo;
		}
		$this->view->username = empty($userinfo['username']) ? '' : $userinfo['username'];
	}

	public function index()
	{
		if (!empty($this->userinfo)) {
			$this->notify('您已经登录过了~', $this->moduleUrl, 2);
		}

		if ($this->ispost) {
			Safe::Check_Token_Once(); //防止接口被刷

			$username = $this->getData('username');
			$password = $this->getData('password');
			$remember_me  = $this->getData('remember_me');
			$expire = $remember_me ? 86400*7 : 86400;

			$password = Fun::saltmd5($password);
			$rs = User::link('user')->setWhere(['username' => $username, 'password' => $password])->getOne();
			if ($rs) {
				//写入缓存
				$sessionid = session_id();
				$key = iredis::getFullKeyName(iredis::$User_Info, $sessionid);
				iredis::getInstance()->setex($key, $expire, json_encode($rs));

				//跳转到来源页面
				$url = $this->moduleUrl;
				$this->notify('登录成功', $url, 2);
			} else {
				$this->notify('登录失败, 请重新登录', $this->controllerUrl.'login', 2);
			}
		} else {
			$this->view->safe_token = Safe::Create_Token();
			$this->show('login');
		}
	}

	public function isLogin()
	{
		$sessionid = session_id();
		$key = iredis::getFullKeyName(iredis::$User_Info, $sessionid);
		$rs = iredis::getInstance()->get($key);
		$rs = json_decode($rs, TRUE);
		if (!empty($rs)) {
			return $rs;
		} else {
			return false;
		}
	}

}