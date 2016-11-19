<h2>安全类 Safe 的使用</h2>

<h3>对参数合法性的检测</h3>
<pre>
/*控制器中使用的方法*/
$this->getData('age', Safe::Check_INT|Safe::Check_DEFAULT);
</pre>
<p>其中的 "self::$Check_***" 是在Safe类中定义的静态变量, 其值是二进制字符串:</p>
<pre>
/*Safe类中定义的检测项目*/
public static $Check_Path       = 8; //防止相对路径包含
public static $Check_JavaScript = 16; //防止有'script'
public static $Check_SQLCHAR    = 32; //防止有'sql'
public static $Check_POST       = 64; //限制post方式提交
public static $Check_INT       	= 128; //限制数字

//self::$Check_DEFAULT = self::$Check_Path | self::$Check_JavaScript | self::$Check_SQLCHAR;
public static $Check_DEFAULT = 56;//默认检查项
</pre>
<ol>
	使用注意:
	<li>如果不传的话, 默认只检测$Check_DEFAULT中的项目</li>
	<li>如果要在$Check_DEFAULT中增加检测项目, 只用加上对应值就可以了</li>
	<li>如果要同时检测好几项的话, 用逻辑或把他们组装起来就可以了</li>
</ol>
<hr>

<h3>对行为合法性的检测</h3>
<h4>检测Token, 防止表单重复提交; 防止接口被刷</h4>
<pre>
/*1. 模版中隐藏表单*/
&lt;input type="hidden" name="safe_token" value="&lt;?= $safe_token ?&gt;"&gt;

/*2. 控制器中生成唯一Token*/
$this->view->safe_token = Safe::Create_Token();

/*3. 表单提交到控制器后进行Token检测*/
Safe::Check_Token_Once();
</pre>
<p>如果表单提交分步骤, 第一步,第二步....这个Token需要重复利用, 那么可以使用Safe::Check_Token($step)方法</p>

<h4>对referer合法性的检测, 常用于ajax接口请求验证, 盗链等</h4>
<pre>
/*1. 在配置文件中配置白名单*/
public static $refer_allow = array(
	'zhangzhibin.com',
);

/*2. 在控制器中使用*/
Safe::Check_Refer();
</pre>

<h3>自定义检测项目</h3>
<ol>
	<li>要在Safe类里边定义一个静态变量, 而且这个变量是以"Check_"开头的, 其值也是二进制串, 把相应的二进制位设为1, 注意不要跟其它变量的值冲突</li>
	<li>然后定义一个跟此变量名完全一样的函数, 进行"绑定"</li>
</ol>



