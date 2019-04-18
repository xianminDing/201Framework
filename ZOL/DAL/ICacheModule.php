<?php
//ini_set('memory_limit', '-1');

/**
* 
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/
interface ZOL_DAL_ICacheModule
{
	public function get($cacheParam = '');
	public function set($cacheParam = '', $content = '');
	public function rm();
	public function refresh(array $param = null);
}
