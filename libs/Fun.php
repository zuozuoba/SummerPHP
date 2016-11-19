<?php
/**
* 一些常用的函数
*/
class Fun
{
    public static $instance;
    // public $main = false;
    
    public static function getInstance()
    {
        if (!Fun::$instance) {
            self::$instance = new Fun();
        }
        return self::$instance;
    }

    private function __construct()
    {
        // $this->main = Main::getInstance();
    }

    /**
     * 一般获得用户IP都是使用$_SERVER['REMOTE_ADDR']这个环境变量，但是此变量只会纪录最后一个主机IP，所以当用户浏览器有设定Proxy时，就无法取得他的真实IP。
     * 这时可以使用另一个环境变量$_SERVER['HTTP_X_FORWARDED_FOR'] ，它会纪录所经过的主机IP，但是只有在用户有透过Proxy时才会产生，所以可以像以下这样写来取得使用者真实IP。
	 * 而且需要webserver的支持
     * @return string 客户端ip
     */
    public static function getClientIp()
    {
        $REMOTE_ADDR = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
        // $HTTP_CLIENT_IP = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : false;
        $ARR_HTTP_X_FORWARDED_FOR = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) : array('0');
        $HTTP_X_FORWARDED_FOR = $ARR_HTTP_X_FORWARDED_FOR['0'];

        $ip = $HTTP_X_FORWARDED_FOR ? $HTTP_X_FORWARDED_FOR : $REMOTE_ADDR;
        // $ip = $HTTP_CLIENT_IP ? $HTTP_CLIENT_IP : $REMOTE_ADDR;
        
        return $ip ? $ip : '0';
    }

    /**
     * @return string 客户端ip
     */
    public static function getServerIp()
    {
        $SERVER_ADDR = !empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : false;
        return $SERVER_ADDR ? $SERVER_ADDR : '0';
    }

    public function getAddress()
    {
        $ip = $this->getClientIp();
        // if (!$ip) {
        //     return array();
        // }
        // $ip = '221.12.88.73';
        $url = "http://api.map.baidu.com/location/ip?ak=bbCXktcD6Qrbxcpyp1DLkR8b&ip={$ip}&coor=bd09ll";
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $output = curl_exec($ch);
        // curl_close($ch);
        
        $output = file_get_contents($url);
        return json_decode($output, true);
    }

    public static function curlPost($url, $post)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * @param string   $url 访问链接
     * @param array $data post数据
     * @param string $target 需要重试的标准, 返回结果中是否包含$target字符串
     * @param int $retry 重试次数, 默认3次
     * @param int $gap 重试间隔时间, 默认1s
     * @return bool|mixed curl返回结果
     * desc 有重试功能的curlget
     */
    function curlPostRetry($url, $data, $target, $retry=3, $gap = 1)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名（为0也可以，就是连域名存在与否都不验证了）

        $output = curl_exec($ch);

        while((strpos($output, $target) === FALSE || empty($output)) && $retry--){
	        if ($gap) {
		        sleep($gap);
	        }
            $output = curl_exec($ch);
        }
        curl_close($ch);
        return $output;
    }


    public static function curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * @param string   $url 访问链接
     * @param string $target 需要重试的标准, 返回结果中是否包含$target字符串
     * @param int $retry 重试次数, 默认3次
     * @param int $gap 重试间隔时间, 默认1s
     * @return bool|mixed curl返回结果
     * desc 有重试功能的curlget
     */
    function curlGetRetry($url, $target, $retry=3, $gap = 1)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名（为0也可以，就是连域名存在与否都不验证了）

        $output = curl_exec($ch);

        while((strpos($output, $target) === FALSE || empty($output)) && $retry--){
			if ($gap) {
				sleep($gap);
			}
            $output = curl_exec($ch);
        }
        curl_close($ch);
        return $output;
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
	 * @param string $module
	 * @return array $arr 注释信息
	 * desc 获取模块下的控制器及其注释内容, 方便根据注释内容进行一些操作, 比如权限控制之类的
	 */
	public static function getControllersDoc($module)
	{
		$dir = MODULEPATH.$module;
		$controllers = scandir($dir);
		$arr = array();
		foreach ($controllers as $ctrl) {
			if (is_file($ctrl)) {
				$path = $dir.'/'.$ctrl;
				require_once($path);

				$classname = '_'.pathinfo($ctrl, PATHINFO_FILENAME);
				$class = new ReflectionClass($classname);

				$arr[$classname]['class']['doc'] = $class->getDocComment();

				$methods = $class->getMethods();
				foreach ($methods as $method) {
					if ($method->class != 'Main') {
						$arr[$classname]['method'][$method->name]['doc'] = $method->getDocComment();
					}
				}
			}
		}

		return $arr;
	}

	/**
	 * @param string $module
	 * @param string $ctrl
	 * @return array $arr
	 * desc 获取某个控制器的所有方法的注释
	 */
	public static function getMethodsDoc($module, $ctrl='')
	{
		$classname = ltrim($ctrl, '_');
		$path = MODULEPATH.$module.'/'.$classname.PHP_FILE_EXTENSION;

		if (file_exists($path)) {
			require_once($path);

			$arr = array();
			$class = new ReflectionClass($ctrl);
			$arr['class']['doc'] = $class->getDocComment();
			$methods = $class->getMethods();
			foreach ($methods as $method) {
				if ($method->class != 'Main') {
					$arr['method'][$method->name]['doc'] = $method->getDocComment();
				}
			}

			return $arr;
		} else {
			return false;
		}
	}

	/**
	 * @param string $module 模块名
	 * @param string $ctrl 控制器名 _index
	 * @param string $action 方法名
	 * @return array|bool $arr
	 */
	public static function getMethodDoc($module, $ctrl='', $action='')
	{
		$classname = ltrim($ctrl, '_');
		$path = MODULEPATH.$module.'/'.$classname.PHP_FILE_EXTENSION;

		if (file_exists($path)) {
			require_once($path);

			$arr = array();
			$class = new ReflectionClass($ctrl);
			$arr['class']['doc'] = $class->getDocComment();
			$method = $class->getMethod($action);
			$arr['method']['doc'] = $method->getDocComment();
			return $arr;
		} else {
			return false;
		}
	}

	public static function success($msg='', $rs='', $url='')
	{
		exit(json_encode(array('code' => '1', 'msg' => $msg, 'result' => $rs, 'url' => $url)));
	}

	public static function error($msg='', $rs='', $url='')
	{
		exit(json_encode(array('code' => '-1', 'msg' => $msg, 'result' => $rs, 'url' => $url)));
	}
    
	//判断是否是手机
	public function isMobile()
	{
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

		if (stripos($user_agent, 'android') !== false) {
			return true;
		}

		if (stripos($user_agent, 'iphone') !== false) {
			return true;
		}

		if (stripos($user_agent, 'windows phone') !== false) {
			return true;
		}
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

	public static function notify($msg, $url, $second)
	{
		header("refresh:{$second};url={$url}");
		exit($msg);
	}
}