<?php
/**
* 
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 上传对象
* @version v1.0
*/

interface ZOL_Interface_Upload
{
	/**
	* 获取文件路径信息
	* 
	* @param array $file 文件信息
	* @return array($path, $thumbPath);
	*/
	public function save(array $file);
	public function rm($path);
}
