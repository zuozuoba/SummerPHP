<?php

Load::Lib('QQIM/TimRestApi');

class QQIM
{
	public static $instance = false;

	public static function getIM()
	{
		if (self::$instance === FALSE) {
			self::$instance = new TimRestAPI();
			self::$instance->init(Config::$QQIM['sdkappid'], Config::$QQIM['identifier']);
			$sign_path = Load::getConfigPath(Config::$QQIM['user_sign']);
			$user_sign = file_get_contents($sign_path);
			self::$instance->set_user_sig($user_sign);
		}
		return self::$instance;
	}
}