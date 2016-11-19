<h2>insert</h2>

<h3>插入一条记录</h3>

<pre>
    $user = array(
        'name' => '小明',
        'age' => '20',
    );

    Test::link('user')->insert($user);
</pre>
<p>返回插入的id</p>

<h3>插入多条记录</h3>

<pre>
    Test::link('user')->insertm('id,age',[[1,1],[2,2]]);
</pre>
<p>返回最后插入的id</p>

<h3>replace into</h3>
<pre>
    $user = array(
        'name' => '小明',
        'age' => '20',
    );

    Test::link('user')->replace($user);
</pre>
<p>返回影响的行数, 因为当更新了表中一条记录时, 返回最后插入的id会是0或上一次insert into 语句插入的id</p>