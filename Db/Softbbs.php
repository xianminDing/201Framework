<?php

class DB_Softbbs extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_softbbs_read',
			'database' => 'z_softbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_softbbs_read',
			'database' => 'z_softbbs',
		 ),        

	);
}