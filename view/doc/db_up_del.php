<h2>update/delete</h2>

<h3>更新</h3>
<pre>
    Test::link('user')
        ->setWhere(['id' => 1])
        ->update(['age'=>10]);

    Test::link('user')
        ->setWhere(['id' => 1])
        ->update(['age'=>'age+1']);
</pre>
<p>返回影响的行数, 如果没有where条件会报错的</p>

<h3>删除</h3>
<pre>
    Test::link('user')
        ->setWhere(['id' => 1])
        ->delete();
</pre>
<p>返回影响的行数, 如果没有where条件会报错的</p>
