<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 
*/
class DB_Gqbbs extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_gqbbs_read',
			'database' => 'z_gqbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_gqbbs_read',
			'database' => 'z_gqbbs',
		 ),
	);    	
}