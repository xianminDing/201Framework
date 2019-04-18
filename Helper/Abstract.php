<?php
/**
* 插件抽象类
* @author zhongwt
* @copyright (c) 2009-9-20
*/
abstract class Helper_Abstract
{
	/**
	* @var ZOL_Product_Caching_GetCacheLoader
	*/
	protected static $cache;	
	
	
	/**
	* 加载缓存数据
	*/
   public static function loadCache($moduleName, $param = array(), $num = 0)
   {
		if(!self::$cache) self::$cache = ZOL_DAL_RefreshCacheLoader::getInstance();
        
		$data = self::$cache->loadCacheObject($moduleName, $param);
		
		if ($num && $data && count($data) > $num) {
			$data = array_slice($data, 0, $num, true);
		}
		
		return $data;
	}
    
    /**
     * 生成kv 对应数据
     */ 
    public  static  function  getKv($data,$key,$main = false){  
            if(empty($data)){
                return  false;
            }
            $outData = array();
            foreach ($data  as $value){
                  if($main){
                      $outData[$value[$key]] = $value[$main];
                  }else{
                      $outData[$value[$key]] = $value;
                  }
            }
            return $outData;
    }
    
    /**
     * curl 抓取
     */
    public static function httpsGet($url)
    {
        $curl = curl_init ();
        curl_setopt	($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt	($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt ($curl, CURLOPT_URL, $url );
        curl_setopt ($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt	($curl, CURLOPT_CONNECTTIMEOUT, 0 );//最长等待连接服务器时间10秒
        curl_setopt	($curl, CURLOPT_TIMEOUT, 30 );//服务器请求返回最长时间不超过10秒
        curl_setopt ($curl, CURLOPT_HEADER, false );//输出不包含头信息
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1 );//如果成功只将结果返回，不自动输出任何内容
        $result = curl_exec ( $curl );//执行CURL发出请求
        // 		print_r($result);exit;
        curl_close ( $curl );//关闭CURL连接
        return $result; //返回结果
    }

}
