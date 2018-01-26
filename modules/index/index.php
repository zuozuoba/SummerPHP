<?php
class _index
{
	public function initc()
	{
	}
    
    public function index()
    {
        Response::notify('Hello World');
    }
    
    public function sql()
	{
        Test::link('user')
            ->fields('age, username')
            ->whereGT('uid', 0)
            ->select()
            ->data()
            ->group('age')
            ->pre();

         exit();
            
        $sql = 'select * from user';
        
        Test::link('user')
            ->query($sql)
            ->data()
            ->array_column('age', 'username')
            ->array_sum()
            ->pre();
        echo '<pre>';print_r(Test::$data); exit();
        
        
        $sql = 'select * from user where uid > 1';

        $rs = Test::link('user')
            ->query($sql)
            ->getFields('age');
        echo '<pre>';print_r($rs); exit();
        
        $rs = Test::link('user')
            ->joinFields('user', 'username, age')
            ->joinFields('grade', 'name')
            ->joinTable('user', 'grade_id', 'grade', 'id')
            ->whereGT('user.uid', 0)
            ->limit('5')
            ->order('user.uid asc')
            ->join()
            ->getAll();
        echo '<pre>';print_r($rs); exit();
        
	    $affectRows = Test::link('user')
            ->where(['uid' => 1])
            ->updateVal(['age' => 20])
            ->addUpdate('a = a + b + C')
            ->update()
            ->query()
            ->affectRows;
	    echo '<pre>';var_dump($affectRows);
	    
	    
        $rs = Test::link('user')
            ->where(['uid' => 1])
            ->updateVal(['sex' => 2, 'class' => 3, 'name' => '张三'])
            ->updateVal(['a' => '13100000000']) //此时手机号在最终的SQL中'会'加上引号
            ->updateVal(['b' => 13100000000]) //此时手机号在最终的SQL中'不会'加上引号
            ->update()
            ->query()
            ->affectRows;
        echo '<pre>';var_dump($rs);
            
	    //插入并返回id
	    $insertId = Test::link('user')
            ->insert(['a' => 1, 'b' => '1', 'c' => 'cc'])
            ->query()
            ->insertId;
        //INSERT INTO user (a,b,c) VALUES (1,1,'cc')

	    //一次插入多条记录
        $insertId = Test::link('user')
            ->insertm('a, b', array(array(1,'aa'), array(2, 'bb'), array(3, 'cc')))
            ->query()
            ->insertId;
        //INSERT INTO user (a, b) VALUES (1,'aa'),(2,'bb'),(3,'cc')

	    //replace into
        $affectRows = Test::link('user')
            ->replace(['a' => 1, 'b' => '1', 'c' => 'cc'])
            ->query()
            ->insertId;
        //REPLACE INTO user (a,b,c) VALUES (1,1,'cc')

	    //删除
        $affectRows = Test::link('user')
            ->where(array('a' => 1))
            ->delete()
            ->query();
        //DELETE FROM user WHERE (a = 1)
        
        //获取一条记录
        // >=, order by
		$rs = Test::link('user')
			->whereGE('uid', 1)
			->order('uid desc')
			->getOne();
		//SELECT * FROM user WHERE (uid >= 1) ORDER BY uid desc
        echo '<pre>';print_r($rs); echo Test::$currentSql;
    
        $rs = Test::link('user')
            ->fields('uid,username')
            ->whereGE('uid', 1)
            ->getOneField('age');
        echo '<pre>';print_r($rs); echo Test::$currentSql;
        
        //获取记录数
        $rs = Test::link('user')
            ->whereGE('age', 10)
            ->count()
            ->getCount();
        //SELECT COUNT(1) AS SUMMER_N FROM user WHERE (age >= 10)
        echo '<pre>';print_r($rs); echo Test::$currentSql;

		//获取多条记录
        // >, limit, order by
		$rs = Test::link('user')
            ->fields('age,username')
			->whereGT('uid', 1)
            ->order('uid desc')
			->limit(10)
			->select()
			->getAll();
        echo '<pre>';print_r($rs); echo Test::$currentSql;
		//SELECT id,content FROM tiezi WHERE (id > 1) ORDER BY id desc LIMIT 10

		//获取多条记录
        // 以id字段的值做索引, 以content字段的值做值
		$rs = Test::link('user')
            ->fields('age,username')
			->whereGE('uid', 1)
			->limit(10)
			->select()
			->getFields('age');
        echo '<pre>';print_r($rs); echo Test::$currentSql;
        //SELECT id,content FROM tiezi WHERE (id >= 1) LIMIT 10

		//获取多条记录
        // select in
		$rs = Test::link('note')->fields('id,content')
			->whereGE('id', 2)
			->whereIn('id', [1,2,3,4,5,7,9])
			->select()//不改写select in
			->getFields('content', 'id');
		//SELECT id,content FROM tiezi WHERE (id >= 2) AND (id IN ( 1,2,3,4,5,7,9 ))

		//获取多条记录
        //改写select in, 1:将数字排序 2:改写成between and 3.用 unin all 联结
		$rs = Test::link('note')->fields('id,content')
			->whereGE('id', 2)
			->whereIn('id', [1,2,3,4,5,7,9])
			->selectIn() //改写select in, 将数字排序并写成between and 最后用 unin all 联结
			->getFields('content', 'id');
        //(SELECT id,content FROM tiezi WHERE (id >= 2) AND (id BETWEEN 1 AND 5) ) UNION ALL (SELECT id,content FROM tiezi WHERE (id >= 2) AND (id = 7) ) UNION ALL (SELECT id,content FROM tiezi WHERE (id >= 2) AND (id = 9) )

//		echo '<pre>';var_dump($rs, Test::$_error, Test::$sql, Timer::$list);

	}

	public function req()
    {
        echo '<pre>';
        var_dump(Request::Get('a'));
        var_dump(Request::Get('b'));
        var_dump(Request::Post('a'));
        var_dump(Request::Cookie('a'));
        var_dump(Request::Route('a'));
    }

    public function route()
    {
        echo '<pre>';
        var_dump(Request::Route('page'));
    }

	public function response()
	{
		$a = ['list' => [1,2,3,4]];

		Response::notify('页面找不到啦~'); //页面找不到啦~
		Response::jsonReturn($a); //{"list":[1,2,3,4]}

		Response::error('参数错误'); //{"code":"-1","msg":"\u53c2\u6570\u9519\u8bef","data":[]]}
		Response::error('参数错误', $a); //{"code":"-1","msg":"\u53c2\u6570\u9519\u8bef","data":{"list":[1,2,3,4]},"url":""}
		Response::error('参数错误', $a, 20001); //{"code":2001,"msg":"\u53c2\u6570\u9519\u8bef","data":{"list":[1,2,3,4]}}

		Response::success($a);
		Response::success($a, '用户列表');
		Response::success($a, '用户列表', 20000);

		Response::redirect('充值成功, 页面即将跳转', 'http://www.summer.com', 3);

		Response::ini($a)->json();  //{"code":"1","msg":"","data":{"list":[1,2,3,4]}}
		Response::ini()->code(50000)->msg('用户列表')->json(); //{"code":50000,"msg":"\u7528\u6237\u5217\u8868","data":""}
		Response::ini()->code(10000)->msg('用户列表')->data($a)->json(); //{"code":10000,"msg":"\u7528\u6237\u5217\u8868","data":{"list":[1,2,3,4]}}
    }

    public function icurl()
    {
        echo '<pre>';

        echo ICurl::ini('http://www.zhangzhibin.com/index/index/ss')
            ->setPort('80')
            ->setExecTimeOut(4)
            ->getRetry('^', 5, 100); //返回结果中不包含"^"字符就重试
        var_dump(ICurl::$reTryCounter);

        echo ICurl::ini('http://www.zhangzhibin.com/index/index/ss')
            ->setPostData(['aaa' => 124, 'bbb' => 456])
            ->postRetry(':', 5, 100);
        var_dump(ICurl::$reTryCounter);

        echo ICurl::ini('http://www.zhangzhibin.com/index/index/ss')
            ->setReferer('http://www.test.com')
            ->setArrayCookie(['a=123', 'b=456'])
            ->setPostData(['aaa' => 124, 'bbb' => 456])
            ->postRetry(':', 5, 100);
        var_dump(ICurl::$reTryCounter);

        echo ICurl::ini('https://www.baidu.com/')
            ->setUserAgent('Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.86 Safari/537.36')
            ->ignoreSSL()
            ->setMaxRedirect(3)
            ->setFollowAction(false)
            ->setReferer('http://www.zhangzhibin.com')
            ->get();
        var_dump(ICurl::$error); //array 所有错误
        var_dump(ICurl::getLastError()); // string 最后一个错误
    }

    public function flog()
    {
        FileLog::ini(['summer', 'test'], 'test')->info('哈哈哈哈');
        FileLog::ini(['summer', 'test'], 'test')->prefix('这里是日志前缀')->info('这里是日志内容');
        FileLog::ini(['summer', 'test'], 'test')->prefix('这里是日志前缀')->caller(__METHOD__)->info('这里是日志内容');
        FileLog::ini(['summer', 'test'], 'test')->prefix('这里是日志前缀')->caller(__METHOD__)->info('这里是日志内容')->isSuccess();

    }

    public function dirlist()
	{
		$rs = Dir::ini(ROOT)->rmPrefix('E:\code\SummerPHP\\');
		echo '<pre>'; print_r($rs);
		$rs = Dir::ini(ROOT)->getList();
		echo '<pre>'; print_r($rs);
	}
	
	public function redis()
    {
        IRedis::getInstance('dev');
        IRedis::getInstance('pro');
        
        echo '<pre>';var_dump(IRedis::$instance);
    }
}