<?php
//set_time_limit(0);
/**
* 后台生成缓存模块加载器
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/
class ZOL_DAL_RefreshCacheLoader extends ZOL_DAL_CacheLoader
{
	/**
	* @var ZOL_DAL_ICacheManager
	*/
	private static $manager;
	private static $cacheKeyObj;
	public function __construct()
	{
		self::$manager = ZOL_DAL_BaseCacheManager::getInstance();
	}
	
	/**
	* 获取缓存模块对象
	* @return ZOL_DAL_ICacheModule
	*/
	public function getCacheModuleObj($moduleName)
	{
		return self::$manager->getCacheModuleObj($moduleName);
	}
	
	/**
	* 加载缓存对象
	* @param string $moduleName                               缓存模块名称
	* @param string|ZOL_DAL_ICacheKey $cacheParam 缓存键值对，或采用的键值算法。如果只是键值对，
	*                                                         系统会自动采用默认算法引擎
	*/
	public function loadCacheObject($moduleName, $cacheParam = array(), $num = 0)
	{
		return self::$manager->getCacheObject($moduleName, $cacheParam);
	}
	
	public function refreshCacheObject($moduleName, $param = array())
	{
		return self::$manager->refreshCacheObject($moduleName, $param);
	}
	
	public function removeCacheObject($moduleName, $cacheParam = array())
	{
		return self::$manager->removeCacheObject($moduleName, $cacheParam);
	}
}
