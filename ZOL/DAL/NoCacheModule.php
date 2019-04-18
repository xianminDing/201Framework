<?php
/**
* 没有缓存模块 继承于文件缓存
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2010-02-09
*/

abstract class ZOL_DAL_NoCacheModule extends ZOL_DAL_FileCacheModule
{	
	public function processParam($cacheParam = null)
	{
		if ($cacheParam === null || $this->_cacheParam === $cacheParam) {
			return $this;
		}
		
		#初始化时间
		$this->_startTime = microtime(true);
		
		$moduleName = get_class($this);

		if (!($cacheParam instanceof ZOL_DAL_ICacheKey)) {
			static $keyMaker = null;
			if(!$keyMaker || !($keyMaker instanceof ZOL_DAL_ICacheKey)) {
				#根据配置文件获取默认KEYMAKER
				$keyMakerName = ZOL_DAL_Config::getKeyMakerName($moduleName, 'NO');
				$keyMaker = new $keyMakerName($moduleName, (array)$cacheParam);
			} else {
				$keyMaker->setParam((array)$cacheParam);
			}
			
		} else {
			$keyMaker = &$cacheParam;
			$keyMaker->setModuleName($moduleName);
		}
		
		#设置缓存存储类型
		$keyMaker->setCacheSaveType($this->_cacheSaveType);
		
		$this->_cacheParam = $keyMaker->getCacheParam();
		$this->_cacheKey   = $keyMaker->getCacheKey();
		return $this;
	}
	/**
	* 获取LuceneCache缓存
	* 可被重写
	* @return mixed
	*/
	public function get($cacheParam = null)
	{
		$this->processParam($cacheParam);
		
		#返回缓存数据
		if (isset($this->_cachePool[$this->_cacheKey])) {
			return $this->_cachePool[$this->_cacheKey];
			
		}
		
		$this->refresh((array)$this->_cacheParam);
		$data = $this->_content;
		
		if ($data) {
			$this->_cachePool[$this->_cacheKey] = $data;
		}
		
		return $data;
	}
	
	/**
	* 设置缓存
	*/
	public function set($cacheParam = '', $content = null)
	{
		$this->_content = $content;
	}
	
	/**
	* 删除缓存
	*/
	public function rm($cacheParam = '')
	{
		return ;
	}
	
	/**
	* 文件同步
	*/
	private function fileSyn()
	{
		return ;
	}
}
