<?php

class DB_Gigabyte extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_gigabyte_read',
			'database' => 'z_gigabyte',
		 ),
		 'slave' => array(
			'host' => 'dbserver_gigabyte_read',
			'database' => 'z_gigabyte',
		 ),        

	);    	
}
