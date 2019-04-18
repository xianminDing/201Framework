<?php
/**
* DAL数据实体管理
* @author wiki<wu.kun@zol.com.cn>
* @copyright (c) 2010年2月2日星期二
* @version v1.0
*/

class ZOL_DAL_EntityManager
{
	/**
	* 实例数组
	* 
	* @var array
	*/
	private static $_entities;
	
	private static $_entitiesListFile;
	
	private static $_moduleNameSpaceLen;
	
	public function __construct()
	{
		self::$_entitiesListFile = '';
		self::$_moduleNameSpaceLen = strlen(ZOL_DAL_Config::CACHE_MODULES_NAMESPACE);
		$this->_loadEntities();
	}
	
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function getEntities()
	{
		return self::$_entities;
	}
	
	public function register()
	{
		
	}
	
	public function remove()
	{
		
	}
	
	public function save()
	{
		if (!self::$_entities) {
			throw new ZOL_Exception('the entities is empty!');
		}
		$content = '<?php return ' . var_export(SELF::$_entities, true) . '?>';
		return ZOL_File::write($content, self::$_entitiesListFile);
	}
	
	/**
	* 检查并获取实例名
	* @param string 模块全名
	*/
	public function checkEntityName($moduleName)
	{
		$prefix = ZOL_DAL_Config::CACHE_MODULES_NAMESPACE;
		
		if (substr_count($moduleName, '_') - substr_count($prefix, '_') < 1) {
			return false;
		}
		
		$moduleToken = explode('_', substr($moduleName, self::$_moduleNameSpaceLen));
		
		if (array_key_exists($moduleToken[0], self::$_entities)) {
			return $moduleToken;
		}
		
		return false;
	}
	
	private function _loadEntities()
	{
		self::$_entities = array();
		if (file_exists(self::$_entitiesListFile)) {
			self::$_entities = include(self::$_entitiesListFile);
		}
	}
}

