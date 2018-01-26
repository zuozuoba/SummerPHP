<?php

class ICurl
{
    public $ch = false;
    public $url = '';
    public $output = '';
    public $connectTimeOut = 3;
    public $excTimeOut = 3;
    public static $error = '';
    public static $reTryCounter = 0;

    public function __construct($url = '')
    {
        $this->url = $url;
        $this->ch = curl_init($url);

        $this->setConnectTimeOut($this->connectTimeOut);
        $this->setExecTimeOut($this->excTimeOut);
        $this->setReturnString(true);
    }

    public static function ini($url = '')
    {
        return new ICurl($url);
    }

    /**
     * desc 设置请求的url
     * @param $str
     * @return $this
     */
    public function setUrl($str)
    {
        curl_setopt($this->ch, CURLOPT_URL, $str);
        return $this;
    }

    /**
     * desc 设置端口
     * @param $port
     * @return $this
     */
    public function setPort($port)
    {
        curl_setopt($this->ch, CURLOPT_PORT, $port);
        return $this;
    }

    /**
     * desc 将curl_exec()获取的信息以字符串返回，而不是直接输出
     * @param $bool
     * @return $this
     */
    public function setReturnString($bool)
    {
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, $bool);
        return $this;
    }

    /**
     * desc 设置cookie
     * @param $str
     * @return $this
     */
    public function setCookie($str)
    {
        //设定 HTTP 请求中"Cookie: "部分的内容。多个 cookie 用分号分隔，分号后带一个空格(例如， "fruit=apple; colour=red")。
        curl_setopt($this->ch, CURLOPT_COOKIE, $str);
        return $this;
    }

    /**
     * desc 设置cookie 参数是数组格式
     * @param $arr
     * @return $this
     */
    public function setArrayCookie($arr)
    {
        //设定 HTTP 请求中"Cookie: "部分的内容。多个 cookie 用分号分隔，分号后带一个空格(例如， "fruit=apple; colour=red")。
        $str = implode('; ', $arr);
        curl_setopt($this->ch, CURLOPT_COOKIE, $str);
        return $this;
    }

    /**
     * desc 设置referer
     * @param $str
     * @return $this
     */
    public function setReferer($str)
    {
        curl_setopt($this->ch, CURLOPT_REFERER, $str);
        return $this;
    }

    /**
     * desc 设置ua
     * @param $str
     * @return $this
     */
    public function setUserAgent($str)
    {
        curl_setopt($this->ch, CURLOPT_USERAGENT, $str);
        return $this;
    }

    /**
     * desc 设置头信息, 参数为数组形式
     * @param $arr
     * @return $this
     */
    public function setArrayHearder($arr)
    {
        // 设置 HTTP 头字段的数组。格式： array('Content-type: text/plain', 'Content-length: 100');
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $arr);
        return $this;
    }

    /**
     * desc 忽略ssl验证
     * @return $this
     */
    public function ignoreSSL()
    {
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); //容易受到中间人攻击 MITM
        // curl_setopt($this->ch, CURLOPT_SSL_VERIFYSTATUS, false); //php 7.0.7生效
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        return $this;
    }

    /**
     * @desc 在尝试连接时等待的秒数
     * @param $second
     * @return object
     */
    public function setConnectTimeOut($second)
    {
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $second);
        return $this;
    }

    /**
     * @desc 尝试连接等待的时间，以毫秒为单位
     * @param $msecond
     * @return object
     */
    public function setConnectTimeOutMs($msecond)
    {
        //如果 libcurl 编译时使用系统标准的名称解析器（ standard system name resolver），那部分的连接仍旧使用以秒计的超时解决方案，最小超时时间还是一秒钟
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $msecond);
        return $this;
    }

    /**
     * @desc 允许 cURL 函数执行的最长秒数
     * @param $second
     * @return object
     */
    public function setExecTimeOut($second)
    {
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $second);
        return $this;
    }

    /**
     * @desc 设置cURL允许执行的最长毫秒数
     * @param $msecond
     * @return object
     */
    public function setExecTimeOutMs($msecond)
    {
        //如果 libcurl 编译时使用系统标准的名称解析器（ standard system name resolver），那部分的连接仍旧使用以秒计的超时解决方案，最小超时时间还是一秒钟
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $msecond);
        return $this;
    }

    /**
     * 是否允许跟随重定向
     * @param $bool
     * @return $this
     */
    public function setFollowAction($bool)
    {
        //TRUE 时将会根据服务器返回 HTTP 头中的 "Location: " 重定向。（注意：这是递归的，"Location: " 发送几次就重定向几次，除非设置了 CURLOPT_MAXREDIRS，限制最大重定向次数。）
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $bool);
        return $this;
    }

    /**
     * 运行跟随重定向的次数
     * @param $times
     * @return $this
     */
    public function setMaxRedirect($times)
    {
        //CURLOPT_MAXREDIRS
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, $times);
        return $this;
    }

    /**
     * desc 发起get请求
     */
	public function get()
	{
        curl_setopt($this->ch, CURLOPT_HTTPGET, true);
		$this->output = curl_exec($this->ch);
        return $this->output;
	}

    /**
     * desc 设置post数据
     * @param $arr
     * @return $this
     */
	public function setPostData($arr)
    {
        //这个参数可以是 urlencoded 后的字符串，类似'para1=val1&para2=val2&...'，
        //也可以使用一个以字段名为键值，字段数据为值的数组。 如果value是一个数组，Content-Type头将会被设置成multipart/form-data。
        //传递一个数组到CURLOPT_POSTFIELDS，cURL会把数据编码成 multipart/form-data，而然传递一个URL-encoded字符串时，数据会被编码成 application/x-www-form-urlencoded。
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $arr);
        return $this;
    }

    /**
     * desc 发起post请求
     */
	public function post()
	{
        $this->output = curl_exec($this->ch);
        return $this->output;
	}

    /**
     * desc get, 重试机制
     * @param string $target 目标中包含该字符表示成功, 可以终止重试
     * @param int $times
     * @param int $msecond 间隔多少毫秒再进行重试
     * @return mixed
     */
	public function getRetry($target, $times, $msecond)
    {
        for ($i=0; $i<$times; $i++) {
            self::$reTryCounter++;
            $this->get();
            
            if (strpos($this->output, $target) !== false) {
                return $this->output;
            } else {
                usleep($msecond);
            }
        }
        return false;
    }

    /**
     * desc post, 重试机制
     * @param string $target 目标中包含该字符表示成功, 可以终止重试
     * @param int $times
     * @param int $msecond
     * @return mixed
     */
    public function postRetry($target, $times, $msecond)
    {
        for ($i=0; $i<$times; $i++) {
            self::$reTryCounter++;
            $this->post();
            if (strpos($this->output, $target) !== false) {
                return $this->output;
            } else {
                usleep($msecond);
            }
        }
        return false;
    }

    public function getError()
    {
        self::$error = 'ERROR_CODE: '.curl_errno($this->ch)."::ERROR_MESSAGE: ".curl_error($this->ch);
        return self::$error;
    }

}