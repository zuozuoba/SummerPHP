<?php
class Response
{
	public $code = '1';
	public $msg = '';
	public $data = '';
	const STATUS_SUCCESS = 1;
	const STATUS_FAILURE = -1;

	public static function ini($data='')
	{
		return new Response($data);
	}

	public function __construct($data='')
	{
		$this->data = $data;
	}
    
    /**
     * 设置返回码
     * @param int $code
     * @return $this
     */
	public function code($code=1)
	{
		$this->code = $code;
		return $this;
	}
    
    /**
     * 设置返回消息
     * @param string $msg
     * @return $this
     */
	public function msg($msg='')
	{
		$this->msg = $msg;
		return $this;
	}
    
    /**
     * 设置返回数据
     * @param mixed $data
     * @return $this
     */
	public function data($data)
	{
		$this->data = $data;
		return $this;
	}
    
    /**
     * 输出固定结构的json格式数据
     * @param INT $option 编码选项 (例如: JSON_UNESCAPED_UNICODE 汉字原样输出, 不再编码为uXXXX php5.4)
     */
	public function json($option = NULL)
	{
		header("Content-type: application/json; charset=utf-8");
		exit(json_encode(array( 'code' => $this->code, 'msg' => $this->msg, 'data' => $this->data ), $option));
	}
    
    /**
     * 返回任意结构的json
     * @param mixed $data
     * @param INT $option 编码选项 (例如: JSON_UNESCAPED_UNICODE 汉字原样输出, 不再编码为uXXXX php5.4)
     */
    public static function jsonReturn($data, $option = NULL)
    {
        header("Content-type: application/json; charset=utf-8");
        
        exit(json_encode($data, $option));
    }

	/**
	 * desc 成功时, 返回json数据
	 * @param string $msg
	 * @param array $rs
	 * @param null $code
	 */
	public static function success($rs = array(), $msg = 'success', $code = NULL)
	{
		header("Content-type: application/json; charset=utf-8");
        $code = isset($code) ? $code : self::STATUS_SUCCESS;
		exit(json_encode(array('code' => $code, 'msg' => $msg, 'data' => $rs)));
	}

	/**
	 * desc 失败时, 返回json数据
	 * @param string $msg
	 * @param array $rs
     * @param null $code
	 */
	public static function error($msg='', $rs = array(), $code = NULL)
	{
		header("Content-type: application/json; charset=utf-8");
        $code = isset($code) ? $code : self::STATUS_FAILURE;
		exit(json_encode(array('code' => $code, 'msg' => $msg, 'data' => $rs)));
	}

	//提示信息，不跳转
	public static function notify($msg)
	{
		header("Content-type: text/html; charset=utf-8");
		exit($msg);
	}

	//提示信息， 并跳转到其他页面
	public static function redirect($msg, $url, $second = 2)
	{
		header("Content-type: text/html; charset=utf-8");
		header("refresh:{$second};url={$url}");
		exit($msg);
	}
    
    /**
     * 只返回数组, 不退出
     * @param mixed $data
     * @param null $code
     * @param string $msg
     * @return array
     */
    public function ok($data, $code = NULL, $msg = '')
    {
        $code = isset($code) ? $code : self::STATUS_SUCCESS;
        return array('code' => $code, 'msg' => $msg, 'data' => $data);
    }
    
    /**
     * 只返回数组, 不退出
     * @param $data
     * @param null $code
     * @param string $msg
     * @return array
     */
    public function wrong($msg, $code = NULL,  $data = array())
    {
        $code = isset($code) ? $code : self::STATUS_FAILURE;
        return array('code' => $code, 'msg' => $msg, 'data' => $data);
    }
	
}