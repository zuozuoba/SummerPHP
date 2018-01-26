<?php
/**
* 七牛SDK
*/
class QiNiu 
{
	public static $Instance = [];
    private $accessKey = '';
    private $secretKey = '';
    private $bucket = '';
    private $upToken = '';
    private $isOk = FALSE;
    private $errMsg = '';

    public function __construct($bucket='')
    {
		$config = Config::$qi_niu;
		$this->accessKey = $config['accessKey'];
		$this->secretKey = $config['secretKey'];
		$this->bucket = $bucket ? $bucket : $config['bucket'];
		$this->upToken = $config['upToken'];
    }

	/**
	 * desc 单例
	 * @return object
	 */
	public static function getInstance()
	{
		if (empty(self::$Instance)) {
			self::$Instance = new QiNiu();
		}
		return self::$Instance;
    }

    public function getFileInfo($filename)
    {
        require_once(LIBPATH.'Qiniu/rs.php');

        Qiniu_SetKeys($this->accessKey, $this->secretKey);
        $client = new Qiniu_MacHttpClient(null);

        $info = Qiniu_RS_Stat($client, $this->bucket, $filename);

        return array(
            'info' => $info[0],
            'error' => $info[1]
            );
    }

    public function getUpToken()
    {
        require_once(LIBPATH.'Qiniu/rs.php');

        Qiniu_SetKeys($this->accessKey, $this->secretKey);
        $putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
        $this->upToken = $putPolicy->Token(null);
    }

	/**
	 * desc 上传单个文件
	 * @param string $filename 文件名,七牛的key
	 * @param string $filepath 待上传的文件所在的路径
	 * @return array
	 */
    public function upFile($filename, $filepath)
    {
        require_once(LIBPATH.'Qiniu/io.php');
        require_once(LIBPATH.'Qiniu/rs.php');

        $this->getUpToken();

        Qiniu_SetKeys($this->accessKey, $this->secretKey);

        $putExtra = new Qiniu_PutExtra();
        $putExtra->Crc32 = 1;
        $info = Qiniu_PutFile($this->upToken, $filename, $filepath, $putExtra);

        if ($info[1] === null) {
        	$this->isOk = TRUE;
		} else {
        	$this->errMsg = json_encode($info[1]);
		}

        return array(
            'info' => $info[0],
            'error' => $info[1]
            );
    }

    //获取图片的URL, 并未对图片做剪裁等处理
    public function getImageBaseUrl($filename)
    {
        require_once(LIBPATH.'Qiniu/rs.php');
        require_once(LIBPATH.'Qiniu/fop.php');
        
        $domain = $this->bucket.'.qiniudn.com';

        Qiniu_SetKeys($this->accessKey, $this->secretKey);
         
        //生成baseUrl
        return  Qiniu_RS_MakeBaseUrl($domain, $filename);
    }

	//生成对图片进行剪裁等处理的字符串, 用户拼接在图片URL后边
    public function getImageView($mode = 0, $width = '', $height = '', $format = '', $interlace='' )
    {
        require_once(LIBPATH.'Qiniu/rs.php');
        require_once(LIBPATH.'Qiniu/fop.php');

        Qiniu_SetKeys($this->accessKey, $this->secretKey);

        //生成fopUrl
        $imgView = new Qiniu_ImageView;
        $imgView->Mode = $mode;
        $imgView->Width = $width;
        $imgView->Height = $height;
        $imgView->Format = $format;
        $imgView->Interlace = $interlace;
        $imgViewUrl = $imgView->MakeRequest();

        //对fopUrl 进行签名，生成privateUrl。 公有bucket 此步可以省去。
        // $getPolicy = new Qiniu_RS_GetPolicy();
        // return $getPolicy->MakeRequest($imgViewUrl, null);
        return $imgViewUrl;
    }

    //水印
    public function getTxtWaterMark($text, $font = '', $fontsize = '10', $fill = '', $dissolve = '', $gravity = '', $dx = '', $dy = '')
    {
        require_once(LIBPATH.'Qiniu/rs.php');
        require_once(LIBPATH.'Qiniu/fop.php');

        // Qiniu_SetKeys($this->accessKey, $this->secretKey);

        //生成fopUrl
        $imgView = new Qiniu_TXT_WaterMark;
        $imgView->Text = $text;
        $imgView->Font = $font;
        $imgView->FontSize = $fontsize;
        $imgView->Fill = $fill;
        $imgView->Dissolve = $dissolve;
        $imgView->Gravity = $gravity;
        $imgView->Dx = $dx;
        $imgView->Dy = $dy;
        $imgViewUrl = $imgView->MakeRequest();

        return $imgViewUrl;
    }

	/**
	 * desc form表单,上传单个文件到七牛
	 * @param string $formname
	 * @param string $pre 前缀
	 * @return array
	 */
    public function upImage($formname, $pre='qn')
    {
        if (empty($_FILES[$formname]['size'])) {
            return array();
        }

        //取得后缀
        $realname = $_FILES[$formname]['name'];
        $arrFileInfo = pathinfo($realname);
        $file_name = $arrFileInfo['filename'];
        $extension = $arrFileInfo['extension'];
        // $arrFileInfo['dirname'];
        // $arrFileInfo['basename'];

        //组织完整入库文件名
        $rand_time = date('YmdHis').'_'.mt_rand(100, 999);
        $qiniu_name = $pre.'_'.$rand_time.'.'.$extension;
        $filepath = $_FILES[$formname]['tmp_name'];
        //上传
        $this->upFile($qiniu_name, $filepath);
        $imgbaseurl = $this->getImageBaseUrl($qiniu_name);
         
        return array(
            'qiniu_url' => $imgbaseurl,
            'qiniu_name' => $qiniu_name,
            'file_name' => $file_name,
            'size' => $_FILES[$formname]['size'],
            'extension' => $extension,
            );
    }

    //一次上传多张图片
    public function upMoreImage($formname, $pre='qn')
    {
        if (empty($_FILES[$formname])) {
            return '';
        }

        //对$_FILES重新组织
        $arrImages = array();
        foreach ($_FILES[$formname]['name'] as $k => $name) {
            if ($_FILES[$formname]['size'][$k]) {
                $arrImages[$k]['name']     = $name;
                $arrImages[$k]['type']     = $_FILES[$formname]['type'][$k];
                $arrImages[$k]['tmp_name'] = $_FILES[$formname]['tmp_name'][$k];
                $arrImages[$k]['error']    = $_FILES[$formname]['error'][$k];
                $arrImages[$k]['size']     = $_FILES[$formname]['size'][$k];
            }
        }

        //循环上传
        $arrUpInfo = array();
        foreach ($arrImages as $k => $v) {
            //取得后缀
            $realname = $v['name'];
            $arrRealName = explode('.', $realname);
            $file_name = $arrRealName[0];
            $stuffix = end($arrRealName);

            //组织完整入库文件名
			$rand_time = date('YmdHis').'_'.mt_rand(100, 999);
            $qiniu_name = $pre.'_'.$rand_time.'.'.$stuffix;
            $filepath = $v['tmp_name'];

            //上传
            $this->upFile($qiniu_name, $filepath);

            $arrUpInfo[$k] = array(
                'qiniu_name' => $qiniu_name,
                'file_name' => $file_name
                );
        }

        return $arrUpInfo;

    }

    //上传是否成功
	public function isOk()
	{
		return $this->isOk;
    }

	//上传是否成功
	public function errMsg()
	{
		return $this->errMsg;
	}
}