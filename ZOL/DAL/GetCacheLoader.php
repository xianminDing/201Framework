<?php
/**
* 
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/
class ZOL_DAL_GetCacheLoader extends ZOL_DAL_CacheLoader
{
	/**
	* @var ZOL_DAL_ICacheManager
	*/
	private static $_manager;
	private static $_loadedCacheModules = array();
	
	public function __construct()
	{
		self::$_manager = ZOL_DAL_BaseCacheManager::getInstance();
	}
	
	/**
	* 加载缓存对象
	* @param string $moduleName                               缓存模块名称
	* @param string|ZOL_DAL_ICacheKey $cacheParam 缓存键值对，或采用的键值算法。如果只是键值对，
	*                                                         系统会自动采用默认算法引擎
	* @param integer                                          调用数量
	*/
	public function loadCacheObject($moduleName, $cacheParam = null, $num = 0)
	{
		$data = self::$_manager->getCacheObject($moduleName, $cacheParam, $num);
		if (empty(self::$_loadedCacheModules[$moduleName])) {
			self::$_loadedCacheModules[$moduleName]['count'] = 0;
		}
		self::$_loadedCacheModules[$moduleName]['count'] ++;
		self::$_loadedCacheModules[$moduleName]['param']  = $cacheParam;
		self::$_loadedCacheModules[$moduleName]['key'] = self::$_manager->getCacheKey();
		return $data;
	}
	
	
	/**
	* 获取页面已加载模块信息
	*/
	public function getLoadedCacheModules()
	{
		return self::$_loadedCacheModules;
	}
}
