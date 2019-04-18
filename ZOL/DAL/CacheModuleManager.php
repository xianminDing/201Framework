<?php
/**
* 缓存模块管理
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/
class ZOL_DAL_CacheModuleManager
{
	private static $instance;
	private $cacheModules;
	private static $cacheModulesListFile;
	
	public function __construct()
	{
		$this->_loadCacheModules();
	}
	
	/**
	* 单例模式
	* @return ZOL_DAL_CacheModules
	*/
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new ZOL_DAL_CacheModuleManager();
		}
		return self::$instance;
	}
	
	/**
	* 返回模块列表数组
	*/
	public function getCacheModules()
	{
		return $this->cacheModules;
	}
	
	/**
	* 加载模块列表缓存
	*/
	private function _loadCacheModules()
	{
		$dalDir = defined('DAL_DIR')
				  ? DAL_DIR
				  : dirname(__FILE__);
		self::$cacheModulesListFile = $dalDir . '/modules.lst';
		if (file_exists(self::$cacheModulesListFile)) {
			$this->cacheModules = unserialize(file_get_contents(self::$cacheModulesListFile));
		} else {
			$this->cacheModules = array();
			//throw new ZOL_Exception('Cache Modules List File "' . self::$cacheModulesListFile . '" does not exist!');
		}
	}
	
	/**
	* 保存模块
	*/
	private function saveCacheModules()
	{
		if (!$this->cacheModules) {
			throw new ZOL_Exception('The cacheModules is empty!');
		}
		$content = serialize($this->cacheModules);
		return ZOL_File::write($content, self::$cacheModulesListFile);
	}
	
	/**
	* 检查模块是否存在
	* @param string $moduleName 模块名称
	* @return boolean
	*/
	public function checkModuleExist($moduleName)
	{
		if (array_key_exists($moduleName, $this->cacheModules)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* 注册模块
	* @param string $moduleName 模块类名
	* @param boolean $rewrite 是否覆盖已经注册过的相同模块
	*/
	public function registerModule($moduleName, $rewrite = false)
	{
		if (!$moduleName) {
			throw new ZOL_Exception('Modulename can not for empty!');
		}
		
		if (strpos($moduleName, ZOL_DAL_Config::CACHE_MODULES_NAMESPACE) !== 0) {
			$moduleName = ZOL_DAL_Config::CACHE_MODULES_NAMESPACE . $moduleName;
		}
		
		if (!class_exists($moduleName)) {
			throw new ZOL_Exception('The moduleCacheClass does not exist!');
		}
		
		if ($this->checkModuleExist($moduleName) && !$rewrite) {
			throw new ZOL_Exception('This Module "' . $moduleName . '" has existed!');
		}

		$module = new $moduleName();
		
		if (!($module instanceof ZOL_DAL_ICacheModule)) {
			throw new ZOL_Exception('This Module "' . $moduleName . '" must be an instance of ZOL_DAL_CacheModule!');
		}
		
		$moduleInfo = array(
			'name'   => $moduleName,//模块名
			'depend' => $module->getDepend(),//模块依赖
			'expire' => $module->getExpire(),//缓存周期
		);

		$this->cacheModules[$moduleName] = $moduleInfo;
		
		return $this->saveCacheModules();
	}
	
	/**
	* 注册所有模块 暂时还有问题 现在无法根据不同实例注册,只能通过应用入口的配置进行注册
	*/
	public function registerAllModule()
	{
		$cacheModulesDir = defined('CACHE_MODULES_DIR')
						 ? DAL_CACHE_MODULES_DIR
						 : ZOL_DAL_Config::CACHE_MODULES_DIR;
		
		$modulesFiles = glob($cacheModulesDir . '/*.php');
		if ($modulesFiles) {
			unset($this->cacheModules);
			foreach ($modulesFiles as $module) {
				$pathInfo = pathinfo($module);
				$moduleName = substr($pathInfo['basename'], 0, strpos($pathInfo['basename'], '.'));
				$this->registerModule($moduleName, true);
			}
		}
		return true;
	}
	
	/**
	* 删除模块
	*/
	public function removeModule($moduleName)
	{
		if (!$moduleName) {
			throw new ZOL_Exception('Modulename can not for empty!');
		}
		$moduleName = ZOL_DAL_Config::CACHE_MODULES_NAMESPACE . $moduleName;
		
		if (!$this->checkModuleExist($moduleName)) {
			throw new ZOL_Exception('The Modulename does not exist!');
		}
		unset($this->cacheModules[$moduleName]);
		return $this->saveCacheModules();
	}
}
