<?php
/**
* AUTO的基类函数
* @author 仲伟涛 <zhong.weitao@zol.com.cn>
* @copyright (c) 2011-06-22
* @version v1.0
*/

abstract class Auto_Abstract extends ZOL_Abstract_Page
{
	/**
	* @var ZOL_DAL_RefreshCacheLoader
	*/
	protected static $_cache;

	/**
	* 初始化缓存更新器
	*/
	protected static function init()
	{
		self::$_cache = ZOL_DAL_RefreshCacheLoader::getInstance();
	}

	/**
	* 加载缓存
	* @return array DAL data
	*/
	protected static function loadCache($moduleName, $param = array(), $num = 0)
	{
		self::init();
		$data = self::$_cache->loadCacheObject($moduleName, $param);

		if ($num && $data && count($data) > $num) {
			$data = array_slice($data, 0, $num, true);
		}

		return $data;
	}

	protected static function setCache($moduleName, $param)
	{
		self::init();
		return self::$_cache->refreshCacheObject($moduleName, $param);
	}
    
    protected static function rmCache($moduleName, $param){
        self::init();
		return self::$_cache->removeCacheObject($moduleName, $param);
    }

    /**
	 * 输出信息
	 * @param string $msg 信息
	 * @param boolean $halt 是否中断
	 */
	protected static function output($msg, $halt = true)
	{
		echo $msg . "\r\n";
		$halt && die();
	}
}
