<?php
/**
* 软件数据库
* @author aliang <liu.hongliang@zol.com.cn>
* @copyright (c)
*/
class Db_Soft extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_soft_read',
			'database' => 'soft',
		 ),
		 'slave' => array(
			'host' => 'dbserver_soft_read',
			'database' => 'soft',
		 ),
	);    	
}
