<?php
/**
* 
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/
interface ZOL_DAL_ICacheKey
{
	/**
	* 获取缓存键,
	*/
	public function getCacheKey();
	public function getCacheParam();
	public function setKeyNames($moduleName);#设置参数
}
