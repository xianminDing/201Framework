<?php

class DB_Win8bbs extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_win8bbs_read',
			'database' => 'z_win8bbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_win8bbs_read',
			'database' => 'z_win8bbs',
		 ),        

	);
}