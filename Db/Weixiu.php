<?php
/**
* 维修库
* @author wangmc
* @copyright (c)
*/
class Db_Weixiu extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_maintain',
			'database' => 'z_maintain',
		 ),
		'slave' => array(
			'host' => 'dbserver_maintain_read',
			'database' => 'z_maintain',
		 ),        
	);
}
