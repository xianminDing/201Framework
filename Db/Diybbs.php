<?php

class Db_Diybbs extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_diybbs',
			'database' => 'z_diybbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_diybbs',
			'database' => 'z_diybbs',
		 ),
	);
}
