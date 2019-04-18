<?php
/**
* MemCache缓存键获取类
* 主要功能：检测参数名，处理参数为键名
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/

class ZOL_DAL_MemCacheKey extends ZOL_DAL_FileCacheKey
{
	/**
	* 获取缓存键名
	*/
	public function getCacheKey()
	{
		if ($this->checkCacheParam($this->param)) {
			return $this->makeCacheKey();
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
		$key['moduleName'] = $this->moduleName;
		return md5(http_build_query($key));
	}
}