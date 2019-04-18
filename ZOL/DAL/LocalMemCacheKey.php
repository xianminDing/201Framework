<?php
/**
* 本地内存缓存KEY生成器 继承于 ZOL_DAL_FileCacheKey
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-9-27
*/

class ZOL_DAL_LocalMemCacheKey extends ZOL_DAL_FileCacheKey
{
	/**
	* 根据KEY制造缓存路径
	* @param string $key
	*/
	protected function makeCachePath($key)
	{
		if (empty($key)) {
			throw new Exception('The key is empty!');
		}
		
		$cacheDir = defined('DAL_LOCALMEM_CACHE_DIR')
				  ? DAL_LOCALMEM_CACHE_DIR 
				  : ZOL_DAL_Config::LOCALMEM_CACHE_DIR;
		
		$path = $cacheDir . $this->moduleName .
				'/' . $this->getCacheFileSubPath($key);
		return $path;
	}
}
