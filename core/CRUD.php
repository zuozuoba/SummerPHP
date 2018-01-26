<?php
trait CRUD
{
    public $tablename = '';//当前表的表名

    public $fields = '*';
	public $arrWhere = [];
	public $order = '';
	public $arrOrder = [];
	public $limit = '';
	public $groupBy = '';
	public $having = '';
	public $arrUpdate = [];

	public $joinField = [];
	public $joinTable = [];
	public $joinOn = [];

	public static $sql = '';

	//查
	public function select()
	{
		$where = $this->getWhere();
		$order = $this->getOrder();

		self::$sql =  "SELECT {$this->fields} FROM {$this->tablename} {$where} {$this->groupBy} {$this->having} {$order} {$this->limit}";
		return $this;
	}

	//增, 另注: 主从切换时注意读写权限
	public function insert($arrData)
	{
		$this->safe($arrData);

		$fields = [];
		$values = [];
		foreach ($arrData as $key=>$value) {
			$fields[] = $key;
			$values[] = !is_string($value) ? $value : "'{$value}'";
		}
		$strFields = implode(',', $fields);
		$strValues = implode(',', $values);
		self::$sql = "INSERT INTO {$this->tablename} ($strFields) VALUES ($strValues)";
		return $this;
	}

	//增, 注意高并发下不要用 replace into 效率低而且容易死锁
	public function replace($arrData)
	{
		$this->safe($arrData);
		foreach ($arrData as $key=>$value) {
			$fields[] = $key;
			$values[] = !is_string($value) ? $value : "'{$value}'";
		}
		$strFields = implode(',', $fields);
		$strValues = implode(',', $values);
		self::$sql = "REPLACE INTO {$this->tablename} ($strFields) VALUES ($strValues)";
		return $this;
	}
	
	//增
	//每次插入多条记录
	//每条记录的字段相同,但是值不一样
	public function insertm($strFields, $arrData)
	{
		foreach ($arrData as $values) {
			foreach ($values as $k => $v) {
				$values[$k] = !is_string($v) ? $v : "'$v'";
			}

			$data[] = '('.implode(',', $values).')';
		}
		
		$strData = implode(',', $data);
		
		self::$sql = "INSERT INTO {$this->tablename} ($strFields) VALUES {$strData}";
		return $this;
	}
	
	//删
	public function delete()
	{
        $where = $this->getWhere();
		if (empty($where)) {
			$this->error('删除时where条件不能为空!');
		}

		self::$sql = "DELETE FROM {$this->tablename} {$where} {$this->limit}";
		return $this;
	}
	
	//改, 组装update语句
	public function update()
	{
        $where = $this->getWhere();
		if (empty($where)) {
			$this->error('更新时where条件不能为空!');
		}
		
		$strSql = implode(',', $this->arrUpdate);
		
		self::$sql = "UPDATE {$this->tablename} set {$strSql} {$where} {$this->limit}";
		return $this;
	}
	
	//改, 自定义set语句
	public function addUpdate($str)
    {
        $this->arrUpdate[] = $str;
        return $this;
    }
	
	//改 a = 1, a = 'b'
	public function updateVal($arrData)
    {
        foreach ($arrData as $field => $v) {
            $this->arrUpdate[] = !is_string($v) ? "{$field} = $v" : "{$field} = '$v'";
        }
        return $this;
    }
	
	//改: a = a + 1
    //$arrData  = array(['a', 'a', 1], ['b', 'c', 1])
	public function updateInc($arrData)
    {
        $where = $this->getWhere();
        if (empty($where)) {
            $this->error('更新时where条件不能为空!');
        } else {
            foreach ($arrData as $k => $row) {
                if (count($row) != 3) {
                    $this->error("第 {$k} 项的元素个数应为3个!");
                } else {
                    list($targetField, $sourceField, $numeric) = $row;
                    if (!is_numeric($numeric)) {
                        $this->error("{$targetField} = {$sourceField} + {$numeric} 中值不是数字");
                    } else {
                        $this->arrUpdate[] = "{$targetField} = {$sourceField} + {$numeric}";
                    }
                }
            }
        }
        return $this;
    }
    
    //改: a = a - 1
    //$arrData  = array(['a', 'a', 1], ['b', 'c', 1])
    public function updateDec($arrData)
    {
        $where = $this->getWhere();
        if (empty($where)) {
            $this->error('更新时where条件不能为空!');
        } else {
            foreach ($arrData as $k => $row) {
                if (count($row) != 3) {
                    $this->error("第 {$k} 项的元素个数应为3个!");
                } else {
                    list($targetField, $sourceField, $numeric) = $row;
                    if (!is_numeric($numeric)) {
                        $this->error("{$targetField} = {$sourceField} - {$numeric} 中值不是数字");
                    } else {
                        $this->arrUpdate[] = "{$targetField} = {$sourceField} - {$numeric}";
                    }
                }
            }
        }
        return $this;
    }
    
    //改: a = a * 1
    //$arrData  = array(['a', 'a', 1], ['b', 'c', 1])
    public function updateMul($arrData)
    {
        $where = $this->getWhere();
        if (empty($where)) {
            $this->error('更新时where条件不能为空!');
        } else {
            foreach ($arrData as $k => $row) {
                if (count($row) != 3) {
                    $this->error("第 {$k} 项的元素个数应为3个!");
                } else {
                    list($targetField, $sourceField, $numeric) = $row;
                    if (!is_numeric($numeric)) {
                        $this->error("{$targetField} = {$sourceField} * {$numeric} 中值不是数字");
                    } else {
                        $this->arrUpdate[] = "{$targetField} = {$sourceField} * {$numeric}";
                    }
                }
            }
        }
        return $this;
    }
    
    //改: a = a / 1
    //$arrData  = array(['a', 'a', 1], ['b', 'c', 1])
    public function updateDiv($arrData)
    {
        $where = $this->getWhere();
        if (empty($where)) {
            $this->error('更新时where条件不能为空!');
        } else {
            foreach ($arrData as $k => $row) {
                if (count($row) != 3) {
                    $this->error("第 {$k} 项的元素个数应为3个!");
                } else {
                    list($targetField, $sourceField, $numeric) = $row;
                    if (!is_numeric($numeric)) {
                        $this->error("{$targetField} = {$sourceField} / {$numeric} 中值不是数字");
                    } else {
                        $this->arrUpdate[] = "{$targetField} = {$sourceField} / {$numeric}";
                    }
                }
            }
        }
        return $this;
    }
    
    //改: a = a % 1
    //$arrData  = array(['a', 'a', 1], ['b', 'c', 1])
    public function updateMod($arrData)
    {
        $where = $this->getWhere();
        if (empty($where)) {
            $this->error('更新时where条件不能为空!');
        } else {
            foreach ($arrData as $k => $row) {
                if (count($row) != 3) {
                    $this->error("第 {$k} 项的元素个数应为3个!");
                } else {
                    list($targetField, $sourceField, $numeric) = $row;
                    if (!is_numeric($numeric)) {
                        $this->error("{$targetField} = {$sourceField} % {$numeric} 中值不是数字");
                    } else {
                        $this->arrUpdate[] = "{$targetField} = {$sourceField} % {$numeric}";
                    }
                }
            }
        }
        return $this;
    }

	//获取总数
	public function count()
	{
		$where = $this->getWhere();
		self::$sql = "SELECT COUNT(1) AS SUMMER_N FROM {$this->tablename} {$where}";
		return $this;
	}
	
	//select in
	//arrData 整数数组，最好是整数
	public function selectIn()
	{
		//取出where条件中的in语句
		$wherein = '';
		foreach ($this->arrWhere as $k => $v) {
			if (strpos($v, 'IN')) {
				$wherein = $v;
				unset($this->arrWhere[$k]);
				break;
			}
		}

		//整理数据
		list($field, $ids) = explode('IN', $wherein);
		$field = trim($field,'( '); //去掉括号和空格
		$ids = trim($ids,'() '); //去掉括号和空格
		$ids = str_replace(' ', '', $ids);

		$arrId = explode(',', $ids);
		$arrId = array_filter($arrId);
		$arrId = array_unique($arrId);

		//分组
//		sort($arrId);
		$len = count($arrId);
		$group = 0;

		$new = array(array($arrId[0]));
		for ($i = 1; $i < $len; $i++) {
			if (($arrId[$i] - $arrId[$i-1]) == 1 ) { //连续的整数
				$new[$group][] = $arrId[$i];
			} else {
				$group = $i;
				$new[$group][] = $arrId[$i];
			}
		}

		$where = $this->getWhere();
		$order = $this->getOrder();

		$where = strlen($where) ? $where : 'WHERE 1=1';
		$arrSql = array();
		foreach ($new as $v) {
			if (count($v) > 1) {
				$start = reset($v);
				$end = end($v);
				$tmp = $where." AND ($field BETWEEN $start AND $end)";
				$sql = "(SELECT {$this->fields} FROM {$this->tablename} {$tmp} {$this->groupBy} {$this->having} {$order} {$this->limit})";
			} else {
				$start = reset($v);
				$tmp = $where. " AND ($field = $start)"; //默认为where条件中的值为数值型
				$sql = "(SELECT {$this->fields} FROM {$this->tablename} {$tmp} {$this->groupBy} {$this->having} {$order} {$this->limit})";
			}

			$arrSql[] = $sql;
		}
		
		self::$sql = implode(' UNION ALL ', $arrSql);
		return $this;
	}
	
	//where
	public function where($arrData)
	{
		if (empty($arrData)) {
			return $this;
		}

		$this->safe($arrData);
		
		foreach ($arrData as $k => $v) {
            if (is_null($v) || is_bool($v) || is_object($v) || is_array($v)) {
                $this->error("第 {$k} 个值的数据类型不对!");
                unset($arrData[$k]);
            } else {
                $str = !is_string($v) ? "({$k} = {$v})" : "({$k} = '{$v}')";
                $this->addWhere($str);
            }
		}
		
		return $this;
	}
	
	//where in
	public function whereIn($key, $arrData)
	{
		if (empty($arrData)) {
			$str = "({$key} IN (0))";
			$this->addWhere($str);
		}

		$this->safe($arrData);

		$arrData = array_unique($arrData);

//		sort($arrData);

		foreach ($arrData as $k => $v) {
			if (is_null($v) || is_bool($v) || is_object($v) || is_array($v)) {
                $this->error("第 {$k} 个值的数据类型不对!");
				unset($arrData[$k]);
			} else {
                $arrData[$k] = !is_string($v) ? $v : "'{$v}'";
            }
		}

		$strData = implode(',', $arrData);

		$this->addWhere("({$key} IN ( {$strData} ))");
		
		return $this;
	}
	
	//between and
	public function whereBetween($key, $min, $max)
	{
		$this->safe($min);
		$this->safe($max);
        
        $min = !is_string($min) ? $min : "'{$min}'";
        $max = !is_string($max) ? $max : "'{$max}'";

		$str = "({$key} BETWEEN {$min} AND {$max})";
		$this->addWhere($str);
		return $this;
	}
	
	//where a>b
	public function whereGT($key, $value)
	{
		$this->safe($value);
        $str = !is_string($value) ? "({$key} > {$value})" : "({$key} > '{$value}')";
		
		$this->addWhere($str);
		return $this;
	}
	
	//where a<b
	public function whereLT($key, $value)
	{
		$this->safe($value);
        $str = !is_string($value) ? "({$key} < {$value})" : "({$key} < '{$value}')";
		
		$this->addWhere($str);
		return $this;
	}

	//where a>=b
	public function whereGE($key, $value)
	{
		$this->safe($value);
        $str = !is_string($value) ? "({$key} >= {$value})" : "({$key} >= '{$value}')";
		
		$this->addWhere($str);
		return $this;
	}

	//where a<=b
	public function whereLE($key, $value)
	{
		$this->safe($value);
        $str = !is_string($value) ? "({$key} <= {$value})" : "({$key} <= '{$value}')";
		
		$this->addWhere($str);
		return $this;
	}
	
	//添加自定义where条件
	public function addWhere($str)
	{
		$this->arrWhere[] = $str;
		return $this;
	}
	
	//获取最终查询用的where条件
	public function getWhere()
	{
		if (!empty($this->arrWhere)) {
            return 'WHERE '.implode(' AND ', $this->arrWhere);
		} else {
			return '';
		}
	}
	
	//以逗号隔开
	public function fields($fields)
	{
		$this->safe($fields);
		$this->fields = $fields;
		return $this;
	}
	
	// order by a desc
	public function order($order)
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
			$this->order = "ORDER BY {$str}";
		}
		return $this->order;
	}

	public function groupBy($str)
	{
		$this->groupBy = "GROUP BY {$str}";
		return $this;
	}

	public function having($str)
	{
		$this->having = "HAVING {$str} ";
	}
	
	//e.g. '0, 10'
	//用limit的时候可以加where条件优化：select ... where id > 1234 limit 0, 10 
	public function limit($limit)
	{
		$this->safe($limit);
		$this->limit = 'LIMIT '.$limit;
		return $this;
	}
	
	public function join()
	{
		$where = $this->getWhere();
		$order = $this->getOrder();
		$joinFields = $this->getJoinFields();
		$joinTable = $this->getJoinTable();

		self::$sql = "SELECT {$joinFields} FROM {$joinTable} {$where} {$this->groupBy} {$this->having} {$order} {$this->limit}";
		return $this;
	}

	/**
	 * 连接查询, 设置查询字段
	 * @param string $table
	 * @param string $fields
	 * @return $this
	 */
	public function joinFields($table, $fields)
	{
		$fields = str_replace(' ', '', $fields);
		$fields = explode(',', $fields);
		foreach ($fields as $k => $v) {
			$fields[$k] = $table.'.'.$v;
		}
		$this->joinField[] = implode(',', $fields);

		return $this;
	}

	public function getJoinFields()
	{
		return implode(', ', $this->joinField);
	}

	public function joinTable($table1, $field1, $table2, $field2)
	{
		$str = "$table1 LEFT JOIN $table2 ON $table1.$field1 = $table2.$field2";
		$this->joinTable[] = $str;

		return $this;
	}

	public function getJoinTable()
	{
		return implode('LEFT JOIN ', $this->joinTable);
	}
	
	public function sql()
	{
		return self::$sql;
	}
	
	public function safe(&$value)
	{
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				if (!is_numeric($v)) {
					$value[$k] = addslashes($v);
				}
			}
		} elseif (!is_numeric($value)) {
			$value = addslashes($value);
		}

	}

	public function clearQueryParam()
	{
		$this->arrWhere = [];
		$this->order = '';
		$this->arrOrder = [];
		$this->limit = '';
		$this->groupBy = '';
		$this->having = '';
		$this->joinField = [];
		$this->joinTable = [];
		$this->joinOn = [];
	}

	public function error($str)
	{
		// $this->error = $str;
        
        throw new Exception($str.'@=@'.self::$sql);
	}
}