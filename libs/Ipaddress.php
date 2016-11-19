<?php
/**
* 利用百度开放api获取ip归属地
*/
class Ipaddress
{
    public $ip = '';
    public $address_detail = '';
    public $address = '';
    public $province = '';
    public $city = '';
    public $city_code = '';
    public $district = '';
    public $street = '';
    public $street_number = '';
    public $x = '';
    public $y = '';
    public $status = '';

    public $fun = false;

    public function __construct()
    {
        $this->fun = Fun::getInstance();
        
        $this->ip = $this->fun->getClientIp();
        $url = "http://api.map.baidu.com/location/ip?ak=bbCXktcD6Qrbxcpyp1DLkR8b&ip={$ip}&coor=bd09ll";
        $output = $this->fun->curl_get($url);
        $location = json_decode($output, true);

        $this->address_detail = $location['address'];
        $this->status        = $location['status'];
        $this->address       = $location['content']['address'];
        $this->porvince      = $location['content']['address_detail']['porvince'];
        $this->city          = $location['content']['address_detail']['city'];
        $this->city_code     = $location['content']['address_detail']['city_code'];
        $this->district      = $location['content']['address_detail']['district'];
        $this->street        = $location['content']['address_detail']['street'];
        $this->street_number = $location['content']['address_detail']['street_number'];
        $this->x             = $location['content']['point']['x'];
        $this->y             = $location['content']['point']['y'];

    }

    public function getAddress()
    {
        if (!$this->ip) {
            return array();
        }
        $url = "http://api.map.baidu.com/location/ip?ak=bbCXktcD6Qrbxcpyp1DLkR8b&ip={$this->ip}&coor=bd09ll";
        
        $output = $this->curl_get($url);
        return json_decode($output, true);
    }
}