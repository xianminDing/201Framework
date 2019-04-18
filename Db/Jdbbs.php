<?php

class Db_Jdbbs extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_jdbbs_read',
			'database' => 'jdbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_jdbbs_read',
			'database' => 'jdbbs',
		 ),
	);    	
}
