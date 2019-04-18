<?php

class Db_Lephone extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_diybbs_read',
			'database' => 'z_lephone',
		 ),
		 'slave' => array(
			'host' => 'dbserver_diybbs_read',
			'database' => 'z_lephone',
		 ),        

	);    	
}
