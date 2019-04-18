<?php
/**
* 验证码相关处理
* @author 仲伟涛 <zhong.weitao@zol.com.cn>
* @copyright (c) 2011-06-20
* @version v1.0
*/
class Libs_Global_CheckCode
{
	const ZOL_CHECKCODE_KEY = 'mdpyw$@4sa^2fa*%';

    /**
     * 获得简单的验证码图片
     * @param array $paramArr 
     */
    public static function getCodeSimpleImage($paramArr){
        $options = array(
            'width'  => 80,
            'height' => 20,
            'numCnt' => 4,
            'inCode' => 'ABCD',
        );
        if (is_array($paramArr)) {
			$options = array_merge($options, $paramArr);
		}
        extract($options);
        $text = self::getAuthCode($inCode);


        $font  = APP_HTML_DIR . '/include/fonts/'. rand(1, 6).'.ttf';
        $size = 14;

        $image = imagecreate($width, $height);

        $whiteColor = imagecolorallocate($image, 255, 255, 255);
        $blackColor = imagecolorallocate($image,   0,   0,   0);

        $count = $width * $height / 8;

        for($i = 0; $i < $count; $i++){
            $randomColor = imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagesetpixel($image, mt_rand(0, $width), mt_rand(0, $height), $randomColor);
        }

        $fontSize = imagettfbbox($size, 0, $font, $text);

        $centerX = abs($fontSize[2] - $fontSize[0]);
        $centerY = abs($fontSize[5] - $fontSize[3]);

        $x = ($width - $centerX) / 2;
        $y = ($height  - $centerY) / 2 + $centerY;

        imagettftext($image, $size, mt_rand(-2, +2), $x, $y - 2, $blackColor, $font, $text);
        imagerectangle($image, 0, 0, $width - 1, $height - 1, $blackColor);
        header("Content-type:image/png");
        imagepng($image);
        imagedestroy($image);
    }
    /**
     * 获得彩色的验证码图片
     * @param array $paramArr 
     */
    public static function getCodeColorImage($paramArr)
    {

        $options = array(
            'width'  => 80,
            'height' => 30,
            'numCnt' => 4,
            'inCode' => 'ABCD',
        );
        if (is_array($paramArr)) {
			$options = array_merge($options, $paramArr);
		}
        extract($options);
        

        $img     = imagecreate($width, $height);
        $bgcolor = self::getRandColor($img,200);//背景色
        $fontCnt = 6;

        $numCnt = 4;
        $authCode = self::getAuthCode($inCode);

        for($i=0; $i < $numCnt; $i++)
        {
            $padding_left = rand(5,10);
            $left = $padding_left+($width-10)*$i/$numCnt;
            //加入多边形色块干扰
            imagefilledpolygon($img,
                                    array(
                                        rand($left,$left+20), rand(0,$height),
                                        rand($left,$left+20), rand(0,$height),
                                        rand($left,$left+20), rand(0,$height),
                                        rand($left,$left+20), rand(0,$height),
                                        rand($left,$left+20), rand(0,$height),
                                        rand($left,$left+20), rand(0,$height),
                                        rand($left,$left+20), rand(0,$height),
                                        rand($left,$left+20), rand(0,$height),
                                        rand($left,$left+20), rand(0,$height),
                                        rand($left,$left+20), rand(0,$height),
                                    ), 10, self::getRandColor($img,180));
            $fontFile = SYSTEM_VAR . 'fonts/' . rand(1, $fontCnt).'.ttf';
            imagettftext($img, rand(18,24), rand(-30,30), $left, rand(22,26), self::getRandColor($img,0,120), $fontFile, $authCode[$i]);
        }
        //干扰像素，随机位置，随机颜色
        for($i=0;$i<300;$i++)
        {
            $rand_x = rand(0,$width-1);
            $rand_y = rand(0,$height-1);
            imagesetpixel($img, $rand_x, $rand_y, self::getRandColor($img));
        }
        header("Content-type:image/png");
        imagepng($img);
        imagedestroy($img);
        exit;

    }

    /**
     * 获得随机的颜色
     * @param <type> $img
     * @param int $min 默认0
     * @param int $max 默认255
     * @return <type>
     */
    private static function getRandColor($img, $min=0, $max=255)
    {
        return imagecolorallocate($img,rand($min,$max),rand($min,$max),rand($min,$max));
    }

    /**
    * 根据传入的字符获得验证码
    *
    * @param array $paramArr 参数数组
    * @return string 返回所有的meta标签
    */
    public static function getAuthCode($inCode)
    {
        //算法需要调整成复杂的
        $codeStr = $inCode . self::ZOL_CHECKCODE_KEY;
        $codeStr = md5($codeStr);
        //echo $codeStr;exit;
        $codeStr = strtoupper(substr($codeStr, 0,4));
        $codeStr = str_replace(array("0","O"), array("A","B"), $codeStr);
        return $codeStr;
    }

    /**
    * 进行验证验证码
    */
    public static function doCheckCode($paramArr)
	{
        $options = array(
            'checkcode'     => 0,     #输入的验证码
            'hidecode'      => 0,     #hide码
            'timeout'       => 3600,  #验证码失效时间
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		$hidecode = $hidecode;
		if(SYSTEM_TIME - $hidecode < $timeout){#如果超时报验证码错误		
			$trueCode = self::getAuthCode($hidecode);
			if($trueCode == strtoupper($checkcode)){ #验证码错误
				return true;#验证ok
			}
		}

		return false;
}

}
