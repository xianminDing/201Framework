<?php

class Db_DiybbsV2 extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_diybbs_v2',
			'database' => 'z_diybbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_diybbs_v2_read',
			'database' => 'z_diybbs',
		 ),
	);
}