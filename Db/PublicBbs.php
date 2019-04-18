<?php

class Db_PublicBbs extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_public_v2',
			'database' => 'z_public',
		 ),
		 'slave' => array(
			'host' => 'dbserver_public_v2_read',
			'database' => 'z_public',
		 ),
	);
}