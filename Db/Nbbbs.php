<?php

class Db_Nbbbs extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_nbbbs_read',
			'database' => 'z_nbbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_nbbbs_read',
			'database' => 'z_nbbbs',
		 ),        

	);    	
}
