<?php
/**
* 一些常用的函数
*/
class Fun
{
    public static $instance;
    public static $Error='';
    
    public static function getInstance()
    {
        if (!Fun::$instance) {
            self::$instance = new Fun();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    public function getAddress()
    {
        $ip = Request::getClientIp();
        if (!$ip) {
         return array();
        }
        $ak = '11111';
        $url = "http://api.map.baidu.com/location/ip?ak={$ak}&ip={$ip}&coor=bd09ll";
        
        $output = file_get_contents($url);
        return json_decode($output, true);
    }
    
    //毫秒数64位
    public static function getMsecTime()
    {
        list($usec, $sec) = explode(' ', microtime());

        $usec2msec = $usec * 1000;  //计算微秒部分的毫秒数(微秒部分并不是微秒,这部分的单位是秒)
        $usec2msec2int = intval($usec2msec);
        $sec2msec = $sec * 1000;    //计算秒部分的毫秒数
        $sec2msec2int = intval($sec2msec);
        
        $msec = $sec2msec2int + $usec2msec2int; //加起来就对了
        return $msec;
    }

	//位图排序
    public static function BitMapSort($a)
    {
		// $a = array(1,4,3,50,34,60,100,88,200,150,300); //定义一个乱序的数组
        // var_dump(PHP_INT_MAX, PHP_INT_SIZE);
        // int 9223372036854775807 对应mysql的bigint
        // int 8

        //申请一个整形数组, 50个元素, 初始化为整数0
        $bitmap = array_fill(0, 1000, 0);

        // $bitmap中每个整形的二进制位数 
        // 本例中int = 8*8 = 64bit; $bitmap数组一共1000*64 = 64000个bit位
        // 也就是说能为最大值等于64000的整数集合排序
        $int_bit_size = PHP_INT_SIZE * 8; 
        // $a = array(1,4,3,50,34,60,100,88,200,150,300); //定义一个乱序的数组

        //扫描$a中的每一个数, 将其转换为 x*64 + y
        foreach ($a as $v) {
            $shang = $v / $int_bit_size; //商
            $yushu = $v % $int_bit_size; //余数

            $offset = 1 << $yushu;

            $bitmap[$shang] = $bitmap[$shang] | $offset;//将bit位置为1
        }

        //将$bitmap中的bit位依次还原为整数输出,即可得到排序后的数组
        $b = array();
        foreach ($bitmap as $k => $v) {
            for ($i = 0; $i < $int_bit_size; $i++) {
                $tmp = 1 << $i;
                $flag = $tmp & $bitmap[$k];

                // $b[] = $flag ? $k * $int_bit_size + $i : false;
                if ($flag) {
                    $b[] =  $k * $int_bit_size + $i;
                }
            }
        }

        return $b;
    }
	
	//按照二维数组中的某个键进行排序
	public static function sort2DArray(&$arr, $key, $desc = '')
	{
		$tmp = array();
		foreach ($arr as $k=>$v) {
			$tmp[$k] = $v[$key];
		}
		
		if ($desc) {
			arsort($tmp);
		} else {
			asort($tmp);
		}
		
		$result = array();
		foreach ($tmp as $k => $v) {
			$result[$k] = $arr[$k];
		}
		
		return $result;
	}

	/**
	 * @param int $len
	 * @return string
	 * desc 生成随机码
	 * 注意里边有+, >, < 等特殊字符在不同编码的时候会又变化
	 */
    public static function randCode($len=10)
    {
        $char = array (
            'Q', '@', '8', 'y', '%', '^', '5', 'Z', '(', 'G', '_', 'O', '`', 'S', '-',
            'N', '<', 'D','{', '}', '[', ']', 'h', ';',
            'W', '.', '/', '|', ':', '1', 'E', 'L', '4', '&', '6', '7', '#', '9',
            'a', 'A', 'b', 'B', '~', 'C', 'd', '>', 'e', '2', 'f', 'P',
            'g', ')', '?', 'H', 'i', 'X', 'U', 'J', 'k', 'r', 'l', '3', 't', 'M',
            'n', '=', 'o', '+', 'p', 'F', 'q', '!', 'K', 'R', 's',
            'c', 'm', 'T', 'v', 'j', 'u', 'V', 'w', ',', 'x', 'I', '$', 'Y', 'z', '*'
        );
        $charLen = count($char) - 1;
        $token = '';
        for ($i = 0; $i < $len; $i++) {
            $index = mt_rand(0, $charLen);
            $token .= $char[$index];
        }

        return $token;
    }

	/**
	 * desc 获取本模块下所有的控制器, 及其方法和注释信息
	 * 注意:
	 * 1. PHP代码缓存类的扩展要保留代码的注释信息, 否则这里获取不到注释信息
	 * 2. 代码注释要用多行注释
	 * 3. 注释中希望获取的文字单独占一行, 并且以字符串 "desc "开始
	 * 4. todo 公共访问模块,无限制
	 * @param string $moduleName 模块名
	 * @return array
	 */
	public static function getAllController($moduleName='')
	{
		$moduleName = empty($moduleName) ? MODULE_NAME : $moduleName;
		$dir = MODULEPATH.$moduleName.'/';
		$controllers = scandir($dir);

		$blackMethod = ['__construct', '__distruct'];
		$blackClass = ['Think\Controller'];

		$arr = [];
		foreach ($controllers as $key => $ctrl) {
			$path = $dir.$ctrl;
			if (is_file($path)) {
				require_once($path);

				$ctrlname = $moduleName.'_'.str_replace(PHP_FILE_EXTENSION, '', $ctrl); //用下划线拼接, admin_index
				$classname = '_'.str_replace(PHP_FILE_EXTENSION, '', $ctrl);
				$class = new ReflectionClass($classname);

				$Ctrlcomment = $class->getDocComment();
				preg_match('/desc\s+(.*)/', $Ctrlcomment, $match);
				$arr[$ctrlname]['class']['doc'] = !empty($match[1]) ? trim($match[1]) : '';
				$arr[$ctrlname]['class']['name'] = $ctrlname;

				$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
				foreach ($methods as $method) {
					//方法名和类名不在黑名单, 而且不是父类的方法
					if (in_array($method->name, $blackMethod) === FALSE && in_array($method->class, $blackClass) === FALSE && strpos($method->class, $classname) !== FALSE) {
						$Methodcomment = $method->getDocComment();
						preg_match('/desc\s+(.*)/', $Methodcomment, $match);
						$tmp = [
							'doc' => !empty($match[1]) ? trim($match[1]) : '',
							'name' => $method->name,
						];
						$arr[$ctrlname]['method'][] = $tmp;

						//找不到注释就不获取
//						if (!empty($match[1])) {
//							$tmp = [
//								'doc' => trim($match[1]),
//								'name' => $method->name,
//							];
//							$arr[$ctrlname]['method'][] = $tmp;
//						}
					}
				}
			}
		}

		return $arr;
	}

	/**
	 * @param string $data 明文
	 * @param int $len 长度
	 * @return string
	 * 不可逆加密, 用于登录密码加密
	 */
	public static function saltmd5($data, $len=32)
	{
		$salt = '@#$*&%![}=!';
		$all = md5(md5($data).$salt);
		return substr($all, 0, $len);
	}

	/**
	 * 正则匹配出汉字
	 * @param $string
	 * @return mixed
	 */
	public static function getCn($string)
	{
		preg_match("/[\x{4e00}-\x{9fa5}]+/u",$string,$match);
		return $match;
	}

    //获取倒计时
    public static function getRemainTimeDesc($timestamp)
    {
        return [
            'day' => $timestamp/86400,
            'hour' => ($timestamp%86400)/3600,
            'minute' => (($timestamp%86400)%3600)/60,
            'sec' => (($timestamp%86400)%3600)%60,
        ];
    }
	
	//判断数字所在的区间
	public static function numberPosition($current, $start, $end)
	{
		if ($current < $start) {
			return 0;
		} elseif ($current >= $start && $current <= $end) {
			return 1;
		} else {
			return 2;
		}
	}
    
    /**
     * 格式化显示时间戳
     * @param $timestamp
     * @return false|string
     */
    public static function formatDate($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }
    
    /**
     * 构建URL
     * @param string $module
     * @param string $ctrl
     * @param string $action
     * @param array $arg
     * @return string
     */
    public static function buildUrl($module, $ctrl = 'index', $action = 'index', $arg = array())
    {
        $url = BASEURL.$module.'/'.$ctrl.'/'.$action;
        if (empty($arg)) {
            return $url;
        } else {
            return $url.'?'.http_build_query($arg);
        }
    }

}