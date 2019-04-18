<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 2009-08-28
*/
class Db_MyApp extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_myapp',
			'database' => 'z_myapp',
		 ),
		 'slave' => array(
			'host' => 'dbserver_myapp_read',
			'database' => 'z_myapp',
		 ),
	);    	
}
