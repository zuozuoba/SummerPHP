<h2>数据库类(MySQL)配置</h2>

<h3>作用</h3>
<p>一个是存放所有数据库连接必须的主机, 帐号密码</p>
<p>另一个是统一管理table与model的对应关系, 将model和table拆开</p>

<h3>配置文件</h3>
<pre>
class DBConfig
{
	//写库
	public static $write = array(
        array(
            'host' => '127.0.0.1',
            'username' => '',
            'password' => '',
        ));

	//读库
	public static $read = array(
        array(
            'host' => '127.0.0.1',
            'username' => '',
            'password' => '',
        ));

	public static $TableInfo = array(
		'user' => 'test, user', //虚拟表名 => 数据库名, 表名
		'goods' => 'test, goods',
		'log_(\d+)_(\d+)' => 'test, log_$1_$2', //正则匹配, 针对分库分表的情况(log_2016_0601)
	);

    public static function getDBInfo($modelName)
    {
        //找到第一个匹配的就返回
    }
}
</pre>

<pre>
//控制器中使用:
$rs = Test::link('user')->setWhere(['id' => '123'])->getOne();
$rs = Test::link('log_123_456')->setWhere(['id' => '123'])->getOne();

//Test模型(model)中使用:
$rs = self::link('user')->setWhere(['id' => '123'])->getOne();
</pre>

<ol>
	注意:
	<li>读库和写库是分开的(对应配置文件中的$read, $write), 在查询的时候会根据最终组装的SQL语句进行判断是连接读库还是写库</li>
	<li>Test是model, 而user就是配置文件中 $TableInfo 中的键("虚拟表名"), 这样表名改掉的话, 只要不改变"虚拟表名"就不影响程序调用</li>
	<li>如果遇到分表的情况下, 配置文件无须把所有子表都写进来, 只用写一个正则就行了</li>
	<li>getDBInfo()函数会找到匹配到的第一个进行数据库信息的组装</li>
</ol>

<h3>有前缀的数据库</h3>
有些情况下为了跟其它项目分开, 数据库是有前缀的, 处理这种情况可以用以下的配置写法
<pre>
//此时的配置数组是二维的
public $table = array(
	'DB_PRE1' => array(
		'virtual_table_name' => 'database_name, table_name'
	),
	'DB_PRE2' => array(
		'virtual_table_name' => 'database_name, table_name'
	)
);

public function getInfo($modelName)
{
	//找到第一个匹配的就返回
}
</pre>