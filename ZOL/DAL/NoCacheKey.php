<?php
/**
* 
* @author wiki<wu.kun@zol.com.cn>
* @copyright (c) $(date)
* @version v1.0
*/

class ZOL_DAL_NoCacheKey extends ZOL_DAL_FileCacheKey
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