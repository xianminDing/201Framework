<?php

class Db_PadbbsV2 extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_padbbs_v2',
			'database' => 'z_padbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_padbbs_v2_read',
			'database' => 'z_padbbs',
		 ),
	);
}