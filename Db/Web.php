<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 
*/
class DB_Web extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_webgamebbs_read',
			'database' => 'z_webgamebbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_webgamebbs_read',
			'database' => 'z_webgamebbs',
		 ),
	);    	
}