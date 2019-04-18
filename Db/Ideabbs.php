<?php

class Db_Ideabbs extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_idea_read',
			'database' => 'z_idea',
		 ),
		 'slave' => array(
			'host' => 'dbserver_idea_read',
			'database' => 'z_idea',
		 ),
	);
}
