<?php

class ZOL_Http {

    public static function sendHeader($arg, $exit = 0) {
        if (is_string($arg)) {
            header($arg);
        } elseif (is_int($arg)) {
            if (self::getStatusByCode($arg)) {
                header(self::getStatusByCode($arg));
            } else {

                return false;
            }
        }
        if ($exit) {
            exit(0);
        }
    }

    /**
     *  利用curl的形式获得页面请求 请用这个函数取代file_get_contents
     */
    public static function curlPage($paramArr){
       if (is_array($paramArr)) {
			$options = array(
				'url'      => false, #要请求的URL数组
				'timeout'  => 2,#超时时间 s
			);
			$options = array_merge($options, $paramArr);
			extract($options);
		}
        $timeout = (int)$timeout;

        if(0 == $timeout || empty($url))return false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); #避免首先解析ipv6
        }
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
	 /**
     *  利用curl POST数据
     */
    public static function curlPost($paramArr){
       
		$options = array(
			'url'      => false, #要请求的URL数组
			'postdata' => '', #POST的数据
			'timeout'  => 2,#超时时间 s
		);
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        $timeout = (int)$timeout;
        if(0 == $timeout || empty($url))return false;


		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt ($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); #避免首先解析ipv6
        }
		$content = curl_exec( $ch );
		curl_close ( $ch );

        return $content;
    }
    /**
     *  利用 curl_multi_** 的函数,并发多个请求
     */
    public static function multiCurl($paramArr){
       if (is_array($paramArr)) {
			$options = array(
				'urlArr'   => false, #要请求的URL数组
				'timeout'  => 10,#超时时间 s
			);
			$options = array_merge($options, $paramArr);
			extract($options);
		}
        $timeout = (int)$timeout;

        if(0 == $timeout)return false;

        $result = $res = $ch = array();
        $nch = 0;
        $mh = curl_multi_init();
        foreach ($urlArr as $nk => $url) {

            $ch[$nch] = curl_init();
            curl_setopt_array($ch[$nch], array(
                                            CURLOPT_URL => $url,
                                            CURLOPT_HEADER => false,
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_TIMEOUT => $timeout,
                                            ));
            curl_multi_add_handle($mh, $ch[$nch]);
            ++$nch;
        }
        /* 执行请求 */
        do {
            $mrc = curl_multi_exec($mh, $running);
        } while (CURLM_CALL_MULTI_PERFORM == $mrc);

        while ($running && $mrc == CURLM_OK) {
            if (curl_multi_select($mh, 0.5) > -1) {
                do {
                    $mrc = curl_multi_exec($mh, $running);
                } while (CURLM_CALL_MULTI_PERFORM == $mrc);
            }
        }

        if ($mrc != CURLM_OK) {

        }

        /* 获得数据 */
        $nch = 0;
        foreach ($urlArr as $moudle=>$node) {
            if (($err = curl_error($ch[$nch])) == '') {
                $res[$nch]=curl_multi_getcontent($ch[$nch]);
                $result[$moudle]=$res[$nch];
            }
            curl_multi_remove_handle($mh,$ch[$nch]);
            curl_close($ch[$nch]);
            ++$nch;
        }
        curl_multi_close($mh);
        return 	$result;

    }

    /**
    * 获得用户的IP地址
    */
    public static function getUserIp(){
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : NULL;
    }

    /**
    * 得到网友的详细IP信息
    * ********特别注意:************
    *      这个获得地址是多个的:10.19.8.12, 118.67.120.27, 127.0.0.1 因此要程序进行区分
    * 如果只想获得一个IP,请用下面的 getClientIp()
    */
	public static function getClientIpMulti(){
	  $realip = ZOL_Api::run("Service.Area.getClientIp" , array());
	  return $realip;
	}

	/**
	 * 得到网友的详细IP信息
	 * 只会获得第一个IP地址,
	 * @param toLongFlag 是否获得数字
	 */
	public static function getClientIp($toLongFlag = false){
		$ip = self::getClientIpMulti();
		$ipArr = explode(",",$ip);
		$ip = is_array($ipArr) ? $ipArr[0] : $ipArr;
		if($toLongFlag)$ip = ip2long($ip);
		return $ip;

	}

	public static function ip2location($ip)
    {
        $ipClass = new IpLocation;
        $location = $ipClass->getlocation($ip);
		return $location;
	}

	/**
	 * 得到用户所在地区信息
	 */
	public static function getUserArea()
	{
		$ip       = self::getClientIpMulti();
        //$ip = '58.42.250.17';
		$ipArr    = explode(",",$ip);
		$ipNum    = count($ipArr);
		$ip       = $ipArr[0];#这个是加了一个广告代理，所以ip变成三个了，取第一个 2010-06-18
		$cacheObj = ZOL_DAL_RefreshCacheLoader::getInstance();
        $ipInfo       = ZOL_Api::run("Service.Area.getIp" , array('ip'=>$ip,'setCookie'=>1));
        $cityName     = $ipInfo['city'];
        $provinceName = $ipInfo['province'];
        $provinceId   = $ipInfo['provinceId'];
        $locationId   = $ipInfo['locationId'];
        $cityId       = $ipInfo['cityId'];
        $coutyId      = $ipInfo['countyId'];
        if($locationId<0) {
            $locationId = 1;
        }
        #重庆市这几个区识别到市 亮总需求#
        if($provinceId == 4 && in_array($cityId,array(705,703,709,702,706,708,704,700,707))){
            $cityId = 0;
        }
        #北京，上海，天津 不用区分区#
        if(in_array($provinceId,array(1,2,3))){
            $cityId = 0;
        }
        /*
		$tmpProvinceArr = $cacheObj->loadCacheObject('Province',array());
		$provinceArr = array();
		if($tmpProvinceArr){
			foreach($tmpProvinceArr as $key=>$value){
				$provinceName = ZOL_String::substr($value,4);
				$provinceArr[$provinceName] = $key;
			}
		}
		$ipName = self::ip2location($ip);
		if (strpos($ipName, '省')) {
			$ipName       = explode('省', $ipName);
			$cityName     = ZOL_String::substr($ipName[1], 4);
			$provinceName = ZOL_String::substr($ipName[0], 4);
		} else {
			$cityName     = '';
			$provinceName = ZOL_String::substr($ipName, 4);
		}

		#省份
		$provinceId = 1;
		if ($cityName && array_key_exists($cityName, $provinceArr)) {
			$provinceId = $provinceArr[$cityName];
		} elseif ($provinceName && array_key_exists($provinceName, $provinceArr)) {
			$provinceId = $provinceArr[$provinceName];
		}

		#城市
		$cityId = 0;
		if ($cityName && $provinceId) {
			$tmpCityArr = $cacheObj->loadCacheObject('City',array('provinceId' => $provinceId));
			foreach ($tmpCityArr as $key => $val) {
				$val = ZOL_String::substr($val, 4);
				if($val == $cityName){
					$cityId = $key;
					break;
				}
			}
		}
        
		#locationId获得
		$tmpLocationArr = $cacheObj->loadCacheObject('Location',array('type' => 'LOCATION'));
		$locationArr = array();
		if ($tmpLocationArr) {
			foreach($tmpLocationArr as $key=>$value){
				$locationName = ZOL_String::substr($value['name'],4);
				$locationArr[$locationName] = $key;
			}
		}
		#根据省的名称和城市的名称,获得LocationId
		$locationId = 1;
		if($cityName && isset($locationArr[$cityName])){
			$locationId = $locationArr[$cityName];
		} elseif ($provinceName && isset($locationArr[$provinceName])) {
			$locationId = $locationArr[$provinceName];			
		}
        */

        $realLocationId = $locationId;
		if(isset($tmpLocationArr[$locationId]['defaultId'])) {
			$locationId = $tmpLocationArr[$locationId]['defaultId'];
		}
        $fLocationId = $locationId;
        if(isset($tmpLocationArr[$locationId]['fid'])){
            $fLocationId = $tmpLocationArr[$locationId]['fid'];
        }

        /*
		setcookie('userProvinceId', $provinceId, SYSTEM_TIME + 86400*3, '/', '.zol.com.cn');
		setcookie('userCityId', $cityId, SYSTEM_TIME + 86400*3, '/', '.zol.com.cn');
		setcookie('userLocationId', $locationId, SYSTEM_TIME + 86400*3, '/', '.zol.com.cn');
        */
		setcookie('realLocationId', $realLocationId, SYSTEM_TIME + 86400*3, '/', '.zol.com.cn');
		setcookie('userFidLocationId', $fLocationId, SYSTEM_TIME + 86400*3, '/', '.zol.com.cn');


		return array('provinceId' => $provinceId, 'cityId' => $cityId, 'countyId' => $coutyId, 'userLocationId' => $locationId,'realLocationId'=>$realLocationId,'userFidLocationId'=>$fLocationId);
	}

    /**
     * 设置404 Header信息
     */
    public static function send404Header(){
        Libs_Global_PageHtml::setExpires(0); #清除过期时间
        header('Content-type:text/html; Charset=gb2312');
        header(self::getStatusByCode(404)); #设置404 header信息

    }
	
    /**
     * 设置301 Header信息
     */
    public static function send301Header(){
        Libs_Global_PageHtml::setExpires(0); #清除过期时间
        header('Content-type:text/html; Charset=gb2312');
        header(self::getStatusByCode(301)); #设置301 header信息
    }
	 /**
     * 设置301 Header信息
     */
    public static function send403Header(){
        Libs_Global_PageHtml::setExpires(0); #清除过期时间
        header('Content-type:text/html; Charset=gb2312');
        header(self::getStatusByCode(403)); #设置301 header信息
    }
    protected static function getStatusByCode($code) {
        $status = array(
    100 => "HTTP/1.1 100 Continue",
    101 => "HTTP/1.1 101 Switching Protocols",
    200 => "HTTP/1.1 200 OK",
    201 => "HTTP/1.1 201 Created",
    202 => "HTTP/1.1 202 Accepted",
    203 => "HTTP/1.1 203 Non-Authoritative Information",
    204 => "HTTP/1.1 204 No Content",
    205 => "HTTP/1.1 205 Reset Content",
    206 => "HTTP/1.1 206 Partial Content",
    300 => "HTTP/1.1 300 Multiple Choices",
    301 => "HTTP/1.1 301 Moved Permanently",
    302 => "HTTP/1.1 302 Found",
    303 => "HTTP/1.1 303 See Other",
    304 => "HTTP/1.1 304 Not Modified",
    305 => "HTTP/1.1 305 Use Proxy",
    307 => "HTTP/1.1 307 Temporary Redirect",
    400 => "HTTP/1.1 400 Bad Request",
    401 => "HTTP/1.1 401 Unauthorized",
    402 => "HTTP/1.1 402 Payment Required",
    403 => "HTTP/1.1 403 Forbidden",
    404 => "HTTP/1.1 404 Not Found",
    405 => "HTTP/1.1 405 Method Not Allowed",
    406 => "HTTP/1.1 406 Not Acceptable",
    407 => "HTTP/1.1 407 Proxy Authentication Required",
    408 => "HTTP/1.1 408 Request Time-out",
    409 => "HTTP/1.1 409 Conflict",
    410 => "HTTP/1.1 410 Gone",
    411 => "HTTP/1.1 411 Length Required",
    412 => "HTTP/1.1 412 Precondition Failed",
    413 => "HTTP/1.1 413 Request Entity Too Large",
    414 => "HTTP/1.1 414 Request-URI Too Large",
    415 => "HTTP/1.1 415 Unsupported Media Type",
    416 => "HTTP/1.1 416 Requested range not satisfiable",
    417 => "HTTP/1.1 417 Expectation Failed",
    500 => "HTTP/1.1 500 Internal Server Error",
    501 => "HTTP/1.1 501 Not Implemented",
    502 => "HTTP/1.1 502 Bad Gateway",
    503 => "HTTP/1.1 503 Service Unavailable",
    504 => "HTTP/1.1 504 Gateway Time-out"  
        );
        if (!empty($status[$code])) {
            
            return $status[$code];
        }
        return false;
    }
    
    /**
     * 判断是不是真正的人在访问
     */
    public static function isRealPerson(){
        $input = ZOL_Registry::get('request');
        if($input){
            $ipck           = $input->cookie("ip_ck");
            $userLocationId = $input->cookie("userLocationId");
            if(!$ipck && !$userLocationId){
                return false;//不是真人
            }
        }
        
        return true;//是真人
    }
    
    public static function isCrawler() {
        $userAgent = strtolower(isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'');
        $spiders = array(
            'Googlebot', // Google 爬虫
            'Baiduspider', // 百度爬虫
            'Yahoo! Slurp', // 雅虎爬虫
            'YodaoBot', // 有道爬虫
            'msnbot', // Bing爬虫
            'Sosospider',
            'iaskspider',
            'Sogou web spider',
            'bingbot',
            '360Spider',
            'EasouSpider',
            'YandexBot',
            'ChinasoSpider',
            'tigerbot',
            'Facebot',
            'YisouSpider',
        );
        foreach ($spiders as $spider) {
            $spider     = strtolower($spider);
            $userAgent  = strtolower($userAgent);
            if(stripos($userAgent, $spider) !== false) {
                return true;
            }
        }
        if (strpos($userAgent, 'sogou') !== false && strpos($userAgent, 'spider') !== false) {
            return true;
        }
        return false;
    }
}
