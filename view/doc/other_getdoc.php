<h2>获取函数的注释信息</h2>

<p>使用场景: 执行某些函数的时候要做一些额外的操作, 但又不想跟这批函数耦合</p>

<h3>使用举例(权限验证)</h3>
<pre>
/**
 * need_audit:1
 */
public function foo()
{}


public function initc() //成员方法执行的必经函数, 也可以写到其它必经函数中去
{
	$doc = Fun::getMethodDoc($this->module, $this->controll, $this->action); //获取函数的注释信息
	$is_audit = preg_match('/need_audit:(\d)?/', $doc, $match); //匹配出想要的字符串
	if ($match && $match['1'] == '1')
	{
	    //do something
	}
}
</pre>
<ol>
	解释:
	<li>在方法foo()的注释中, 有一断字符串"need_audit:1"</li>
	<li>如果匹配到这个字符串, 那么就会做相应的权限验证操作</li>
	<li>检验"是否匹配"这个动作放在了initc()这个函数里, 因为所有的action执行前都必先执行这个函数</li>
	<li>注释信息必须用/**/这种的区块注释, 不能用行注释</li>
	<li>使用此功能的时如果开启了PHP的opcode cache(opcache等), 注意相关配置, 要把注释缓存打开</li>
</ol>
