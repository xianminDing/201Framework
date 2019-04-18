<?php
class Db_Star extends ZOL_Abstract_Pdo{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_star',
			'database' => 'z_star',
		 ),
		 'slave' => array(
			'host' => 'dbserver_star_read',
			'database' => 'z_star',
		 ),
	);    	
}
