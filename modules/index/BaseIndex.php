<?php

/**
 * desc 前台用户访问基类
 * Class BaseIndex
 */
class BaseIndex
{
	protected $uid = false;
	protected $userinfo = [];
	protected $notNull = []; //不能为空的数据

	public function __construct()
	{
		$user = User::getUserCookie(User::$UserCookieName);
		if (!empty($user)) {
			$this->uid = $user['uid'];
			$this->userinfo = $user;
			User::setUserCookie($user); //重新计算有效期
		}

		//预渲染
		View::preShow('header');
		View::preShow('nav');
		View::endShow('footer');
	}
}