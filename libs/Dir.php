<?php
//后根序遍历目录, 读取所有子目录和文件
class Dir
{
	public $dir = '';
	public $fileList = [];

	public function __construct($dir)
	{
		$stack = [];
		$this->dir = $dir = realpath($dir);
		$currentDir = $dir.DIRECTORY_SEPARATOR;
		$current = array_slice(scandir($dir), 2); //去掉 . 和 ..

		if (!empty($current)) {
			do {
				$isOver = 1;
				foreach ($current as $k => $fileName) {
					if (substr($fileName, 0, 1) != '.') {
						$pathName = $currentDir.$fileName;
						if (is_file($pathName)) {
							$this->fileList[] = $pathName;
						} elseif (is_dir($pathName) && count(scandir($pathName)) > 2) {
							//当前是目录, 就把其以后的数据压栈
							$tmp = array_slice($current, $k+1);
							$tmp['currentDir'] = $currentDir;
							array_push($stack, $tmp);

							$current = array_slice(scandir($pathName), 2);
							$currentDir = $pathName.DIRECTORY_SEPARATOR;

							$isOver = 0;
							break;
						}
					}
				}
				if ($isOver) { //没有入栈, 说明当前目录里全是文件
					$current = array_pop($stack);
					$currentDir = $current['currentDir'];
					unset($current['currentDir']);
				}

			} while(!empty($stack) || count($current));
		}
	}

	//读取所有子目录和文件, $dir目录最后不要有'/'
	public static function ini($dir)
	{
		return new self($dir);
	}

	//替换路径的前缀
	public function replacePrefix($target, $replace='')
	{
		$fileList = [];
		foreach ($this->fileList as $v) {
			$fileList[] = str_replace($target, $replace, $v);
		}
		return $fileList;

	}
	
	//过滤指定的后缀
	public function extension($ext)
	{
		foreach ($this->fileList as $k => $v) {
			$extension = pathinfo($v, PATHINFO_EXTENSION);
			if (strtolower($extension) != $ext) {
				unset($this->fileList[$k]);
			}
		}
		return $this;
	}
	
	//重新整理文件列表, 给每一个目录和文件标上唯一的整数id
	public function sample()
	{
		$list[] = ['currentId' => 1, 'preId' => -1, 'name' => 'Root'];

		$preId = 1; //父节点索引
		$i = 2; //当前索引
		$nodeIndex = []; //已生成索引的节点

		foreach ($fileList as $k => $srcPathName) {
			$path = str_replace($this->dir, '', $srcPathName);
			$arrNode = explode(DIRECTORY_SEPARATOR, ltrim($path, DIRECTORY_SEPARATOR));
			
			if (count($arrNode) == 1) {
				$list[] = ['currentId' => $i++, 'preId' => 1, 'name' => reset($arrNode), 'isEnd' => 1];
			} else {
				//第一个节点(第一层目录), 父节点固定为1
				$firstNode = array_shift($arrNode);
				if (empty($nodeIndex[$firstNode])) {
					$nodeIndex[$firstNode] = $i++;
					$list[] = ['currentId' => $nodeIndex[$firstNode], 'preId' => 1, 'name' => $firstNode];
				}
				$preId = $nodeIndex[$firstNode];
				
				//最后一个节点, url值不为空
				$endNode = array_pop($arrNode);
				
				//中间节点
				if (!empty($arrNode)) {
					foreach ($arrNode as $node) {
						if (empty($nodeIndex[$node])) {
							$nodeIndex[$node] = $i++;
							$list[] = ['currentId' => $nodeIndex[$node], 'preId' => $preId, 'name' => $node, 'isEnd' => 0];
						}
						$preId = $nodeIndex[$node];
					}
				}
				
				//最后一个节点(文件名)
				$list[] = ['currentId' => $i++, 'preId' => $preId, 'name' => $endNode, 'isEnd' => 1];
			}
			$preId = 1;
		}
	}

}