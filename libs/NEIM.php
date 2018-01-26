<?php

//网易云IM 服务端对接
//服务端接口文档, http://dev.netease.im/docs/product/IM%E5%8D%B3%E6%97%B6%E9%80%9A%E8%AE%AF/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3
class NEIM
{
	public static $appkey = '';
	public static $secret = '';

	public static function init($appkey='', $secret='')
	{
		self::$appkey = $appkey;
		self::$secret = $secret;
	}

	public static function getCheckSum()
	{
		$Nonce = rand(10000, 99999);
		$CurTime = (string)time();
		$sha1 = sha1(self::$secret.$Nonce.$CurTime);
		return ['Nonce' => $Nonce, 'CurTime' => $CurTime, 'CheckSum' => strtolower($sha1)];
	}

	public static function request($url, $data)
	{
		$ch = curl_init($url);
		$sign = self::getCheckSum();

		$header = [
			'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
			'AppKey: '.self::$appkey,
			'Nonce: '.$sign['Nonce'],
			'CurTime: '.$sign['CurTime'],
			'CheckSum: '.$sign['CheckSum'],
		];
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //设置头信息的地方
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 检查证书中是否设置域名（为0也可以，就是连域名存在与否都不验证了）
		return curl_exec($ch);
	}

	//创建用户
	public static function createUser()
	{
		$url = 'https://api.netease.im/nimserver/user/create.action';
		$post = [
			'accid' => '123456',
			'name' => '张志斌',
			'props' => '{}',
			'icon' => '',
			'token' => ''

		];
		return self::request($url, $post);

		//{"code":200,"info":{"token":"a443c552790b55f14687214d95f2d1d6","accid":"123456","name":"张志斌"}}
	}

	//更新用户信息
	public static function updateUser()
	{
		$url = 'https://api.netease.im/nimserver/user/update.action';
		$post = [
			'accid' => '123456',
			'props' => '{}',
			'token' => ''
		];

		return self::request($url, $post);
	}

	//更新用户token
	public static function refreshToken()
	{
		$url = 'https://api.netease.im/nimserver/user/refreshToken.action';
		$post = [
			'accid' => '123456',
		];

		return self::request($url, $post);
		//{"code":200,"info":{"token":"8f2f4adb8a688ed3f7382050fd75582b","accid":"123456"}}
	}

	//封禁用户
	public static function block()
	{
		$url = 'https://api.netease.im/nimserver/user/block.action';
		$post = [
			'accid' => '123456',
			'needkick' => 'false' //string true/false
		];

		return self::request($url, $post);
	}

	//封禁用户
	public static function unblock()
	{
		$url = 'https://api.netease.im/nimserver/user/block.action';
		$post = [
			'accid' => '123456',
			'needkick' => 'false' //string true/false
		];

		return self::request($url, $post);
	}

	//更新用户名片(附加信息)
	public static function updateUInfo()
	{
		$url = 'https://api.netease.im/nimserver/user/updateUinfo.action';
		$post = [
			'accid' => '123456',
			'name' => '',
			'icon' => '',
			'needkick' => '',
			'sign' => '',
			'email' => '',
			'birth' => '',
			'mobile' => '',
			'gender' => '',
			'ex' => '',
		];

		return self::request($url, $post);
	}

	//获取用户名片
	public static function getUinfos()
	{
		$url = 'https://api.netease.im/nimserver/user/getUinfos.action';
		$post = [
			'accid' => '123456',
		];

		return self::request($url, $post);
	}

	//设置, 设置桌面端在线时，移动端是否需要推送
	public static function setDonnop()
	{
		$url = 'https://api.netease.im/nimserver/user/setDonnop.action';
		$post = [
			'accid' => '123456',
			'donnopOpen' => 'true',
		];

		return self::request($url, $post);
	}

	//添加好友
	public static function friendAdd()
	{
		$url = 'https://api.netease.im/nimserver/friend/add.action';
		$post = [
			'accid' => '123456',
			'faccid' => '654321',
			'type' => 1, //1直接加好友，2请求加好友，3同意加好友，4拒绝加好友
			'msg' => 'hello', //加好友对应的请求消息，第三方组装，最长256字符
		];

		return self::request($url, $post);
		//{"code":200,"info":{"token":"8f2f4adb8a688ed3f7382050fd75582b","accid":"123456"}}
	}

	//更新好友信息
	public static function friendUpdate()
	{
		$url = 'https://api.netease.im/nimserver/friend/update.action';
		$post = [
			'accid' => '123456',
			'faccid' => '654321',
			'alias' => '6666', //给好友增加备注名，限制长度128
			'ex' => 'hello', //修改ex字段，限制长度256
		];

		return self::request($url, $post);
	}

	//删除好友
	public static function friendDelete()
	{
		$url = 'https://api.netease.im/nimserver/friend/delete.action';
		$post = [
			'accid' => '123456',
			'faccid' => '654321',
		];

		return self::request($url, $post);
	}

	//删除好友
	public static function friendList()
	{
		$url = 'https://api.netease.im/nimserver/friend/get.action';
		$post = [
			'accid' => '123456',
			'createtime' => '', //查询的时间点, 时间戳
		];

		return self::request($url, $post);
	}

	//拉黑/静音好友
	public static function friendBlack()
	{
		$url = 'https://api.netease.im/nimserver/user/setSpecialRelation.action';
		$post = [
			'accid' => '123456',
			'targetAcc' => '', //被加黑或加静音的帐号
			'relationType' => '', //本次操作的关系类型,1:黑名单操作，2:静音列表操作
			'value' => '', //操作值，0:取消黑名单或静音，1:加入黑名单或静音
		];

		return self::request($url, $post);
	}

	//拉黑/静音好友列表
	public static function friendBlackList()
	{
		$url = 'https://api.netease.im/nimserver/user/listBlackAndMuteList.action';
		$post = [
			'accid' => '123456',
		];

		return self::request($url, $post);
	}

	//发送普通消息
	public static function sendMsg()
	{
		$url = 'https://api.netease.im/nimserver/msg/sendMsg.action';
		$post = [
			'from' => '123456',//发送者accid，用户帐号
			'ope' => '0', //0：点对点个人消息，1：群消息（高级群），其他返回414
			'to' => '654321', //ope==0是表示accid即用户id，ope==1表示tid即群id
			'type' => '0', //0 表示文本消息, 1 表示图片， 2 表示语音， 3 表示视频， 4 表示地理位置信息， 6 表示文件， 100 自定义消息类型
			'body' => '{"msg":"hello"}', //最大长度5000字符，为一个JSON串, {"msg":"hello"}
			'antispam' => 'true', //本消息是否需要过自定义反垃圾系统，true或false, 默认false
			'antispamCustom' => '',
			'option' => '',
			'pushcontent' => '',
			'payload' => '',
			'ext' => '',
			'forcepushlist' => '',
			'forcepushcontent' => '',
			'forcepushall' => '',
		];

		return self::request($url, $post);
	}

	//批量发送消息
	public static function sendBatchMsg()
	{
		$url = 'https://api.netease.im/nimserver/msg/sendBatchMsg.action';
		$post = [
			'fromAccid' => '123456',//发送者accid，用户帐号
			'toAccids' => '["aaa","bbb"]', //["aaa","bbb"]（JSONArray对应的accid，如果解析出错，会报414错误），限500人
			'type' => '0', //0 表示文本消息, 1 表示图片， 2 表示语音， 3 表示视频， 4 表示地理位置信息， 6 表示文件， 100 自定义消息类型
			'body' => '{"msg":"hello"}', //最大长度5000字符，为一个JSON串, {"msg":"hello"}
			'option' => '',
			'pushcontent' => '',
			'payload' => '',
			'ext' => '' //开发者扩展字段，长度限制1024字符
		];

		return self::request($url, $post);
	}

	//发送系统消息
	public static function sendAttachMsg()
	{
		$url = 'https://api.netease.im/nimserver/msg/sendAttachMsg.action';
		$post = [
			'from' => '123456',//发送者accid，用户帐号
			'msgtype' => 0, //0：点对点自定义通知，1：群消息自定义通知，其他返回414
			'to' => '', //msgtype==0是表示accid即用户id，msgtype==1表示tid即群id
			'type' => '0', //0 表示文本消息, 1 表示图片， 2 表示语音， 3 表示视频， 4 表示地理位置信息， 6 表示文件， 100 自定义消息类型
			'attach' => '{"msg":"hello"}', //自定义通知内容，第三方组装的字符串，建议是JSON串，最大长度4096字符
			'sound' => '',
			'pushcontent' => '',
			'payload' => '',
			'save' => '' //1表示只发在线，2表示会存离线，其他会报414错误。默认会存离线
		];

		return self::request($url, $post);
	}

	//批量发送系统消息
	public static function sendBatchAttachMsg()
	{
		$url = 'https://api.netease.im/nimserver/msg/sendBatchAttachMsg.action';
		$post = [
			'fromAccid' => '123456',//发送者accid，用户帐号
			'toAccids' => 0,
			'attach' => '',
			'pushcontent' => '0',
			'payload' => '{"msg":"hello"}',
			'sound' => '',
			'save' => '',
			'option' => '',
		];

		return self::request($url, $post);
	}

	//批量发送系统消息
	public static function recall()
	{
		$url = 'https://api.netease.im/nimserver/msg/recall.action';
		$post = [
			'deleteMsgid' => '123',//要撤回消息的msgid
			'timetag' => 0, //要撤回消息的创建时间
			'type' => '', //7:表示点对点消息撤回，8:表示群消息撤回，其它为参数错误
			'from' => '123456', //发消息的accid
			'to' => '', //如果点对点消息，为接收消息的accid,如果群消息，为对应群的tid
			'msg' => '', //可以带上对应的描述
			'ignoreTime' => '', //1表示忽略撤回时间检测，其它为非法参数，如果需要撤回时间检测，不填即可
		];

		return self::request($url, $post);
	}
}
$appkey = '';
$secret = '';
NEIM::init($appkey, $secret);
//NEIM::createUser();
//NEIM::updateUser();
//NEIM::refreshToken();
//NEIM::block();
