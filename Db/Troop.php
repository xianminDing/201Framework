<?php
/**
* 论坛数据库
* @author aliang <liu.hongliang@zol.com.cn>
* @copyright (c)
*/
class Db_Troop extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_troop',
			'database' => 'troop',
			//'database' => 'z_group',
		 ),
		 'slave' => array(
			'host' => 'dbserver_troop_read',
			'database' => 'troop',
			//'database' => 'z_group',
		 ),
	);
}
