<?php
class Test extends Model
{
    public static function queue($a)
    {
        FileLog::ini('queue')->info(json_encode($a));
    }
}