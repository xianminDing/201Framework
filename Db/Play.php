<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 
*/
class Db_Play extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_flash_read',
			'database' => 'flash',
		 ),
		 'slave' => array(
			'host' => 'dbserver_flash_read',
			'database' => 'flash',
		 ),
	);    	
}
