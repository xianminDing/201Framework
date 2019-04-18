<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 
*/
class DB_Lolbbs extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_lolbbs_read',
			'database' => 'z_lolbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_lolbbs_read',
			'database' => 'z_lolbbs',
		 ),
	);    	
}