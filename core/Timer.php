<?php

/**
 * Class Timer
 * 注意, start over 函数必须成对出现
 */
class Timer
{
	public static $list = [];
	public static $counter = 0;

	public static function start($key)
	{
		self::$list[self::$counter]['key'] = $key;
		self::$list[self::$counter]['start'] = microtime(true);
	}

	public static function over($key)
	{
		$counter = self::$counter;
		self::$list[self::$counter++]['over'] = microtime(true);

		self::$list[$counter]['cost'] = bcsub(self::$list[$counter]['over'], self::$list[$counter]['start'], 4);
	}
}

//Timer::start(1);
//usleep(100);
//Timer::over(1);
//
//Timer::start(2);
//usleep(100);
//Timer::over(2);
//
//Timer::start(3);
//usleep(100);
//Timer::over(3);
//
//Timer::start(4);
//usleep(100);
//Timer::over(4);
//
//echo '<pre>';print_r(Timer::$list);

//	Array
//	(
//		[0] => Array
//	    (
//			  [key] => 1
//            [start] => 1497538210.5637
//            [over] => 1497538210.5647
//            [cost] => 0.0010
//        )
//
//    [1] => Array
//		   (
//			   [key] => 2
//            [start] => 1497538210.5647
//            [over] => 1497538210.5657
//            [cost] => 0.0010
//        )
//
//    [2] => Array
//		   (
//			   [key] => 3
//            [start] => 1497538210.5657
//            [over] => 1497538210.5667
//            [cost] => 0.0010
//        )
//
//    [3] => Array
//		   (
//			   [key] => 4
//            [start] => 1497538210.5667
//            [over] => 1497538210.5677
//            [cost] => 0.0010
//        )
//
//)