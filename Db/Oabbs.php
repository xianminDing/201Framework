<?php

class Db_Oabbs extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_oabbs_read',
			'database' => 'z_oabbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_oabbs_read',
			'database' => 'z_oabbs',
		 ),
	);
}
