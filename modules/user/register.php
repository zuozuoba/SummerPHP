<?php

class _register
{
	public function initc()
	{
	}

	public function index()
	{
		if (IS_POST) {
			Safe::Check_Token_Once(); //防止重复提交

			$username = Request::post('username');
			if (mb_strlen($username) > 16) {
                Response::redirect('抱歉~ 您输入的姓名不能超过16个字符, 请重新输入', ACTION_URL, 3);
			}

			//检测username是否重复
//			$already_used = User::link('user')
//				->setFields('uid')
//				->setWhere(['username' => $username])
//				->getOneField('uid');
//			if ($already_used) {
//				Fun::redirect('抱歉~ 您输入的姓名已经被占用, 请重新选择', ACTION_URL, 2);
//			}

			$mobile = Request::post('mobile', Safe::$Check_DEFAULT|Safe::$Check_INT);
			$password = Request::post('password');
			$password_confirm = Request::post('password_confirm');
			$remember_me  = Request::post('remember_me');

			if ( strcmp($password, $password_confirm) != 0) {
                Response::redirect('您输入的两个密码不一样, 请重新输入', ACTION_URL, 3);
			} else {
				$password = Response::saltmd5($password);
			}

			if ($mobile) {
				$mobile = Safe::encrypt($mobile);
			}

			$expire = $remember_me ? 86400*7 : 86400;

			$user = array(
				'username' => $username,
				'password' => $password,
				'mobile' => $mobile,
				'addtime' => REQUEST_TIME
			);

			//入库
			$uid = User::link('user')->insert($user);
			$user = json_encode(['username' => $username, 'mobile' => $mobile, 'uid' => $uid]);
			User::setUserCookie($user, $expire);
            
            Response::redirect('注册成功~', MODULE_URL.'login', 2);
		} else {
			View::show('reg');
		}
	}
}