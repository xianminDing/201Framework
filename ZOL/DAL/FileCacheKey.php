<?php
/**
* 测试用缓存键获取类
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/

class ZOL_DAL_FileCacheKey implements ZOL_DAL_ICacheKey
{
	protected static $_keyNames = array();
	
	/**
	* 缓存键类
	* 
	* @var ZOL_DAL_IKeyNames
	*/
	protected static $_keyNamesObj;
	protected $param = array();
	protected $moduleName;
	protected $_cacheSaveType;
	
	public function __construct($moduleName = '', array $param = array())
	{
		$this->setModuleName($moduleName);
		$this->getKeyNameObj($moduleName);
		$this->setKeyNames($moduleName);
		$this->setParam($param);
	}
	
	private function getKeyNameObj($moduleName)
	{
		if (self::$_keyNamesObj === null) {
			$config = ZOL_DAL_Config::getConfig($moduleName);
			self::$_keyNamesObj = new $config['CACHE_KEYNAMES']();
		}
		return self::$_keyNamesObj;
	}
	
	public function setModuleName($moduleName)
	{
		$modulePrefix = ZOL_DAL_Config::CACHE_MODULES_NAMESPACE;
		if (strpos($moduleName, $modulePrefix) === 0) {
			$moduleName = substr($moduleName, strlen($modulePrefix));
		}
		$this->moduleName = $moduleName;
	}
	
	public function setCacheSaveType($cacheSaveType)
	{
		$this->_cacheSaveType = $cacheSaveType;
	}
	
	/**
	* 获取缓存键名
	*/
	public function getCacheKey()
	{
		if ($this->checkCacheParam($this->param)) {
			$key = $this->makeCacheKey();
			
			return $this->makeCachePath($key);
		} else {
			return false;
		}
	}
	
	/**
	* 返回参数数组
	*/
	public function getCacheParam()
	{
		return $this->param;
	}
	
	/**
	* 设置参数名
	* @return void
	*/
	public function setKeyNames($moduleName)
	{
		self::$_keyNames = self::$_keyNamesObj->getKeyNames();
	}
	
	public function setParam(array $param)
	{
		$param = (array)$param;
		
		if ($this->checkCacheParam($param)) {
			$this->param = $param;
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* 检查参数是否是指定的参数集中的
	*/
	protected function checkCacheParam(array $param = array())
	{
		if (empty($param) && $this->param) {
			$param = $this->param;
		}
		
		//为空就不检查
		if (empty($param)) {
			return true;
		}
		
		//var_dump($param);exit;
		$paramKey = $param ? array_keys($param) : array();
		unset($param);
		if (array_intersect($paramKey, array_keys(self::$_keyNames)) == $paramKey) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* 重组键值顺序，并返回KEY
	* @return string
	*/
	protected function makeCacheKey()
	{
		$key = array();
		foreach (self::$_keyNames as $name => $type) {
			if (!isset($this->param[$name])) {
				continue;
			}
			$key[$name] = $type($this->param[$name]);
		}
		return md5(http_build_query($key));
	}
	
	/**
	* 根据KEY制造缓存路径
	* @param string $key
	*/
	protected function makeCachePath($key)
	{
		if (empty($key)) {
			throw new Exception('The key is empty!');
		}
		
		$cacheDir = defined('DAL_CACHE_DIR')
				  ? DAL_CACHE_DIR
				  : ZOL_DAL_Config::CACHE_DIR;

		$path = $cacheDir . $this->moduleName .
				'/' . $this->getCacheFileSubPath($key);
		
		return $path;
	}
	
	protected function getCacheFileSubPath($key)
	{
		switch($this->_cacheSaveType) {
			case 'SERIALIZE':
				$len = 4;
				$fileName = substr($key, 4) . '.zcache';
				break;
			case 'PHP':
			default:
				$len = 6;
				$fileName = $key . '.php';
		}
		return chunk_split(substr($key, 0, $len), 2, '/') . $fileName;
	}
}