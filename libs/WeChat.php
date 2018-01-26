<?php
class WeChat
{
	public $AppID = '';
	public $AppSecret = '';
	public $EncodingAESKey = '';
	public $callback = '';
	public $token = '';

	public $msg ='';
	
	public function __construct()
	{
		$config = Config::$we_chat;
		$this->AppID 			= $config['AppID'];
		$this->AppSecret 		= $config['AppSecret'];
		$this->EncodingAESKey 	= $config['EncodingAESKey'];
		$this->callback 		= $config['callback'];
		$this->token 			= $config['token'];
	}

	public function initc()
	{
		
	}

	public function checkSignature($signature, $timestamp, $nonce, $echostr)
	{
		$tmpArr = array($this->token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return $echostr;
		}else{
			return false;
		}
	}
	
	public function index()
	{
		$echostr = $this->checkSignature();
		return $echostr;
	}

	public function voice()
	{
		// 每次发送消息都会post 来一份签名相关的数据
		// $echostr = $this->checkSignature();
		// exit($echostr);

		preg_match('#<FromUserName><!\[CDATA\[([a-zA-Z0-9_]+)\]#', $GLOBALS['HTTP_RAW_POST_DATA'], $matches1);
		preg_match('#<Recognition><!\[CDATA\[([^\]]*)\]#', $GLOBALS['HTTP_RAW_POST_DATA'], $matches2);
		$openid = !empty($matches1[1]) ? $matches1[1] : '0';
		$text = !empty($matches2[1]) ? $matches2[1] : '没听清...';

		$objRedis = IRedis::getInstance();
		$objRedis->publish($openid, $text);

	}

	public function voice1()
	{
		// 每次发送消息都会post 来一份签名相关的数据
		// $echostr = $this->checkSignature();
		// exit($echostr);

		preg_match('#<FromUserName><!\[CDATA\[([a-zA-Z0-9_]+)\]#', $GLOBALS['HTTP_RAW_POST_DATA'], $matches1);
		preg_match('#<Recognition><!\[CDATA\[([^\]]*)\]#', $GLOBALS['HTTP_RAW_POST_DATA'], $matches2);
		$openid = !empty($matches1[1]) ? $matches1[1] : '0';
		$text = !empty($matches2[1]) ? $matches2[1] : '没听清...';

		$objRedis = IRedis::getInstance();
		$objRedis->putWeixinVoice($openid, $text);
	}

	public function text()
	{
		preg_match('#<FromUserName><!\[CDATA\[([a-zA-Z0-9_]+)\]#', $GLOBALS['HTTP_RAW_POST_DATA'], $strUserName);
		preg_match('#<Content><!\[CDATA\[([^\]]*)\]#', $GLOBALS['HTTP_RAW_POST_DATA'], $strContent);
		$openid = !empty($strUserName[1]) ? $strUserName[1] : '0';
		$content = !empty($strContent[1]) ? $strContent[1] : '0';

		if (strpos($content, '主播_') !== false) {
			$this->loadBusiness('Test')->setWeiXinUser(str_replace('主播_', '', $content), $openid, 1);
		}

		$fp = fopen('/usr/local/data/msg/weixin/voice.php', 'a+');
		fwrite($fp, $content.'=>'.$openid.PHP_EOL);
		fclose($fp);

		$detailUrl = $this->controllerUrl.'live?id='.$openid;

		header("Content-type:text/xml;charset=utf-8");
		echo <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<xml>
<ToUserName><![CDATA[{$openid}]]></ToUserName>
<FromUserName><![CDATA[gh_33b05f876ff8]]></FromUserName>
<CreateTime>{$this->intVisitTime}</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[请访问下边链接, 查看识别结果: {$detailUrl}]]></Content>
</xml>
EOD;
	}

	public function getTextOfVoice()
	{
		$username = $this->getData('username');
		exit(IRedis::getInstance()->getWeixinVoice($username));
	}

	public function r()
	{
		//$xml = '<xml><ToUserName><![CDATA[gh_33b05f876ff8]]></ToUserName><FromUserName><![CDATA[oOmNJuBe40prore6vY_WVAb5QFwg]]></FromUserName><CreateTime>1430654166</CreateTime><MsgType><![CDATA[voice]]></MsgType><MediaId><![CDATA[bncV92rlYTdAGG-5QFMaOk8DeZJTQ_l_U7Y0IlOV4yUyTyPUipZT1SZyfnqjKugq]]></MediaId><Format><![CDATA[amr]]></Format><MsgId>6144612854856155136</MsgId><Recognition><![CDATA[哈哈哈哈哈哈]]></Recognition></xml>';
		preg_match('#<MsgType><!\[CDATA\[([a-z]+)\]#', $GLOBALS['HTTP_RAW_POST_DATA'], $strMsgType);

		$fp = fopen('/usr/local/data/msg/weixin/voice.php', 'a+');
		fwrite($fp, json_encode($strMsgType).PHP_EOL);
		fclose($fp);

		switch ($strMsgType[1]) {
			case 'text':
				$this->text();
				break;
			case 'voice':
				$this->voice();
				break;
		}

		exit(''); //返回空串, 告诉微信已经收到了消息
	}
}
