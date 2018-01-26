<?php
// 需要预定义 UNIQID (高并发时区分不同客户端的请求, 跟踪id)
class FileLog
{
    private $day = '';
    private $time = '';
    private $dir = ''; //disk_free_space()
    private $file = '';
    private $ext = '.log';
    private $filepath = '';
    private $isSuccess = false;
    private $caller = ''; //记录谁调用了本类
    private $prefix = ''; //额外的前缀
    private $logPath = '/tmp/phplog/'; //日志存放目录的统一前缀
    private $separtor = ' --> ';

    /**
     * @param array|string $dir 要写入的目录
     * @param string $file 文件名, 默认为当天的日期
     * @return $this
     * @throws Exception
     */
    public static function ini($dir='', $file='')
    {
        $obj = new FileLog();
        $obj->dir($dir, $file);
        return $obj;
    }

    public function __construct()
    {
        $this->day = date('Y-m-d');
        $this->time = date('Y-m-d H:i:s');
    }
    
    /**
     * 设置日志的默认前缀, 不设置则用默认值
     * @param $str
     * @return $this
     */
    public function setLogPath($str)
    {
        $this->logPath = $str;
        return $this;
    }
    
    /**
     * 设置日志信息的分隔符, 不设置则用默认值
     * @param $str
     * @return $this
     */
    public function setSepartor($str)
    {
        $this->separtor = $str;
        return $this;
    }

    /**
     * @param array $dir 要写入的目录
     * @param string $file 文件名, 默认为当天的日期
     * @return $this
     * @throws Exception
     */
    private function dir($dir, $file='')
    {
        //组装目录
        if (is_array($dir)) {
            $this->dir = $this->logPath.implode(DIRECTORY_SEPARATOR, $dir);
        } else {
            $this->dir = $this->logPath.$dir;
        }
        

        //创建目录
        if (file_exists($this->dir) === FALSE) {
            if (!mkdir($this->dir, 0666, true)) {
                throw new Exception('创建日志目录失败: '.$this->dir);
            }
        }

        //组装文件完整路径
        $this->file = empty($file) ? $this->day : $file;
        $this->filepath = $this->dir.DIRECTORY_SEPARATOR.$this->file.$this->ext;

        return $this;
    }
    
    /**
     * 日志信息以外的备注信息
     * @param $str
     * @return $this
     */
    public function prefix($str)
    {
        $this->prefix = $str;
        return $this;
    }
    
    /**
     * 谁调用了本日志
     * @param $str
     * @return $this
     */
    public function caller($str)
    {
        $this->caller = $str;
        return $this;
    }

    /**
     * 记录日志
     * @param string|array|object $msg 日志信息
     * @return $this
     */
    public function info($msg)
    {
        if (is_array($msg) || is_object($msg)) {
            $msg = json_encode($msg);
        }

        //获取调用者信息
        if (empty($this->caller)) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $callInfo = [];
            if (!empty($trace[1])) {
                $callInfo[] = !empty($trace[1]['class'])    ? $trace[1]['class'] : '';
                $callInfo[] = !empty($trace[1]['function']) ? $trace[1]['function'] : '';
                $callInfo[] = !empty($trace[1]['line'])     ? $trace[1]['line'] : '';
        
                $callInfo = array_filter($callInfo);
            }
            $this->caller = implode(':', $callInfo);
        }
        

        $arr = [UNIQID, $this->time, $this->caller, $this->prefix, $msg];
        $string = implode($this->separtor, $arr).PHP_EOL;

        $this->isSuccess = file_put_contents($this->filepath, $string, FILE_APPEND ); //追加模式

        return $this;
    }

    public function isSuccess()
    {
        if ($this->isSuccess === false) {
            return false;
        } else {
            return true;
        }
    }
}
