<?php
/**
* 图片库链接
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-7-16
*/
class Db_Picture extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_picture',
			'database' => 'picture',
		 ),
		'slave' => array(
			'host' => 'dbserver_picture',
			'database' => 'picture',
			
		 ),
	);
}
