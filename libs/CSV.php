<?php

class CSV
{
    public static $csvError = '';
    
    public static function _SetError($error)
    {
        self::$csvError = $error;
    }
    
    /**
     * 读取csv文件成数组
     * @param string $filePath 文件路径
     * @return array|bool
     */
    public static function import($filePath)
    {
        setlocale(LC_ALL, 'zh_CN');
        
        if(!file_exists($filePath) || !is_readable($filePath)) {
            self::_SetError('文件不存在或者不可读');
            return FALSE;
        }
        
        $rows = array();
        $fp = fopen($filePath, 'rb');
        while (!feof($fp)) {
            
            $row = str_replace(array("\r\n", "\r", "\n"), '', fgets($fp));
            $rows[] = explode(',', iconv('GB2312', 'UTF-8', $row)); //简体中文编码转为 utf-8, gbk 兼容gb2312
            
        }
        return $rows;
    }
    
    /**
     * 输出 UTF-8 编码的csv文件
     * @param array $data  ['filename' => 'xxx', 'list' => [[xx,xx,x], [xx,xx,x], ....]]
     * @return bool
     */
    public static function export($data)
    {
        if (empty($data['filename']) || empty($data['list'])) {
            self::_SetError('缺少参数filename/list');
            return FALSE;
        }
        $filename = $data['filename']; //文件名
        
        header("Expires: 0");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        // 强制下载
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
        
        //设置utf-8 + bom ，处理汉字显示的乱码
        echo(chr(0xEF).chr(0xBB).chr(0xBF));
        
        //打开输出缓存
        ob_start();
        
        //打开输出流
        $df = fopen("php://output", 'w');
        
        //数据写入缓存
        foreach ($data['list'] as $row) {
            foreach ($row as $k => $v) {
                is_numeric($v) && ($row[$k] .= "\t"); //防止变为科学计数法显示
            }
            fputcsv($df, $row);
        }
        
        fclose($df);
        echo ob_get_clean();
        exit;
    }
}