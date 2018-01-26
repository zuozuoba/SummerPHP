<?php
class DBmysql
{
	private static $links = array();//mysql链接数组

    public $dbType = 'read';

	public $_host=''; //数据库所在主机名
    public $_database = '';//当前数据库名
    public $_tablename = '';//当前表的表名
    public $tablename = '';//当前表的表名
    public $_dt ='';//database.tablename
	public $modelName = ''; //虚拟表名, 对应DBConfig中$TableInfo的键名
    public $isRelease = 0; //查询完成后是否释放
	public $insertId = 0;
	public $affectRows = 0;
	public $custom = FALSE; //是否是直接查询SQL语句, query($sql)

	public $rs;
	public static $data = array(); //被 Data::ini() 使用

	public static $sqls = [];
	public static $currentSql = '';

	use CRUD; // php 5.4+

	//构造函数
    private function __construct($database='', $tablename='', $isRelease=0)
    {
        $this->_database = $database;//数据库名
        $this->_tablename = $tablename;//表名
        $this->tablename = $tablename;//表名
        $this->isRelease = $isRelease;
    }

	/**
	 * desc 获取链接实例
	 * @param string  $modelName model名
	 * @param int $isRelease 执行完sql语句后是否关闭连接，大并发下需要关闭连接
	 * @return DBmysql|null
	 */
	public static function link($modelName, $isRelease=0)
	{
		$tableinfo 	= DBConfig::getDBInfo($modelName);
		$database = $tableinfo[0];//database name
		$tablename = $tableinfo[1];//table name

        return new self($database, $tablename, $isRelease);

	}

	//如果主机没变,并且已经存在MYSQL连接,就不再创建新的连接
	//如果主机改变,就再生成一个实例创建一个连接
    //$type == 'write'或'read'
	private function getConnect($type)
	{
        $this->dbType = $type;

        //随机选取一个数据库连接(区分读写)
        $dbConfig = DBConfig::$$type;
        $randKey = array_rand($dbConfig);
        $config = $dbConfig[$randKey];
        
        //链接数据库
        $host = $config['host'];
        $username = $config['username'];
        $password = $config['password'];

		if (empty(self::$links[$host])) {
			$this->_host = $host;
			$mysqli = mysqli_init(); //初始化mysqli
			$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 2); //超时2s
			$mysqli->options(MYSQLI_INIT_COMMAND, "set names utf8mb4;");

			if ($mysqli->real_connect($host, $username, $password, $this->_database)) {
				self::$links[$host] = $mysqli;
			} else {
				$this->error(mysqli_connect_error());
			}
		}
	}

	/**
	 * 查询封装
	 * @param string $sql
	 * @return mixed
	 */
	public function query($sql='')
	{
		if (!empty($sql)) {
		    $this->custom = TRUE;
			self::$sqls[] = self::$currentSql = $sql;
		} else {
            self::$sqls[] = self::$currentSql = $this->sql();
		}
		
		Timer::start(self::$currentSql);
  
		$sql = strtolower(self::$currentSql);
		
		if (strlen($sql) == 0) {
		    $this->error('待执行的SQL语句为空');
        }
		
        if (strpos($sql, 'select') !== false) {
            $this->getConnect('read');//读库
        } else {
            $this->getConnect('write');//写库
        }
        
		$this->clearQueryParam(); //清除查询条件
        
		//执行查询语句
		$this->rs = self::$links[$this->_host]->query(self::$currentSql);
		
		($this->rs === FALSE) && $this->error(self::$links[$this->_host]->error);

		if (strpos($sql, 'replace') !== FALSE) {
			$this->affectRows = self::$links[$this->_host]->affected_rows;

		} elseif (strpos($sql, 'insert') !== FALSE) {
			$this->insertId = self::$links[$this->_host]->insert_id;

		} elseif (strpos($sql, 'delete') !== FALSE) {
			$this->affectRows = self::$links[$this->_host]->affected_rows;

		} elseif (strpos($sql, 'update') !== FALSE) {
			$this->affectRows = self::$links[$this->_host]->affected_rows;
		}

		//查询完成后释放链接, 并删除链接对象
		if ($this->isRelease) {
			self::$links[$this->_host]->close();
			unset(self::$links[$this->_host]);
		}

		Timer::over(self::$currentSql);

		return $this;
	}

	//将结果集转换成数组, 一个一个返回, 如果本函数的返回值会被foreach, 就用此函数
	//如果field不为空，则返回的数组以$field为键重新索引
	public function rsToArrayYield($field = '')
	{
		if ($field) {
			while ($row = $this->rs->fetch_assoc()) {
				$tmp = [];
				$tmp[$row[$field]] = $row;
				yield $tmp;
			}
		} else {
			while ($row = $this->rs->fetch_assoc()) {
				yield $row; //不一次性获取全部数组到内存, 用一个取一个, 返回值的数据类型为"生成器"
			}
		}
	}

	//一次性获取所有数据到内存
	//如果field不为空，则返回的数组以$field为键重新索引
	public function getAll($field='')
	{
	    !$this->custom && $this->query();

		$rs = [];
		if (empty($field)) {
			return $this->rs->fetch_all(MYSQLI_ASSOC); //该函数只能用于php的mysqlnd驱动
		} else {
			while ($row = $this->rs->fetch_assoc()) {
				$rs[$row[$field]] = $row;
			}
			// $this->rs = $rs;
			return $rs;
		}
	}
	
	//配合 Data 类使用, 链式调用PHP自带的函数, 继续处理数据集
	public function data()
    {
        !$this->custom && $this->query();
    
        self::$data = $this->rs->fetch_all(MYSQLI_ASSOC); //该函数只能用于php的mysqlnd驱动
        
        return Data::ini(self::$data);
    }

	//获取一条记录
	public function getOne()
	{
        !$this->custom && $this->limit(1)->select()->query();
		
		$rs = $this->rs->fetch_assoc();

		return !empty($rs) ? $rs : [];
	}
	
	//获取一条记录的某一个字段的值
	public function getOneField($field)
	{
        !$this->custom && $this->fields($field)
            ->limit(1)
            ->select()
            ->query();
        
        $rs = $this->rs->fetch_assoc();
        
		return isset($rs[$field]) ? $rs[$field] : '';
	}

	//获取数据集中所有某个字段的值
	public function getFields($field, $index='')
	{
		$rs = $this->getAll();
		if (!empty($index)) {
			return array_column($rs, $field, $index); //以$index字段的值做索引, 以$field字段的值做值
		} else {
			return array_column($rs, $field);
		}
	}

	//获取总数
	public function getCount()
	{
	    $this->query();
        $rs = $this->rs->fetch_assoc();
        return isset($rs['SUMMER_N']) ? $rs['SUMMER_N'] : 0;
	}

    //断开数据库连接
    public function close()
    {
		self::$links[$this->_host]->close();
    }

    //事务
    //自动提交开关
    public function autocommit($bool)
    {
		self::$links[$this->_host]->autocommit($bool);
    }
    
    //事务开始
    public function begin_transaction($flag=MYSQLI_TRANS_START_READ_WRITE, $name=null)
    {
        self::$links[$this->_host]->begin_transaction($flag, $name);
    }

    //事务完成提交
    public function commit()
    {
		self::$links[$this->_host]->commit();
    }

    //回滚
    public function rollback()
    {
		self::$links[$this->_host]->rollback();
    }

	//获取当前连接
	public function getCurrentLinks()
	{
		return self::$links;
	}
}