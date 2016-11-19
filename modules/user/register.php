<?php

class _register extends Main
{
	public function initc()
	{
	}

	public function index()
	{
		if ($this->ispost) {
			Safe::Check_Token_Once(); //防止接口被刷

			$username = $this->getData('username');
			if (mb_strlen($username) > 16) {
				$this->notify('抱歉~ 您输入的姓名不能超过16个字符, 请重新输入', $this->actionUrl, 3);
			}

			//检测username是否重复
			$already_used = User::link('user')
				->setFields('id')
				->setWhere(['username' => $username])
				->getOneField('id');
			if ($already_used) {
				$this->notify('抱歉~ 您输入的姓名已经被占用, 请重新选择', $this->actionUrl, 2);
			}

			$mobile = $this->getData('mobile', Safe::$Check_DEFAULT|Safe::$Check_INT);
			$password = $this->getData('password');
			$password_confirm = $this->getData('password_confirm');
			$remember_me  = $this->getData('remember_me');

			if ( strcmp($password, $password_confirm) != 0) {
				$this->notify('您输入的两个密码不一样, 请重新输入', $this->actionUrl, 3);
			} else {
				$password = Fun::saltmd5($password);
			}

			if ($mobile) {
				$mobile = Safe::encrypt($mobile);
			}

			$expire = $remember_me ? 86400*7 : 86400;
			// $salt = @#$!%

			$user = array(
				'username' => $username,
				'password' => $password,
				'mobile' => $mobile,
				'create_time' => INT_NOW_TIME
			);

			//入库
			User::link('user')->insert($user);

			//记录到reids中
			$sessionid = session_id();
			$key = iredis::getFullKeyName(iredis::$User_Info, $sessionid);
			iredis::getInstance()->setex($key, $expire, json_encode(['username' => $username, 'mobile' => $mobile]));
			$this->notify('注册成功~', $this->moduleUrl, 2);
		} else {
			$this->view->safe_token = Safe::Create_Token();
			$this->show('reg');
		}
	}
}