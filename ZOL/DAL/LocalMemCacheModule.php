<?php
/**
* 内存盘缓存模块 继承于文件缓存
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-9-27
*/

abstract class ZOL_DAL_LocalMemCacheModule extends ZOL_DAL_FileCacheModule
{
	/**
	* 初始化处理参数和模块名;
	*/
	public function processParam($cacheParam = array())
	{
		if ($cacheParam === null || $this->_cacheParam === $cacheParam) {
			return $this;
		}
		$moduleName = get_class($this);
		if (!($cacheParam instanceof ZOL_DAL_ICacheKey)) {
			#根据配置文件获取默认KEYMAKER
			$keyMakerName = ZOL_DAL_Config::getKeyMakerName($moduleName, 'LOCALMEM');
			$keyMaker = new $keyMakerName($moduleName, (array)$cacheParam);
		} else {
			$keyMaker = &$cacheParam;
			$keyMaker->setModuleName($moduleName);
		}
		
		#设置缓存存储类型
		$keyMaker->setCacheSaveType($this->_cacheSaveType);
		
		$this->_cacheParam = $keyMaker->getCacheParam();
		$this->_cachePath  = $keyMaker->getCacheKey();
		
		return $this;
	}
}