<?php

class Db_Dcbbs extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_dcbbs_read',
			'database' => 'z_dcbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_dcbbs_read',
			'database' => 'z_dcbbs',
		 ),
	);    	
}
