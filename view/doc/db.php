<h2>DBmysql类的基本使用</h2>

<h3>样例</h3>
<pre>
Test::link('user')
	->setWhere(['age' => 20])
	->getOne();
</pre>
<p>
	其中的"Test"是model的名字, 框架会自动找到model类的文件 <br>
	其中的"user"是在配置文件中定义的, 用于找到对应数据库名和表名<br>
	这里使用链式写法, 减少了代码量<br>
</p>

<h3>Model</h3>
<pre>
/*model文件的位置, 以Test为例: */
/model/Test.php


/*Test model的定义*/
class Test extends Model
{

}
</pre>
<p>
	所有的model类都要继承Model这个核心类, 而Model核心类继承了DBmysql类; <br>
	当然也可以不这样做, 而是在Model类中初始化一个DBmysql的对象, 作为其成员变量 <br>
	这样做是为了充分利用IDE自动补全功能, 节省开发者的时间<br>
	我用过一年多的zend studio 也用过两年左右的sublime 2和3, 最后还是转向PHPstorm了, 他的goToLastEdit功能在调试时很方便
</p>

<h3>在控制器中直接查询数据库</h3>

<h4>使用方法</h4>
<pre>
Test::link('user')
	->setWhere(['age' => 20])
	->getOne();
</pre>


<h4>查看当前查询的完整sql语句</h4>
<pre>Test::$sql</pre>

<h3>model中查询数据库</h3>
<pre>
/*model 中的方法*/
public static function testdb()
{
	$rs = self::link('user')
		->setWhere(['age' => 20])
		->getFields('age', 'id');
	var_dump($rs);
}

/*控制器中调用model的方法*/
public function test()
{
	Test::testdb();
}
</pre>