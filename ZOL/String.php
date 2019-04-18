<?php

/*
|---------------------------------------------------------------
| Various static string helper methods.
|---------------------------------------------------------------
| @package ZOL
|
*/

class ZOL_String
{

	public static function trimWhitespace($var)
	{
		if (!isset($var)) {
			return false;
		}
		if (is_array($var)) {
			$newArray = array();
			foreach ($var as $key => $value) {
				$newArray[$key] = self::trimWhitespace($value);
			}
			return $newArray;
		} else {
			return trim($var);
		}
	}

	/*
	|---------------------------------------------------------------
	| Returns cleaned user input.
	|---------------------------------------------------------------
	| @access  public
	| @param   string $var  The string to clean.
	| @return  string       $cleaned result.
	*/
	public static function clean($var)
	{
		if (!isset($var)) {
			return false;
		}
		$var = self::trimWhitespace($var);
		if (is_array($var)) {
			$newArray = array();
			foreach ($var as $key => $value) {
				$newArray[$key] = self::clean(self::addslashes($value));
			}
			return $newArray;
		} else {
			return strip_tags($var);
		}
	}

	public static function removeJs($var)
	{
		if (!isset($var)) {
			return false;
		}
		$var = self::trimWhitespace($var);
		if (is_array($var)) {
			$newArray = array();
			foreach ($var as $key => $value) {
				$newArray[$key] = self::removeJs($value);
			}
			return $newArray;
		} else {
			$search = "/<script[^>]*?>.*?<\/script\s*>/i";
			$replace = '';
			$clean = preg_replace($search, $replace, $var);
			return $clean;
		}
	}

	public static function toValidVariableName($str)
	{
		//  remove illegal chars
		$search = '/[^a-zA-Z1-9_]/';
		$replace = '';
		$res = preg_replace($search, $replace, $str);
		//  ensure 1st letter is lc
		$firstLetter = strtolower($res[0]);
		$final = substr_replace($res, $firstLetter, 0, 1);
		return $final;
	}

	public static function toValidFileName($origName)
	{
		return self::dirify($origName);
	}

	//  from http://kalsey.com/2004/07/dirify_in_php/
	public static function dirify($s)
	{
		 $s = self::_convertHighAscii($s);     ## convert high-ASCII chars to 7bit.
		 $s = strtolower($s);                       ## lower-case.
		 $s = strip_tags($s);                       ## remove HTML tags.
		 // Note that &nbsp (for example) is legal in HTML 4, ie. semi-colon is optional if it is followed
		 // by a non-alphanumeric character (eg. space, tag...).
//         $s = preg_replace('!&[^;\s]+;!','',$s);    ## remove HTML entities.
		 $s = preg_replace('!&#?[A-Za-z0-9]{1,7};?!', '', $s);    ## remove HTML entities.
		 $s = preg_replace('![^\w\s-]!', '',$s);    ## remove non-word/space chars.
		 $s = preg_replace('!\s+!', '_',$s);        ## change space chars to underscores.
		 return $s;
	}

	protected static function _convertHighAscii($s)
	{
		// Seems to be for Latin-1 (ISO-8859-1) and quite limited (no ae/oe, no y:/Y:, etc.)
		 $aHighAscii = array(
		   "!\xc0!" => 'A',    # A`
		   "!\xe0!" => 'a',    # a`
		   "!\xc1!" => 'A',    # A'
		   "!\xe1!" => 'a',    # a'
		   "!\xc2!" => 'A',    # A^
		   "!\xe2!" => 'a',    # a^
		   "!\xc4!" => 'A',    # A:
		   "!\xe4!" => 'a',    # a:
		   "!\xc3!" => 'A',    # A~
		   "!\xe3!" => 'a',    # a~
		   "!\xc8!" => 'E',    # E`
		   "!\xe8!" => 'e',    # e`
		   "!\xc9!" => 'E',    # E'
		   "!\xe9!" => 'e',    # e'
		   "!\xca!" => 'E',    # E^
		   "!\xea!" => 'e',    # e^
		   "!\xcb!" => 'E',    # E:
		   "!\xeb!" => 'e',    # e:
		   "!\xcc!" => 'I',    # I`
		   "!\xec!" => 'i',    # i`
		   "!\xcd!" => 'I',    # I'
		   "!\xed!" => 'i',    # i'
		   "!\xce!" => 'I',    # I^
		   "!\xee!" => 'i',    # i^
		   "!\xcf!" => 'I',    # I:
		   "!\xef!" => 'i',    # i:
		   "!\xd2!" => 'O',    # O`
		   "!\xf2!" => 'o',    # o`
		   "!\xd3!" => 'O',    # O'
		   "!\xf3!" => 'o',    # o'
		   "!\xd4!" => 'O',    # O^
		   "!\xf4!" => 'o',    # o^
		   "!\xd6!" => 'O',    # O:
		   "!\xf6!" => 'o',    # o:
		   "!\xd5!" => 'O',    # O~
		   "!\xf5!" => 'o',    # o~
		   "!\xd8!" => 'O',    # O/
		   "!\xf8!" => 'o',    # o/
		   "!\xd9!" => 'U',    # U`
		   "!\xf9!" => 'u',    # u`
		   "!\xda!" => 'U',    # U'
		   "!\xfa!" => 'u',    # u'
		   "!\xdb!" => 'U',    # U^
		   "!\xfb!" => 'u',    # u^
		   "!\xdc!" => 'U',    # U:
		   "!\xfc!" => 'u',    # u:
		   "!\xc7!" => 'C',    # ,C
		   "!\xe7!" => 'c',    # ,c
		   "!\xd1!" => 'N',    # N~
		   "!\xf1!" => 'n',    # n~
		   "!\xdf!" => 'ss'
		 );
		 $find = array_keys($aHighAscii);
		 $replace = array_values($aHighAscii);
		 $s = preg_replace($find, $replace, $s);
		 return $s;
	}

	protected function _to7bit($text)
	{
		if (!function_exists('mb_convert_encoding')) {
			return $text;
		}
		$text = mb_convert_encoding($text,'HTML-ENTITIES',mb_detect_encoding($text));
		$text = preg_replace(
		   array('/&szlig;/','/&(..)lig;/',
				 '/&([aouAOU])uml;/','/&(.)[^;]*;/'),
		   array('ss',"$1","$1".'e',"$1"),
		   $text);
		return $text;
	}

	/*
	|---------------------------------------------------------------
	| Replaces accents in string.
	|---------------------------------------------------------------
	| @todo make it work with cyrillic chars
	| @todo make it work with non utf-8 encoded strings
	| @see ZOL_String::isCyrillic()
	| @param string $str
	| @return string
	*/
	public static function replaceAccents($str)
	{
		if (!self::_isCyrillic($str)) {
			$str = self::_to7bit($str);
			$str = preg_replace('/[^A-Z^a-z^0-9()]+/',' ',$str);
		}
		return $str;
	}

	/*
	|---------------------------------------------------------------
	| Checks if strings has cyrillic chars.
	|---------------------------------------------------------------
	| @param string $str
	| @return boolean
	*/
	protected function _isCyrillic($str)
	{
		$ret = false;
		if (function_exists('mb_convert_encoding') && !empty($str)) {
			// codes for Russian chars
			$aCodes = range(1040, 1103);
			// convert to entities
			$encoded = mb_convert_encoding($str, 'HTML-ENTITIES',
				mb_detect_encoding($str));
			// get codes of the string
			$aChars = explode(';', str_replace('&#', '', $encoded));
			array_pop($aChars);
			$aChars = array_unique($aChars);
			// see if cyrillic chars there
			$aNonCyrillicChars = array_diff($aChars, $aCodes);
			// if string is the same -> no cyrillic chars
			$ret = count($aNonCyrillicChars) != count($aChars);
		}
		return $ret;
	}

	/*
	|---------------------------------------------------------------
	| Removes chars that are illegal in ini files.
	|---------------------------------------------------------------
	| @param string $string
	| @return string
	*/
	public static function stripIniFileIllegalChars($string)
	{
		return preg_replace("/[\|\&\~\!\"\(\)]/i", "", $string);
	}

	/*
	|---------------------------------------------------------------
	| Converts strings representing constants to int values.
	| Used for when constants are stored as strings in config.
	|---------------------------------------------------------------
	| @param string $string
	| @return integer
	*/
	public static function pseudoConstantToInt($string)
	{
		$ret = 0;
		if (is_int($string)) {
			$ret = $string;
		}
		if (is_numeric($string)) {
			$ret = (int)$string;
		}
		if (ZOL_Inflector::isConstant($string)) {
			$const = str_replace("'", '', $string);
			if (defined($const)) {
				$ret = constant($const);
			}
		}
		return $ret;
	}

	/*
	|---------------------------------------------------------------
	| Esacape single quote.
	|---------------------------------------------------------------
	| @param string $string
	| @return  string
	*/
	public static function escapeSingleQuote($string)
	{
		$ret = str_replace('\\', '\\\\', $string);
		$ret = str_replace("'", '\\\'', $ret);
		return $ret;
	}


	/*
	|---------------------------------------------------------------
	| Escape single quotes in every key of given array.
	|---------------------------------------------------------------
	| @param   array $array
	| @static
	*/
	public static function escapeSingleQuoteInArrayKeys($array)
	{
		$ret = array();
		foreach ($array as $key => $value) {
			$k = self::escapeSingleQuote($key);
			$ret[$k] = is_array($value)
				? self::escapeSingleQuoteInArrayKeys($value)
				: $value;
		}
		return $ret;
	}

	/*
	|---------------------------------------------------------------
	| 将一个字串中含有全角或半角的数字字符、字母、空格或'%+-()'字符互换
	|---------------------------------------------------------------
	| @static
	| @access  public
	| @param   string       $str         待转换字串
	| @param   boolean      $reverse     默认true为全角转半角, false为半角转全角
	| @return  string       $str         处理后字串
	*/

	public static function convertSemiangle($str, $reverse = true)
	{
		$arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
					 '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
					 'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
					 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
					 'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
					 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
					 'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
					 'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
					 'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
					 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
					 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
					 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
					 'ｙ' => 'y', 'ｚ' => 'z',
					 '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
					 '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
					 '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
					 '》' => '>',
					 '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
					 '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
					 '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
					 '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
					 '　' => ' ');
		if (false === $reverse)
		{
			$arr = array_flip($arr);
		}
		return strtr($str, $arr);
	}

	/**
	 * convert utf-8 encoding data to other encodings
	 *
	 * @param mixed $input
	 * @param string $encoding
	 * @return mixed
	 */
	public static function u8conv($input, $encoding='GBK')
	{
		if(is_array($input)){
	        foreach($input as $key=>$val){
	            $input[$key] = self::u8conv($val, $encoding);
	        }
	    } else {
	        //$input = iconv('UTF-8', $encoding, $input);
            $input = mb_convert_encoding($input,$encoding,"UTF-8");
	    }
	    return $input;
	}

	/**
	 * 将字符转为utf8字符
	 */
	public static function convToU8($input, $encoding='GBK')
	{
        #各种恶心的数据生成xml报错 各种报错啊。。。下面这个是处理报错的 把ascii为1-7的符号全部替换为空
        if($encoding == 'GBK' && is_string($input)) $input = str_replace(array(chr(1),chr(2),chr(3),chr(4),chr(5),chr(6),chr(7)), '', $input);
        if(is_array($input)){
	        foreach($input as $key=>$val){
	            $input[$key] = self::convToU8($val, $encoding);
	        }
	    } else {
	        //$input = iconv($encoding, 'UTF-8//ignore', $input);
	        $input = mb_convert_encoding($input, 'UTF-8', $encoding);
	    }
	    return $input;
	}

	public static function stripslashes($val)
	{
		if (get_magic_quotes_gpc())
		{
			return stripslashes($val);
		} else {
			return $val;
		}
	}

	public static function addslashes($val)
	{
		if (!get_magic_quotes_gpc())
		{
			return addslashes($val);
		}
		else
		{
			return $val;
		}
	}

	public static function convertEncodingDeep($value, $target_lang, $source_lang)
	{
		if (empty($value))
		{
			return $value;
		}
		else
		{
			if (is_array($value))
			{
				foreach ($value as $k=>$v)
				{
					#$value[$k] = self::convertEncodingDeep($source_lang, $target_lang, $v);
                    $value[$k] = self::convertEncodingDeep($v,$target_lang,$source_lang);
				}
				return $value;
			}
			elseif (is_string($value))
			{
				return mb_convert_encoding($value, $target_lang, $source_lang);
			}
			else
			{
				return $value;
			}
		}
	}

	public static function addslashesDeep($value)
	{
		if (empty($value) || get_magic_quotes_gpc())
		{
			return $value;
		}
		else
		{
			return is_array($value) ? array_map(array(self, __FUNCTION__), $value) : addslashes($value);
		}
	}

	public static function substr($str, $len, $charset = 'gbk')
	{
        return self::substr_php($str, $len, $charset);
		/*if (!function_exists('cnsubstr_ext') || 'utf-8' == strtolower($charset))
		{
			return self::substr_php($str, $len, $charset);
		}
		else
		{
			return cnsubstr_ext($str, $len);
		}*/
	}
	public static function substr_php($str, $len, $charset = 'gbk')
	{
		if (empty($str))
		{
			return false;
		}
		if ($len >= strlen($str) || $len < 1)
		{
			return $str;
		}

		$str = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;','&nbsp;'), array('&', '"', '<', '>',' '), $str);

		$strcut = array();
		$temp_str = '';
		$sublen = (strtolower($charset) == 'utf-8') ? 3 : 2;
		for ($i = 0; $i < $len; ++ $i)
		{
			$temp_str = substr($str, 0, 1);

			if (ord($temp_str) > 127)
			{
				++ $i;
				if ($sublen == 3)
				{
					++ $i;
				}
				if($i < $len)
				{
					$strcut[] = substr($str, 0, $sublen);
					$str = substr($str, $sublen);
				}
			}
			else
			{
				if ($i < $len)
				{
					$strcut[] = substr($str, 0, 1);
					$str = substr($str, 1);
				}
			}
		}
		if (!empty($strcut))
		{
			$strcut = join($strcut);
			$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

			return $strcut;
		}
		else
		{
			return '';
		}
	}

    /**
     * 加密中文 同JS同名函数功能
     * @param string $str 要转码的字符
     * @param string $encoding 字符的编码方式
     * @return encode string 回返已转码的字符
     */
    public static function escape($str, $encoding = 'GBK', $prefix = '%')
    {
        $return = '';
        for ($x = 0; $x < mb_strlen($str, $encoding); $x++) {
            $s = mb_substr($str, $x, 1, $encoding);
            if (strlen($s) > 1) {//多字节字符
                $return .= $prefix . 'u' . strtoupper(bin2hex(mb_convert_encoding($s, 'UCS-2', $encoding)));
            } else {
                $return .= $prefix . strtoupper(bin2hex($s));
            }
        }
        return $return;
    }

    /**
     * UTF-8转GBK 用于rewrite后的关键字处理
     * @param string $str 要转码的字符
     * @return encode string 回返已转码的字符
     */
    public static function kwUrldecode($str)
    {
        $str = urldecode(str_replace('@', '%', $str)); //关键字转码
        $str = iconv('UTF-8', 'GBK', $str);
        return $str;
    }

    /**
     * GBK转UTF-8 用于rewrite后的关键字处理
     * @param string $str 要转码的字符
     * @return encode string 回返已转码的字符
     */
    public static function kwUrlencode($str)
    {
        $str = iconv('GBK', 'UTF-8', $str);
        $str = urlencode($str);
        $str = str_replace('%', '@', $str);
        return $str;
    }

	/**
	* 解析JS的escape编码
	*
	* @param string $str
	* @param string $encoding
	*/
	public static function unescape($str, $encoding = 'GBK', $prefix = '%',$stripTag=1)
	{
        $prefix != '%' && $str = str_replace($prefix, '%', $str);
        $str  = rawurldecode($str);
		$text = preg_replace_callback("/%u[0-9A-Za-z]{4}/", array(__CLASS__, 'unicode2Utf8'), $str);
        if($stripTag)$text = strip_tags($text);
		return self::u8conv($text, $encoding);
	}

    /**
     * 处理HTML中的转义字符
     * @param string $str 要处理的转义字符
     * @param string $encoding 转换后的编码方式
     * @return string
     */
    public static function recode($str, $encoding = 'GBK')
    {
        if (function_exists('recode')) {
            return recode("html..{$encoding}", $str);
        } else {
            return self::phprecode($str, $encoding);
        }
    }

    /**
     * PHP版的recode，只处理HTML中的转义字符
     * @param string $str 要处理的转义字符
     * @param string $encoding 转换后的编码方式
     * @return string
     */
    public static function phprecode($str, $encoding = 'GBK')
    {
        $text = preg_replace_callback("/&#[0-9]{1,5}/", array(__CLASS__, 'htmlDecode'), $str);
        return self::u8conv($text, $encoding);
    }

    public static function htmlDecode($ar)
    {
		$str = '';
        foreach ($ar as $val) {
            $c = substr($val, 2);
            if ($c < 0x80) {
                $str.= chr($c);
            } else if ($c < 0x800) {
                $str.= chr(0xC0 | $c>>6);
                $str.= chr(0x80 | $c & 0x3F);
            } else if ($c < 0x10000) {
                $str.= chr(0xE0 | $c>>12);
                $str.= chr(0x80 | $c>>6 & 0x3F);
                $str.= chr(0x80 | $c & 0x3F);
            } else if ($c < 0x200000) {
                $str.= chr(0xF0 | $c>>18);
                $str.= chr(0x80 | $c>>12 & 0x3F);
                $str.= chr(0x80 | $c>>6 & 0x3F);
                $str.= chr(0x80 | $c & 0x3F);
            }
        }
		return $str;
    }


	/**
	* 转换UNICODE编码为UTF8
	*
	* @param mixed $ar
	*/
	public static function unicode2Utf8($ar)
	{
		$c = '';
		foreach($ar as $val) {
			$val = intval(substr($val, 2), 16);
			if ($val < 0x7F) {        // 0000-007F 单字节
				$c .= chr($val);
			} elseif ($val < 0x800) { // 0080-0800 双字节
				$c .= chr(0xC0 | ($val / 64));
				$c .= chr(0x80 | ($val % 64));
			} else {                // 0800-FFFF 三字节
				$c .= chr(0xE0 | (($val / 64) / 64));
				$c .= chr(0x80 | (($val / 64) % 64));
				$c .= chr(0x80 | ($val % 64));
			}
		}
        return $c;
	}

    public static function utf82Unicode($str)
    {
        switch(strlen($c)) {
            case 1:
            return ord($c);
        case 2:
            $n = (ord($c[0]) & 0x3f) << 6;
            $n += ord($c[1]) & 0x3f;
            return $n;
        case 3:
            $n = (ord($c[0]) & 0x1f) << 12;
            $n += (ord($c[1]) & 0x3f) << 6;
            $n += ord($c[2]) & 0x3f;
            return $n;
        case 4:
            $n = (ord($c[0]) & 0x0f) << 18;
            $n += (ord($c[1]) & 0x3f) << 12;
            $n += (ord($c[2]) & 0x3f) << 6;
            $n += ord($c[3]) & 0x3f;
            return $n;
        }
    }

	/**
	 * 加密解密函数
	 *
	 * @param   string     加解密字符串
	 * @param   string       EN 加密 | DE 解密	 *
	 * @return  string
     * 例子:ZOL_String::mcrypt(serialize($arr),"EN","KEYKEY"); 加密数组
	 */
	public static function mcrypt($string="",$type="EN",$mcrypt_key='ZOL_FRAMEWORK'){

		$mcrypt_cipher_alg  = MCRYPT_RIJNDAEL_128;
		$iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt_cipher_alg,MCRYPT_MODE_ECB), MCRYPT_RAND);
		switch($type){
			case "EN":
				@$new_string=mcrypt_encrypt($mcrypt_cipher_alg, $mcrypt_key, $string, MCRYPT_MODE_ECB, $iv);
				$new_string = bin2hex($new_string);
				break;
			case "DE":
				@$string=pack("H*",$string);
				@$new_string=mcrypt_decrypt($mcrypt_cipher_alg, $mcrypt_key, $string, MCRYPT_MODE_ECB, $iv);
				$new_string = trim($new_string);
				break;

		}
		return $new_string;
	}

    /**
     * 数组编码转换
     * @author wang.tao5@zol.com.cn
     * @copyright 2011年9月19日11:07:48
     */
    public static function arrayIconv($in_charset,$out_charset,$arr){
        return eval('return ' . iconv($in_charset, $out_charset . '//IGNORE', var_export($arr,true) . ';' ));
    }

    /**
      *计算雅黑字体 英文字符长度
      **/
    public static function yahei_strlen($str) {
        $lencounter = 0;
        for($i = 0; $i < strlen ( $str ); $i ++) {
            $ch = $str [$i];
            if (ord ( $ch ) > 128) {
                $i ++;
                $lencounter ++;
            } else if ($ch == 'i' || $ch == 'I' || $ch == 'l' || $ch == ' ' || $ch == '.' || $ch == ':' || $ch == '-') {
                $lencounter += 0.2;
            } else if ($ch == '@'|| $ch == '_' || $ch == '（' || $ch == '×' || $ch == '）') {
                $lencounter += 1;
            } else if ($ch == 'f' || $ch == 'j' || $ch == 'r' || $ch == 't' || $ch == ';' || $ch == '(' || $ch == ')' || $ch == '*' || $ch == '!' || $ch == '\'') {
                $lencounter += 0.3;
            } else if ($ch >= '0' && $ch <= '9' ) {
                $lencounter += 0.52;
            } else if ($ch == 'm' || $ch == 'w') {
                $lencounter += 0.7;
            } else if ($ch >= 'a' && $ch <= 'z'&& $ch != 'm') {
                $lencounter += 0.6;
            } else if ($ch >= 'A' && $ch <= 'Z') {
                $lencounter += 0.58;
            } else {
                $lencounter ++;
            }
            //echo $ch.' ';
        }
        return ceil ( $lencounter * 2 );
    }

    /**
     * 利用Php的mb_strlen和strlen函数就可以轻松得知字符串的构成是全英文、英汉混合、还是纯汉字
        1、如果strlen返回的字符长度和mb_strlen以当前编码计算的长度一致，可以判断是纯英文字符串。
        2、如果strlen返回的字符长度和mb_strlen以当前编码计算的长度不一致，且strlen返回值同mb_strlen的返回值求余后得0可以判断为是全汉字的字符串。
        3、如果strlen返回的字符长度和mb_strlen以当前编码计算的长度不一致，且strlen返回值同mb_strlen的返回值求余后不为0，可以判断为是英汉混合的字符串
     * @author wanghb
     * @copyright 2011年9月19日11:07:48
     */
    public static function Check_stringType($str1) {
        $strA = trim($str1);
        $lenA = strlen($strA);
        $lenB = mb_strlen($strA, "utf-8");
        if ($lenA === $lenB) {
            return 1; //全英文
        } else {
            if ($lenA % $lenB == 0) {
                return 2; //全中文
            } else {
                return 3; //中英混合
            }
       }
    }

    /*把时间计算成几分钟前，几小时前，几天前*/
    public static function timeTran($the_time) {
        $now_time  = strtotime(date("Y-m-d H:i:s"));
        $show_time = $the_time;
        $dur = $now_time - $show_time;
        if ($dur < 0) {
            return date('m月d日 h:i', $the_time);
        } else if ($dur < 60) {
            return $dur.'秒前';
        } else if ($dur < 3600) {
            return floor($dur/60).'分钟前';
        } else if ($dur < 86400) {
            return floor($dur/3600).'小时前';
        } else if ($dur < 2592000){  #3天内
            return floor($dur/86400).'天前';
        } else if (date('Y',$the_time) == date('Y')){  #1年内
            return date('m月d日 h:i', $the_time);
        } else {
            return date('Y年m月d日 h:i', $the_time);
        }
    }
    /**
     * 判断字符串中有没有网址
     */
    public static function checkHasUrl($str){
        $re="/([A-Z0-9][A-Z0-9_-]*(?:\.[a-z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
        $check = 0;
	    if(preg_match($re,$str)){
            $check = 1;
        }
        return $check;
	}
    
    #出现在XML和JSON中的字符串处理 
    public static function filterSpecStr($keyWord) {
            $keyWord = str_replace("（", " ", $keyWord);
            $keyWord = str_replace("）", "", $keyWord);
            $keyWord = str_replace("、", " ", $keyWord);
            $keyWord = str_replace("/", "/", $keyWord);#2013年6月27日修改，保证跟全程一致
            $keyWord = str_replace("＼", '\\', $keyWord);#2013年6月27日修改，保证跟全程一致
            $keyWord = str_replace("！", "", $keyWord);
           // $keyWord = str_replace("＠", "@", $keyWord);#会导致乱码
            $keyWord = str_replace("＋", "+", $keyWord);
            $keyWord = str_replace("&nbsp;","", $keyWord);
            $keyWord = str_replace("nbsp;","", $keyWord);
            $keyWord = str_replace("`","", $keyWord);
            $keyWord = str_replace('"',"'", $keyWord);
            $keyWord = str_replace('—',"-", $keyWord);
            $keyWord = str_replace('‘',"", $keyWord);
            $keyWord = str_replace('，',",", $keyWord);
            $keyWord = str_replace('<',"(", $keyWord);
            $keyWord = str_replace('>',")", $keyWord);
            $keyWord = str_replace('＆#8226;'," ", $keyWord);
            $keyWord = str_replace('&#160;'," ", $keyWord);
            $keyWord = str_replace("&"," ", $keyWord);
            $keyWord = str_replace(array("\r\n","\r","\n"),",",$keyWord);
            $keyWord = preg_replace("/\s\s+/"," ",$keyWord);
            $keyWord = filter_var($keyWord, FILTER_SANITIZE_SPECIAL_CHARS);
            return $keyWord;
    }
    
    /**
     * 多维数组的in_array
     * @param type $needle
     * @param type $haystack
     * @return boolean
     */
    public static function deepInArray($needle,$haystack){
        if(empty($needle)){
            return false;
        }
        if(in_array($needle, $haystack)){
            return true;
        }
        foreach ($haystack as $key=>$value){
            if(!is_array($value)){
                if($value==$needle){
                    return false;
                }else{
                    continue;
                }
            }else{
                if(in_array($needle, $value)){
                    return true;
                }else{
                    self::deepInArray($needle,$value);
                }
            }
        }
    }

	public static function strReplaceOnce($needle, $replace, $haystack){
		$pos = stripos($haystack, $needle);
		if ($pos === false) {
			return $haystack;
		}
		return substr_replace($haystack, $replace, $pos, strlen($needle));
	}
    
    /**
     * 点击等上万数据的格式化
     */
    public static function hitChange($hits)
    {
        if($hits > 10000 && $hits < 100000) {
            $first_num  = substr($hits, 0,1);
            $second_num = substr($hits, 1,1);
            if($second_num >= 1) {
                $hits = $first_num.'.'.$second_num.'万';
            }else{
                $hits = $first_num.'万';
            }
        }elseif($hits > 100000 && $hits < 1000000){
            $first_num  = substr($hits, 0,1);
            $second_num = substr($hits, 1,1);
            $hits = $first_num.$second_num.'万';
        }elseif($hits > 1000000 && $hits < 10000000){
            $first_num  = substr($hits, 0,1);
            $second_num = substr($hits, 1,1);
            $third_num = substr($hits, 2,1);
            $hits = $first_num.$second_num.$third_num.'万';
        }elseif($hits > 10000000){
            $first_num  = substr($hits, 0,1);
            $second_num = substr($hits, 1,1);
            $third_num = substr($hits, 2,1);
            $four_num = substr($hits, 3,1);
            $hits = $first_num.$second_num.$third_num.$four_num.'万';
        }
        
        return $hits;
    }
    
    /**
     * 获得汉字的首字母
     * @param 要得到那个字符串的首字母
     * @return 获得的首字母
     */
    public static function getFirstLetter($input)
    {
        $dict = array(
            'A' => 0XB0C4, 'B' => 0XB2C0, 'C' => 0XB4ED, 'D' => 0XB6E9, 'E' => 0XB7A1,
            'F' => 0XB8C0, 'G' => 0XB9FD, 'H' => 0XBBF6, 'J' => 0XBFA5, 'K' => 0XC0AB,
            'L' => 0XC2E7, 'M' => 0XC4C2, 'N' => 0XC5B5, 'O' => 0XC5BD, 'P' => 0XC6D9,
            'Q' => 0XC8BA, 'R' => 0XC8F5, 'S' => 0XCBF9, 'T' => 0XCDD9, 'W' => 0XCEF3,
            'X' => 0XD1B8, 'Y' => 0XD4D0, 'Z' => 0XD7F9,
        );

        $str_1 = substr($input, 0, 1);
        if ($str_1 >= chr(0x81) && $str_1 <= chr(0xfe)) {
            $num = hexdec(bin2hex(substr($input, 0, 2)));
            foreach ($dict as $k => $v) {
                if($v >= $num) break;
            }

            #品牌特数字归类
            $bArr = array('e7cd');
            $dArr = array('f5f5','e0bd');
            $eArr = array('e2f9');
            $fArr = array('dcbd','ecb3','e8f3',);
            $gArr = array('dfc9');
            $hArr = array('e3fc','e5ab','e7fa');
            $jArr = array('ecba','ecab','f0a8');
            $kArr = array('dfc7');
            $lArr = array('efaa','e8b4','e1e2');
            $mArr = array('f7c8', 'f2fe');
            $oArr = array('e0de','daa9');
            $qArr = array('ecf7','f7e8', 'e7f7');
            $rArr = array('eea3');
            $sArr = array('f6e8');
            $wArr = array('ecbf','eac5','e1cb');
            $xArr = array('f6ce','ecc5','e7f4','e8af','dfc8');
            $yArr = array('e5ad');

            if (in_array(dechex($num), $bArr)) { $k = 'B'; }
            elseif (in_array(dechex($num), $dArr)) { $k = 'D'; }
            elseif (in_array(dechex($num), $eArr)) { $k = 'E'; }
            elseif (in_array(dechex($num), $fArr)) { $k = 'F'; }
            elseif (in_array(dechex($num), $gArr)) { $k = 'G'; }
            elseif (in_array(dechex($num), $hArr)) { $k = 'H'; }
            elseif (in_array(dechex($num), $jArr)) { $k = 'J'; }
            elseif (in_array(dechex($num), $kArr)) { $k = 'K'; }
            elseif (in_array(dechex($num), $lArr)) { $k = 'L'; }
            elseif (in_array(dechex($num), $mArr)) { $k = 'M'; }
            elseif (in_array(dechex($num), $oArr)) { $k = 'O'; }
            elseif (in_array(dechex($num), $qArr)) { $k = 'Q'; }
            elseif (in_array(dechex($num), $rArr)) { $k = 'R'; }
            elseif (in_array(dechex($num), $sArr)) { $k = 'S'; }
            elseif (in_array(dechex($num), $wArr)) { $k = 'W'; }
            elseif (in_array(dechex($num), $xArr)) { $k = 'X'; }
            elseif (in_array(dechex($num), $yArr)) { $k = 'Y'; }
            elseif ($num > 0XD7FF) {    //非常用字（3008个）按部首排列，无法拼音
                return '其他';
            }
            return $k;
        } else {
            return strtoupper($str_1);
        }
    }

    /**
	* 转换HTML为JS
	*/
	public static function convHtml2Js($content, $moduleName) {
		if (!($content && $moduleName)) {
			return false;
		}
		$content = addslashes(stripcslashes(str_replace(array("\r","\n"), array('',''), $content)));
		$content = 'document.getElementById("' . $moduleName . '").innerHTML=\'' . $content . '\';';
		return $content;
	}
}


