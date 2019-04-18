<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 
*/
class DB_Shenmobbs extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_jdbbs_read',
			'database' => 'z_shenmobbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_jdbbs_read',
			'database' => 'z_shenmobbs',
		 ),
	);    	
}