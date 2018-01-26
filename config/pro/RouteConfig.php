<?php

class RouteConfig
{
    public static $Domain = array(
        'doc' => 'doc',
        'documnet' => 'doc/index/index',
    );
    
    public static $Path = array(
        'article_list_(\d+)' => 'index/index/route/page/$1'
    );
}