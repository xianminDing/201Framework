<?php
/**
* 抽象类
* 文件缓存类别相关操作
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/

abstract class ZOL_DAL_FileCacheModule implements ZOL_DAL_ICacheModule
{
	/**
	* 缓存大小 条数
	*/
	const CACHE_SIZE = 1000;
	
	/**
	* 缓存周期
	*/
	protected $_expire = 3600;
	
	/**
	* 是否正点更新
	* 
	* @var boolean
	*/
	protected $_isDuly = false;
	
	/**
	* 自动更新缓存
	*/
	protected $_autoRefresh = false;

    /**
     * 当前的缓存模块名
     * @var string 
     */
    protected $_moduleName;

	/**
	* 调用参数
	*/
	protected $_cacheParam;
	
	/**
	* 保存路径
	*/
	protected $_cachePath;
	
	/**
	* 缓存数据，防止二次加载
	*/
	protected $_cachePool = array();
	
	/**
	* KEY生成器名
	*/
	protected $_keyMakerName;
	
	/**
	* 调用的模块名，用于统计页面模块调用
	*/
	protected $_moduleNames = array();
	
	/**
	* 用于保存的内容
	*/
	protected $_content;
	
	protected $_startTime = 0;
	
	protected $_endTime   = 0;
	
	/**
	* 缓存依赖
	*/
	protected $_depend = array();
	
	protected $_cacheSaveType;
	
	public function __construct($cacheParam = null)
	{
		if ($cacheParam !== null) {
			$this->processParam($cacheParam);
		}
		
		$this->_cacheSaveType = defined('DAL_CACHE_SAVE_TYPE') 
				? DAL_CACHE_SAVE_TYPE 
				: ZOL_DAL_Config::DAL_CACHE_SAVE_TYPE;
	}
	
	/**
	* 初始化处理参数和模块名;
	*/
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
				$keyMakerName = ZOL_DAL_Config::getKeyMakerName($moduleName, 'FILE');
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

        $this->_moduleName = $moduleName;
		$this->_cacheParam = $keyMaker->getCacheParam();
		$this->_cachePath  = $keyMaker->getCacheKey();
		
		return $this;
	}
	
	/**
	* 获取文件缓存
	* 可被重写
	* @return mixed|false
	*/
	public function get($cacheParam = null)
	{
		$this->processParam($cacheParam);
//		var_dump($this->_cachePath);
//		var_dump($this->_cacheParam);
//		exit;
		#返回缓存数据
		if (isset($this->_cachePool[$this->_cachePath])) {
			return $this->_cachePool[$this->_cachePath];
		}
		
		//更新
		if ($this->_autoRefresh && $this->isExpire()) {
			$this->refresh($this->_cacheParam);
		}
		
		if (file_exists($this->_cachePath)) {
			$data = false;
			switch ($this->_cacheSaveType) {
				case 'SERIALIZE':#读取缓存内容解压后反序列化
					$str = file_get_contents($this->_cachePath);
					if ($str) {
						$data = unserialize(gzinflate($str));
					}
					break;
				case 'PHP':
				default:
					$data = include($this->_cachePath);
			}
			
			#释放内存
			if ($this->_cachePool && count($this->_cachePool) > self::CACHE_SIZE) {
				unset($this->_cachePool);
			}
			
			#缓存数据
			$this->_cachePool[$this->_cachePath] = $data;
			return $data;
		} else {
			return false;
		}
	}
	
	/**
	* 设置文件缓存
	* @param array $cacheParam
	* @param mixed $content 内容
	* @param boolen $fileSyn 是否同步
	*/
	public function set($cacheParam = null, $content = null, $fileSyn = false)
	{
		$this->processParam($cacheParam);
		
//		var_dump($cacheParam);
//		var_dump($this->_cacheParam);
		$this->_content = isset($content) ? $content : $this->_content;
		
		if (empty($this->_cachePath)) {
			return false;
		}
		
		//删除当前文件
//		if (ZOL_File::exists($this->_cachePath) && empty($this->_content)) {
//			$this->rm();
//			return false;
//		} elseif (empty($this->_content)) {
//			return false;
//		}
		
		if (is_object($this->_content)) {
			$this->_content = (array)$this->_content;
		}
		
		if (is_array($this->_content)) {
			#过滤空值
			$this->_content = self::arrayFilter($this->_content);
		}
		//var_dump($this->_content);
		#转换数据，以便保存
		$this->_convData($this->_content);
		
		$this->_endTime = microtime(true);
		
		#var_dump($this->_cachePath);
		
		
		if (ZOL_File::exists($this->_cachePath) || !empty($this->_content)) {
			//$sourceMd5 = md5($content);
			//$desMd5    = md5(ZOL_File::get($this->_cachePath));
			//if($sourceMd5 != $desMd5){#判断md5是否相同~~
				if($fileSyn){
					$this->fileSyn();
				}
			//}
			
//			var_dump($this->_content, $this->_cachePath);
			ZOL_File::write($this->_content, $this->_cachePath);
			unset($content, $this->_content);
			return true;
		}
		return false;
	}
	
	/**
	* 转换数据
	*/
	private function _convData(&$content)
	{
		if (!$content) {
			return false;
		}
		switch ($this->_cacheSaveType) {
			case 'SERIALIZE':
				$content = gzdeflate(serialize($content), 9);
				break;
			case 'PHP':
			default:
				$content = '<?php return ' . self::compressData(var_export($content, true)) . ';';
		}
	}
	
	/**
	* 文件同步
	*/
	private function fileSyn()
	{
		$path = $this->_cachePath;
		//同步写库..........
		//Libs_GlobalFunc::putSynFile($path);
		return true;
	}
	
	/**
	* 删除文件缓存
	*/
	public function rm($cacheParam = null)
	{
		$this->processParam($cacheParam);
		
		if (empty($this->_cachePath) || !file_exists($this->_cachePath)) {
			return false;
		}
		return ZOL_File::rm($this->_cachePath);
	}
	
	/**
	* 是否过期
	* @return boolean true|false 过期|没过期
	*/
	public function isExpire($cacheParam = null)
	{
		$this->processParam($cacheParam);
		
		if (empty($this->_cachePath) || !is_file($this->_cachePath)) {
			return true;
		}
		
		$expire = $this->_isDuly 
				? ($this->_expire - (SYSTEM_TIME % $this->_expire)) 
				: $this->_expire;
		
		return filemtime($this->_cachePath) + $expire < time();
	}
	
	public function getDepend()
	{
		return $this->_depend;
	}
	
	public function getExpire()
	{
		return $this->_expire;
	}
    /**
     * 设置自动更新标志
     * @param type $auto 
     */
	public function setAutoRefresh($auto=0)
    {
        $this->_autoRefresh = $auto;
    }
	/**
	* 获取缓存KEY
	*/
	public function getCacheKey()
	{
		return $this->_cachePath;
	}
	
	/**
	* 获取发布时间
	*/
	public function getRefreshTime()
	{
		return $this->_endTime - $this->_startTime;
	}
	
	/**
	* 压缩数据 主要是除去多余空格和换行，减少字符串大小
	*/
	private static function compressData($data)
	{
		$data = str_replace(array("\r", "\n"), array('', ''), $data);//去除换行
		$data = str_replace(' => ', '=>', $data);//去除数组键值符空格
		$data = preg_replace("/( ){2,}/", ' ', $data);//连续空格替换成单一空格
		return $data;
	}
	
	/**
	* 递归过滤数组值
	* 
	* @param mixed $array 传入的数组
	* @param mixed $callback 回调函数
	* @return array
	*/
	public static function arrayFilter(array $array, $callback = null)
	{
		foreach ($array as &$value) {
			if (is_array($value)) {
				$value = self::arrayFilter($value, $callback);
			}
		}
		return array_filter($array);
	}
}
