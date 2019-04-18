<?php
/**
* 缓存管理接口
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/

interface ZOL_DAL_ICacheManager
{
	/**
	* 获取缓存模块对象
	*/
	public function getCacheModuleObj($moduleName);
	
	/**
	* 获取缓存数据对象
	*/
	public function getCacheObject($moduleName, $cacheParam = null, $num = 0);
	
	/**
	* 刷新缓存对象
	*/
	public function refreshCacheObject($moduleName, $param = null);
	
	/**
	* 清除缓存对象
	*/
	public function removeCacheObject($moduleName, $cacheParam = null);
}