<?php
/**
* 图片库写链接
* @author aliang <liu.hongliang@zol.com.cn>
* @copyright (c) 2010-03-24
*/
class Db_PictureWrite extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_picture_write',
			'database' => 'picture',
		 ),
		'slave' => array(
			'host' => 'dbserver_picture_write',
			'database' => 'picture',
		 ),
	);
}
