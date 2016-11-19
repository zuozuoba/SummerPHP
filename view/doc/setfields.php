<h2>指定返回的字段</h2>

<h3> setFields </h3>
<pre>
User::link('user')
	->setFields('id, age, birth')
	->setWhere(['sex' => '男')
	->getOne();
</pre>
<p>注: 获取一条男生的记录, 只返回id, age, birth字段</p>