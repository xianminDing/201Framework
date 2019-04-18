<?php
/**
* 
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/
abstract class ZOL_DAL_CacheLoader
{
	/**
	* @var ZOL_DAL_CacheLoader
	*/
	static protected $instance;
	
	abstract public function loadCacheObject($moduleName, $cacheParam = null, $num = 0);
	
	public static function getInstance()
	{
		static $instance;
		if ($instance == null) {
			$childClass = get_called_class();
			$instance = new $childClass;
		}
		return $instance;
	}
}
