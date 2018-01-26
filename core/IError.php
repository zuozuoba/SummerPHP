<?php
//PHP伪多继承
//放在别的类中, 用来存放错误信息
trait IError
{
	public static $_error = [];
	public static $_ext = []; //其它备注信息

	public static function _SetError($str, $key='')
	{
		if (!empty($key)) {
			self::$_error[$key] = $str;
		} else {
			self::$_error[] = $str;
		}
	}

	public static function _Error()
	{
		return end(self::$_error);
	}

	public static function _IsSucceed()
	{
		return count(self::$_error) ? FALSE : TRUE;
	}

	public static function _Ext()
	{
		return self::$_ext;
	}

	public static function _ErrorOut()
	{
		exit(json_encode(self::$_error));
	}
}
