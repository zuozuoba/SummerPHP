<h2>查询 Select</h2>

<h3>获取多条记录</h3>
<pre>
Test::link('user')
	->setWhereLT('age', 20)
	->get();
</pre>
<p>get()函数还有一个可选参数, 比如说get('id'), 那么返回的数组就会以id字段的值作为索引;</p>

<h3>获取一条记录</h3>
<pre>
Test::link('user')
	->setWhereLT('age', 20)
	->getOne();
</pre>

<h3>获取一条记录的某个字段</h3>
<pre>
Test::link('user')
	->setWhere(['id' => 20])
	->getOneField('id');
</pre>

<h3>获取多条记录的某个字段</h3>
<pre>
Test::link('user')
	->setWhere(['age' => 20])
	->getFields('sex', 'id');
</pre>
<p>此时返回的数组是这样的: 以id字段的值为键, 以sex字段的值为值; <br>
	如果只想返回所有结果中的sex的值, 那么第二个参数不传就可以了;<br>
	参考PHP的array_column()
</p>


<h3>指定返回的字段 </h3>
<pre>
User::link('user')
	->setFields('id, age, birth')
	->getOne();
</pre>
<p>注: 只返回id, age, birth字段</p>

<h3>排序</h3>
<pre>
User::link('user')
	->setFields('id, age, birth')
	->setOrder('age desc')
	->get();
</pre>

<h3>Limit</h3>
<pre>
User::link('user')
	->setFields('id, age, birth')
	->setWhereGT('id', 20)
    ->setLimit('0, 20')
	->get();
</pre>
<p>建议limit 和 where 配合使用优化SQL查询, 比如: "select id,name from user where id > 100 limit 0,20"</p>
<p>这样比"select id,name from user limit 100,20" 要更快, 后者需要读出120条然后返回最后20条</p>