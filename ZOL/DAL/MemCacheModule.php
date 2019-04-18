<?php
/**
* 抽象类
* Memcache缓存类别相关操作
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/

abstract class ZOL_DAL_MemCacheModule extends ZOL_DAL_FileCacheModule
{	
	protected $_cacheParam;
	
	protected $_cacheKey;
	/**
	* 初始化处理参数和模块名;
	*/
	public function processParam($cacheParam = null)
	{
		if ($cacheParam === null || $this->_cacheParam === $cacheParam) {
			return $this;
		}
		
		$moduleName = get_class($this);
		if (!($cacheParam instanceof ZOL_DAL_ICacheKey)) {
			#根据配置文件获取默认KEYMAKER
			$keyMakerName = ZOL_DAL_Config::getKeyMakerName($moduleName, 'MEM');
			$keyMaker = new $keyMakerName($moduleName, (array)$cacheParam);
		} else {
			$keyMaker = &$cacheParam;
			$keyMaker->setModuleName($moduleName);
		}
		
		$this->_cacheParam = $keyMaker->getCacheParam();
		$this->_cacheKey   = $keyMaker->getCacheKey();
		return $this;
	}
	
	/**
	* 获取MemCache缓存
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
		
		$data = ZOL_Caching_Memcache::get($this->_cacheKey);
		if ($data) {
			$this->_cachePool[$this->_cacheKey] = $data;
		} elseif ($this->_autoRefresh) {#自动更新缓存
			if ($this->refresh($this->_cacheParam)) {
				#重新获取缓存内容
				$data = $this->_content;
			}
		}
		
		return $data;
	}
	
	/**
	* 设置MemCache缓存
	*/
	public function set($cacheParam = '', $content = '')
	{
		$this->processParam($cacheParam);
		
		$this->_content = $content ? $content : $this->_content;
		
		if (empty($this->_cacheKey) || empty($this->_content)) {
			return false;
		}
		
		$expire = $this->_isDuly 
				? ($this->_expire - (SYSTEM_TIME % $this->_expire)) 
				: $this->_expire;
		
		return ZOL_Caching_Memcache::set($this->_cacheKey, $this->_content, $expire);
	}
	
	/**
	* 删除MemCache缓存
	*/
	public function rm($cacheParam = '')
	{
		$this->processParam($cacheParam);
		
		if (empty($this->_cacheKey)) {
			return false;
		}
		return ZOL_Caching_Memcache::delete($this->_cacheKey);
	}
}
