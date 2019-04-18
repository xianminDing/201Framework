<?php

class DB_Lepad extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_lepad_read',
			'database' => 'z_lepad',
		 ),
		 'slave' => array(
			'host' => 'dbserver_lepad_read',
			'database' => 'z_lepad',
		 ),        

	);    	
}