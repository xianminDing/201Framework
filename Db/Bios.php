<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 
*/
class DB_Bios extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_bios_read',
			'database' => 'z_bios',
		 ),
		 'slave' => array(
			'host' => 'dbserver_bios_read',
			'database' => 'z_bios',
		 ),
	);    	
}