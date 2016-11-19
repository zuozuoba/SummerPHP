<?php
class DBmysql
{
    private static $Instance = NULL;//本类的实例
	private static $links = array();//mysql链接数组

	private $link = null; //当前连接
    public $dbType = 'read';

	public $_host=''; //数据库所在主机名
    public $_database = '';//当前数据库名
    public $_tablename = '';//当前表的表名
    public $_dt ='';//database.tablename
    public $isRelease = 0; //查询完成后是否释放

    public $fields = '*';
	public $arrWhere = [];
	public $order = '';
	public $arrOrder = [];
	public $limit = '';

	public static $sql = ''; //只保存一份sql查询语句, 方便调试, e.g. echo Test::$sql;

	public $rs;//结果集

	//构造函数
    public function __construct($database='', $tablename='', $isRelease=0)
    {
		$this->_database = $database;//database name
		$this->_tablename = $tablename;//table name
		$this->_dt = "`{$database}`.`{$tablename}`";
		$this->isRelease = $isRelease;
    }

	//获取本类的实例
	//也就是说, get_class_method()只有本类定义的函数
	//可能还没有mysql连接
	public static function getInstance($modelName, $isRelease=0)
	{
		$tableinfo 	= DBConfig::getDBInfo($modelName);
		$database = $tableinfo[0];//database name
		$tablename = $tableinfo[1];//table name

		if (empty(self::$Instance)) {
			self::$Instance = new DBmysql($database, $tablename, $isRelease);
		}

		return self::$Instance;
	}

	public static function link($modelName, $isRelease=0)
	{
		return self::getInstance($modelName, $isRelease);
	}


	//如果主机没变,并且已经存在MYSQL连接,就不再创建新的连接
	//如果主机改变,就再生成一个实例创建一个连接
    //$type == 'write'或'read'
	public function getConnect($type)
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
			self::$links[$host] = new mysqli($host, $username, $password, $this->_database);
            if(self::$links[$host]->connect_error) {
                $this->error(self::$links[$host]->connect_error);
            }
		}

		//初始化链接
		$this->link = self::$links[$host];
		$this->link->query("set names utf8mb4;"); //支持emoji表情
		$this->link->query("use {$this->_database};");
	}
	
	public function getCurrentLinks()
	{
		return self::$links;
	}
	
	//析构函数
	public function __destruct()
	{
        foreach (self::$links as $v) {
            $v->close();
        }
	}
	
	//查询封装
	public function query($sql)
	{
		self::$sql = $sql; //因为增删改查的sql语句不一样, 因此在这里记录最终的sql语句

        if (strpos($sql, 'select') !== false) {
            $this->getConnect('read');//读库
        } else {
            $this->getConnect('write');//写库
        }

		$this->rs = $this->link->query($sql);
		($this->rs === false) && $this->error('sql error: '.$sql.PHP_EOL.$this->link->error);

		//查询完成后释放链接, 并删除链接对象
        if ($this->isRelease) {
            $this->link->close();
	        unset(self::$links[$this->_host]);
        }
		return $this->rs;
	}

	//增
	public function insert($arrData)
	{
		foreach ($arrData as $key=>$value) {
			$fields[] = $key;
			$values[] = "'".$value."'";
			// $fields[] = '`'.$key.'`';
			// $values[] = "'".$value."'";
		}
		$strFields = implode(',', $fields);
		$strValues = implode(',', $values);
		$sql = "insert into {$this->_dt} ($strFields) values ($strValues)";
		$this->query($sql);
		$insert_id = $this->link->insert_id;
		
		return $insert_id;
	}

	//增
	public function replace($arrData)
	{
		foreach ($arrData as $key=>$value) {
			$fields[] = $key;
			$values[] = "'{$value}'";
		}
		$strFields = implode(',', $fields);
		$strValues = implode(',', $values);
		$sql = "replace into {$this->_dt} ($strFields) values ($strValues)";
		
		$this->query($sql);
		
		return $this->link->affected_rows;
	}
	
	//增
	//每次插入多条记录
	//每条记录的字段相同,但是值不一样
	public function insertm($strFields, $arrData)
	{
		foreach ($arrData as $v) {
			$v = "'".$v."'";
			$data[] = '('.implode(',', $v).')';
		}
		
		$strData = implode(',', $data);
		
		$sql = "insert into {$this->_dt} ($strFields) values {$strData}";
		
		$this->query($sql);
		
		return $this->link->insert_id;
	}
	
	//删
	public function delete()
	{
        $where = $this->getWhere();
		if (empty($where)) {
			$this->error('删除时where条件不能为空!');
		}

        $limit = $this->getLimit();

		$sql = " delete from {$this->_dt} {$where} {$limit}";
		$this->query($sql);
		return $this->link->affected_rows;
	}
	
	//改
	public function update($data)
	{
        $where = $this->getWhere();
		if (empty($where)) {
			$this->error('更新时where条件不能为空!');
		}

		$arrSql = array();
		foreach ($data as $key=>$value) {
			if (strpos($value, '+')  || strpos($value, '-')) {
				$arrSql[] = "{$key}={$value}"; //update语句中如果有加号或减号就不再拼接单引号, 例如set a = a + 1;
			} else {
				$arrSql[] = "{$key}='{$value}'";
			}
		}
		$strSql = implode(',', $arrSql);
		
		$sql = "update {$this->_dt} set {$strSql} {$where} {$this->limit}";
		
		$this->query($sql);
		
		return $this->link->affected_rows;
	
	}

	//获取总数
	public function getCount()
	{
		$where = $this->getWhere();
		
		$sql = " select count(1) as n from {$this->_dt} {$where} ";
		$this->rs = $this->query($sql);
		
		($this->rs===false) && $this->error('getCount error: '.$sql);
		
		$arrRs = $this->rsToArray();
		
		$num = array_shift($arrRs);
		return $num['n'];
	}
	
	//将结果集转换成数组返回
	//如果field不为空，则返回的数组以$field为键重新索引
	public function rsToArray($field = '')
	{
		$arrRs = $this->rs->fetch_all(MYSQLI_ASSOC); //该函数只能用于php的mysqlnd驱动
		$this->rs->free();//释放结果集
		
		if ($field) {
			$arrResult = [];
			foreach ($arrRs as $v) {
				$arrResult[$v[$field]] = $v;
			}
			return $arrResult;
		}
		
		return $arrRs;
	}

	//处理入库数据,将字符串格式的数据转换为...格式(未实现)
	public function getInsertData($strData)
	{
		// $bmap = "jingdu,$jingdu;weidu,$weidu;content,$content";
	}

	//select in
	//arrData 整数数组，最好是整数
	public function select_in($key, $arrData, $fields='')
	{
		$fields = $fields ? $fields : '*';
		sort($arrData);
		$len = count($arrData);
		$cur = 0;
		$pre = $arrData[0];

		$new = array('0' => array($arrData[0]));
		for ($i = 1; $i < $len; $i++) {
			if (($arrData[$i] - $pre) == 1 ) {
				$new[$cur][] = $arrData[$i];
			} else {
				$cur = $i;
				$new[$cur][] = $arrData[$i];
			}
			$pre = $arrData[$i];
		}

		$arrSql = array();
		foreach ($new as $v) {
			$len = count($v) - 1;
			if ($len) {
				$s = $v[0];
				$e = end($v);
				$sql = "(select $fields from {$this->_dt} where $key between $s and $e)";
			} else {
				$s = $v[0];
				$sql = "(select $fields from {$this->_dt} where $key = $s)";
			}

			$arrSql[] = $sql;
		}
		
		$strUnion = implode(' UNION ALL ', $arrSql);
		$res = $this->query($strUnion);
		return $this->rstoarray($res);
	}
	
	//where in
	public function setWhereIn($key, $arrData)
	{
		if (empty($arrData)) {
			$str = "(`{$key}` in ('0'))";
			$this->addWhere($str);
			return $str;
		}
		
		foreach ($arrData as &$v) {
			$v = "'{$v}'";
		}
		$str = implode(',', $arrData);
		$str = "(`{$key}` in ( {$str} ))";
		
		$this->addWhere($str);
		
		return $this;
	}
	
	//where in
	public function setWhere($arrData)
	{
		if (empty($arrData)) {
			return '';
		}
		
		foreach ($arrData as $k => $v) {
			$str = "(`{$k}` = '{$v}')";
			$this->addWhere($str);
		}
		
		return $this;
	}
	
	//between and
	public function setWhereBetween($key, $min, $max)
	{
		$str = "(`{$key}` between '{$min}' and '{$max}')";
		$this->addWhere($str);
		return $this;
	}
	
	//where a>b
	public function setWhereGT($key, $value)
	{
		$str = "(`{$key}` > '{$value}')";
		$this->addWhere($str);
		return $this;
	}
	
	//where a<b
	public function setWhereLT($key, $value)
	{
		$str = "(`{$key}` < '{$value}')";
		$this->addWhere($str);
		return $this;
	}
	
	//组装where条件
	public function addWhere($where)
	{
		$this->arrWhere[] = $where;
	}
	
	//获取最终查询用的where条件
	public function getWhere()
	{
		if (empty($this->arrWhere)) {
			return 'where 1';
		} else {
			return 'where '.implode(' and ', $this->arrWhere);
		}
	}
	
	//以逗号隔开
	public function setFields($fields)
	{
		$this->fields = $fields;
		return $this;
	}
	
	// order by a desc
	public function setOrder($order)
	{
		$this->arrOrder[] = $order;
		return $this;
	}
	
	//获取order语句
	public function getOrder()
	{
		if (empty($this->arrOrder)) {
			return '';
		} else {
			$str = implode(',', $this->arrOrder);
			$this->order = "order by {$str}";
		}
		return $this->order;
	}
	
	//e.g. '0, 10'
	//用limit的时候可以加where条件优化：select ... where id > 1234 limit 0, 10 
	public function setLimit($limit)
	{
		$this->limit = 'limit '.$limit;
		return $this;
	}

	//直接查询sql语句, 返回数组格式
	public function arrQuery($sql, $field='')
	{
		$this->query($sql);
		$this->clearQuery();
		($this->rs===false) && $this->error('select error: '.$this->sql);
		return $this->rsToArray($field);
	}
	
	//如果 $field 不为空, 则返回的结果以该字段的值为索引
	//暂不支持join
	public function get($field='')
	{
		$where = $this->getWhere();
		$order = $this->getOrder();

		$sql =  " select {$this->fields} from {$this->_dt} {$where} {$order} {$this->limit} ";
		return $this->arrQuery($sql, $field);
	}
	
	//获取一条记录
	public function getOne()
	{
		$this->setLimit(1);
		$rs = $this->get();

		return !empty($rs) ? $rs[0] : [];
	}
	
	//获取一条记录的某一个字段的值
	public function getOneField($field)
	{
		$this->setFields($field);
		$rs = $this->getOne();

		return !empty($rs[$field]) ? $rs[$field] : '';
	}

	//获取数据集中所有某个字段的值
	public function getFields($field, $index='')
	{
		if ($index) {
			$this->setFields($field, $index);
			$rs = $this->get();
			return array_column($rs, $field, $index); //以$index字段的值做索引, 以$field字段的值做值
		} else {
			$this->setFields($field);
			$rs = $this->get();
			return array_column($rs, $field);
		}
	}
	
	//清除查询条件
	//防止干扰下次查询
	public function clearQuery()
	{
		$this->fields = '*';
		$this->arrWhere = [];
		$this->order = '';
		$this->arrOrder = [];
		$this->limit = '';
	}

    //断开数据库连接
    public function close()
    {
        $this->link->close();
    }

    //事务
    //自动提交开关
    public function autocommit($bool)
    {
        $this->link->autocommit($bool);
    }

    //事务完成提交
    public function commit()
    {
        $this->link->commit();
    }

    //回滚
    public function rollback()
    {
        $this->link->rollback();
    }


    //输出错误sql语句
	public function error($sql)
	{
		if (ENV == 'dev') {
			exit($sql);
		} else {
			return false;
		}
	}
}