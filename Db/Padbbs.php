<?php

class Db_Padbbs extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_padbbs_read',
			'database' => 'z_padbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_padbbs_read',
			'database' => 'z_padbbs',
		 ),        

	);    	
}
