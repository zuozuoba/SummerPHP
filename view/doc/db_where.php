<h2>组装 Where 条件</h2>

<h3>普通Where条件</h3>
<pre>
Test::link('user')
	->setWhere(['username' => $username, 'password' => $password])
	->getOne();
</pre>
<p>注: 登录时获取一条用户信息</p>

<h3>Between And</h3>
<pre>
User::link('user')
	->setWhere(['sex' => '男'])
	->setWhereBetween('age', 1, 20)
	->get();
</pre>
<p>注: 获取年龄在1~20岁的男生</p>

<h3>Where In</h3>
<pre>
User::link('user')
	->setWhere(['sex' => '男'])
	->setWhereIn('age', [1,2,3])
	->get();
</pre>
<p>注: 获取年龄为1, 2, 3岁的男生</p>

<h3>Where A &gt; B </h3>
<pre>
User::link('user')
	->setWhereGT('age', 20)
	->get();
</pre>
<p>注: 获取年龄大于20岁的用户</p>

<h3>Where A &lt; B </h3>
<pre>
User::link('user')
	->setWhereLT('age', 20)
	->get();
</pre>
<p>注: 获取年龄小于20岁的用户</p>