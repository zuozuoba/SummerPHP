<?php
/**
* 七牛SDK
*/
class QiNiu 
{
    private $accessKey = '';
    private $secretKey = '';
    private $bucket = '';
    private $upToken = '';

    public function __construct($bucket='')
    {
		$config = Config::$qi_niu;
		$this->accessKey = $config['accessKey'];
		$this->secretKey = $config['secretKey'];
		$this->bucket = $bucket ? $bucket : $config['bucket'];
		$this->upToken = $config['upToken'];
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

    public function upFile($filename, $filepath)
    {
        require_once(LIBPATH.'Qiniu/io.php');
        require_once(LIBPATH.'Qiniu/rs.php');

        $this->getUpToken();


        Qiniu_SetKeys($accessKey, $secretKey);

        $putExtra = new Qiniu_PutExtra();
        $putExtra->Crc32 = 1;
        $info = Qiniu_PutFile($this->upToken, $filename, $filepath, $putExtra);
        
        return array(
            'info' => $info[0],
            'error' => $info[1]
            );
    }

    public function getImageBaseUrl($filename)
    {
        require_once(LIBPATH.'Qiniu/rs.php');
        require_once(LIBPATH.'Qiniu/fop.php');
        
        $domain = $this->bucket.'.qiniudn.com';

        Qiniu_SetKeys($this->accessKey, $this->secretKey);
         
        //生成baseUrl
        $baseUrl = Qiniu_RS_MakeBaseUrl($domain, $filename);

        return $baseUrl;
    }

    public function getImageView($mode = 0, $width = '', $height = '', $format = '', $interlace='' )
    {
        require_once(LIBPATH.'Qiniu/rs.php');
        require_once(LIBPATH.'Qiniu/fop.php');
        
        $domain = $this->bucket.'.qiniudn.com';

        Qiniu_SetKeys($this->accessKey, $this->secretKey);
         
        //生成baseUrl
        $baseUrl = Qiniu_RS_MakeBaseUrl($domain, $filename);

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

    //上传到七牛
    //单个文件
    //formname: 表单名字; pre: 图片Url中显示的图片名字(也就是七牛中的key)
    public function upImage($formname, $pre='')
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
        $rand_time = date('YmdHis').mt_rand(0, 99);
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

    public function upMoreImage($formname, $pre)
    {
        if (empty($_FILES[$formname])) {
            return '';
        }

        $count = count($_FILES[$formname]['name']);

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

        $this->CI->load->library('qiniu');
        $arrUpInfo = array();
        foreach ($arrImages as $k => $v) {
            //取得后缀
            $realname = $v['name'];
            $arrRealName = explode('.', $realname);
            $file_name = $arrRealName[0];
            $stuffix = end($arrRealName);

            //组织完整入库文件名
            $rand_time = $this->getMsec();
            $qiniu_name = 'qiniu'.$rand_time.'_'.$pre.'.'.$stuffix;
            $filepath = $v['tmp_name'];

            //上传
            $this->CI->qiniu->upFile($qiniu_name, $filepath);

            $arrUpInfo[$k] = array(
                'qiniu_name' => $qiniu_name,
                'file_name' => $file_name
                );
        }

        return $arrUpInfo;

    }
}