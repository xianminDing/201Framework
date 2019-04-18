<?php
/**
* 技术论坛数据库
* @author aliang <liu.hongliang@zol.com.cn>
* @copyright (c)
*/
class Db_Techbbs extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_techbbs_read',
			'database' => 'z_techbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_techbbs_read',
			'database' => 'z_techbbs',
		 ),
	);
}
