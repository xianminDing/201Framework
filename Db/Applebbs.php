<?php

class DB_Applebbs extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_applebbs_read',
			'database' => 'z_applebbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_applebbs_read',
			'database' => 'z_applebbs',
		 ),        

	);
}