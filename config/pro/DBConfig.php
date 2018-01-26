<?php
class DBConfig
{
    //mysql link param
    public static $write = array(
        array(
            'host' => '127.0.0.1',
            'username' => 'root',
            'password' => '',
        )

    );

    public static $read = array(
        array(
            'host' => '',
            'username' => '',
            'password' => '',
        )
    );

    //table info
    //虚拟表名 => 数据库名, 表名
    //最好将所有model在此备案, 方便管理
    public static $TableInfo = array(
        'user'          => 'test, user',
        'note'          => 'test, tiezi',
        'pinyin'          => 'test, pinyin',
        'test_(\d+)_(\d+)' => 'test, test_$1_$2'
    );

    public static function getDBInfo($modelName)
    {
        $strDT = '';
        if (array_key_exists($modelName, self::$TableInfo)) {
            $strDT = self::$TableInfo[$modelName];//获得database table 字符串
        } else {
            foreach (self::$TableInfo as $pattern => $dt) {
                if (strpos($pattern, '(') !== FALSE) {
                    preg_match('#'.$pattern.'#', $modelName, $matches);

                    if (!empty($matches)) {
                        $strDT = $dt;
                        foreach ($matches as $key => $value) {
                            $strDT = str_replace('$'.$key, $value, $strDT);
                        }
                        break;
                    }
                }
            }
        }

        if (!empty($strDT)) {
            $strDT = preg_replace('#\s+#', '', $strDT);//去掉空白
            return explode(',', $strDT);
        } else {
            self::error('no table info <'.$modelName.'> found !');
        }
    }

    public static function error($msg)
    {
        exit($msg);
    }

    public $table = array(
        'DB_PRE1' => array(
            'virtual_table_name' => 'database_name, table_name'
        ),
        'DB_PRE2' => array(
            'virtual_table_name' => 'database_name, table_name'
        )
    );

    public function getInfo($model_name)
    {
        $info = array();
        foreach ($this->table as $db_pre => $db_info) {
            if (array_key_exists($model_name, $db_info)) {
                $strDT = $db_info[$model_name];//获得database table 字符串
                $strDT = preg_replace('#\s+#', '', $strDT);//去掉空白
                $info = explode(',', $strDT);
                $info[0] = $db_pre . $info[0];
                exit;
            }
        }

        return $info;
    }
}