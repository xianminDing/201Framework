<?php

class Db_Gpsbbs extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_gpsbbs_read',
			'database' => 'z_gpsbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_gpsbbs_read',
			'database' => 'z_gpsbbs',
		 ),
	);    	
}
