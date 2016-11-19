<?php

require_once("auth_digest.php");
require_once("utils.php");

// --------------------------------------------------------------------------------
// class Qiniu_ImageView

class Qiniu_ImageView {
	public $Mode;
    public $Width;
    public $Height;
    public $Quality;
    public $Format;
    public $Interlace;

    public function MakeRequest($url = '')
    {
    	$ops = array($this->Mode);

    	if (!empty($this->Width)) {
    		$ops[] = 'w/' . $this->Width;
    	}
    	if (!empty($this->Height)) {
    		$ops[] = 'h/' . $this->Height;
    	}
    	if (!empty($this->Quality)) {
    		$ops[] = 'q/' . $this->Quality;
    	}
    	if (!empty($this->Format)) {
    		$ops[] = 'format/' . $this->Format;
    	}
        if (!empty($this->Interlace)) {
            $ops[] = 'interlace/' . $this->Interlace;
        }

    	return $url . "imageView2/" . implode('/', $ops);
    }
}

class Qiniu_IMG_WaterMark {
    public $Mode;
    public $Width;
    public $Height;
    public $Quality;
    public $Format;
    public $Interlace;

    public function MakeRequest($url = '')
    {
        $ops = array($this->Mode);

        if (!empty($this->Width)) {
            $ops[] = 'w/' . $this->Width;
        }
        if (!empty($this->Height)) {
            $ops[] = 'h/' . $this->Height;
        }
        if (!empty($this->Quality)) {
            $ops[] = 'q/' . $this->Quality;
        }
        if (!empty($this->Format)) {
            $ops[] = 'format/' . $this->Format;
        }
        if (!empty($this->Interlace)) {
            $ops[] = 'interlace/' . $this->Interlace;
        }

        return $url . "?imageView/" . implode('/', $ops);
    }
}

class Qiniu_TXT_WaterMark {
    public $Mode = 2;
    public $Text;
    public $Font;
    public $FontSize;
    public $Fill;
    public $Disslove;
    public $Gravity;
    public $Dx;
    public $Dy;

    public function MakeRequest($url = '')
    {
        $ops = array($this->Mode);

        if (!empty($this->Text)) {
            $ops[] = 'text/' . Qiniu_Encode($this->Text);
        }
        if (!empty($this->Font)) {
            $ops[] = 'font/' . Qiniu_Encode($this->Font);
        }
        if (!empty($this->FontSize)) {
            $ops[] = 'fontsize/' . $this->FontSize;
        }
        if (!empty($this->Fill)) {
            $ops[] = 'fill/' . Qiniu_Encode($this->Fill);
        }
        if (!empty($this->Disslove)) {
            $ops[] = 'dissolve/' . $this->Disslove;
        }
        if (!empty($this->Gravity)) {
            $ops[] = 'gravity/' . $this->Gravity;
        }
        if (!empty($this->Dx)) {
            $ops[] = 'dx/' . $this->Dx;
        }
        if (!empty($this->Dy)) {
            $ops[] = 'dy/' . $this->Dy;
        }


        return $url .'watermark/'. implode('/', $ops);
    }
}

// --------------------------------------------------------------------------------
// class Qiniu_Exif

class Qiniu_Exif {

	public function MakeRequest($url)
	{
		return $url . "?exif";
	}

}

// --------------------------------------------------------------------------------
// class Qiniu_ImageInfo

class Qiniu_ImageInfo {

	public function MakeRequest($url)
	{
		return $url . "?imageInfo";
	}

}
