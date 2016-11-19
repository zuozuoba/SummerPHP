<?php
/**
* 大众点评sdk
*/
class dianping 
{
    private $appkey = '';
    private $secret = '';
    private $format = '';

    private $api = 'http://api.dianping.com/v1/';

    public function __construct()
    {
        $config = Config::$dian_ping;
		$this->appkey = $config['appkey'];
		$this->secret = $config['secret'];
		$this->format = $config['format'];
    }

    public function get_sign($params)
    {
        ksort($params);

        //连接待加密的字符串
        $codes = $this->appkey;

        foreach($params as $key=>$val) {
          $codes .= ($key.$val);
        }

        $codes .= $this->secret;
        $sign = strtoupper(sha1($codes));

        return $sign;
    }
    
    // 搜索团购 Deal/find_deals
    // http://developer.dianping.com/app/api/v1/deal/find_deals
    public function find_deals($params)
    {
        $api = $this->api.'deal/find_deals';
        $sign = $this->get_sign($params);

        $params['appkey'] = $this->appkey;
        $params['sign'] = $sign;
        
        $queryString = http_build_query($params);
        
        $url = $api.'?'.$queryString;
        
        $json = Fun::getInstance()->curl_get($url);

        return json_decode($json, true);
    }

    //获取支持商户搜索的最新城市列表
    public function get_cities_with_businesses()
    {
        $api = $this->api.'metadata/get_cities_with_businesses';
        $a = array();
        $a['sign'] = $this->get_sign($a);
        $a['appkey'] = $this->appkey;

        $queryString = http_build_query($a);
        $url = $api.'?'.$queryString;

        $json = Fun::getInstance()->curl_get($url);
        $arr = json_decode($json, true);
        return $arr;
    }

    //获取支持团购搜索的最新分类列表
    public function get_categories_with_deals()
    {
        $api = $this->api.'metadata/get_categories_with_deals';
        $a = array();
        $a['sign'] = $this->get_sign($a);
        $a['appkey'] = $this->appkey;

        $queryString = http_build_query($a);
        $url = $api.'?'.$queryString;

        $json = Fun::getInstance()->curl_get($url);
        return json_decode($json, true);
    }

    //获取支持商户搜索的最新分类列表
    public function get_categories_with_businesses()
    {
        $api = $this->api.'metadata/get_categories_with_deals';
        $a = array();
        $a['sign'] = $this->get_sign($a);
        $a['appkey'] = $this->appkey;

        $queryString = http_build_query($a);
        $url = $api.'?'.$queryString;

        $json = Fun::getInstance()->curl_get($url);
        return json_decode($json, true);
    }

}