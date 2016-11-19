<?php
/**
* 迅搜SDK
*/
require_once('/path/to/xunsearch/sdk/php/lib/XS.php');
class XunSearch 
{
    public function __construct()
    {
        // $xs = new XS('name');

        // //获取索引对象
        // $index = $xs->index;
        // $index->clean();//清空项目索引内容

        // //获取搜索对象
        // $search = $xs->search;
        // $docs = $search->search('张');
        // var_dump($doc);
    }

    public function demo()
    {
    	$xs = new XS('demo');
    	$search = $xs->search;

    	$query = '项目测试'; // 这里的搜索语句很简单，就一个短语
 
		$search->setQuery($query); // 设置搜索语句
		$search->addWeight('subject', 'xunsearch'); // 增加附加条件：提升标题中包含 'xunsearch' 的记录的权重
		$search->setLimit(5); // 设置返回结果最多为 5 条，并跳过前 10 条
		 
		$docs = $search->search(); // 执行搜索，将搜索结果文档保存在 $docs 数组中
		$count = $search->count(); // 获取搜索结果的匹配总数估算值

		var_dump($docs, $count );
    }

    public function & name()
    {
        $xs = new XS('name');
        $search = $xs->search;

        $query = '张'; // 这里的搜索语句很简单，就一个短语
 
        $search->setQuery($query); // 设置搜索语句
        $search->setLimit(2); // 设置返回结果最多为 5 条，并跳过前 10 条

        $class_method = 'array(21) {
            [0]=>
            string(11) "__construct"
            [1]=>
            string(5) "__get"
            [2]=>
            string(5) "__set"
            [3]=>
            string(6) "__call"
            [4]=>
            string(10) "getCharset"
            [5]=>
            string(10) "setCharset"
            [6]=>
            string(9) "getFields"
            [7]=>
            string(9) "setFields"
            [8]=>
            string(8) "setField"
            [9]=>
            string(1) "f"
            [10]=>
            string(11) "getAddTerms"
            [11]=>
            string(11) "getAddIndex"
            [12]=>
            string(7) "addTerm"
            [13]=>
            string(8) "addIndex"
            [14]=>
            string(11) "getIterator"
            [15]=>
            string(12) "offsetExists"
            [16]=>
            string(9) "offsetGet"
            [17]=>
            string(9) "offsetSet"
            [18]=>
            string(11) "offsetUnset"
            [19]=>
            string(12) "beforeSubmit"
            [20]=>
            string(11) "afterSubmit"
          }';
         
        // $docs = $search->search(); // 执行搜索，将搜索结果文档保存在 $docs 数组中
        // $count = $search->count(); // 获取搜索结果的匹配总数估算值

        return $search ;
    }

    public function & xiyao()
    {
        $xs = new XS('xiyao');
        $search = $xs->search;

        // $query = '张'; // 这里的搜索语句很简单，就一个短语
 
        // $search->setQuery($query); // 设置搜索语句
        // $search->setLimit(2); // 设置返回结果最多为 5 条，并跳过前 10 条

       
         
        // $docs = $search->search(); // 执行搜索，将搜索结果文档保存在 $docs 数组中
        // $count = $search->count(); // 获取搜索结果的匹配总数估算值

        return $search ;
    }

    public function & zhongyao()
    {
        $xs = new XS('zhongyao');
        $search = $xs->search;

        // $query = '张'; // 这里的搜索语句很简单，就一个短语
 
        // $search->setQuery($query); // 设置搜索语句
        // $search->setLimit(2); // 设置返回结果最多为 5 条，并跳过前 10 条

       
         
        // $docs = $search->search(); // 执行搜索，将搜索结果文档保存在 $docs 数组中
        // $count = $search->count(); // 获取搜索结果的匹配总数估算值

        return $search ;
    }

    public function & pinyin()
    {
        $xs = new XS('pinyin');
        $search = $xs->search;
        return $search ;
    }
}