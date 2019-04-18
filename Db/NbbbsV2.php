<?php

class Db_NbbbsV2 extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_nbbbs_v2',
			'database' => 'z_nbbbs'
		 ),
		 'slave' => array(
			'host' => 'dbserver_nbbbs_v2_read',
			'database' => 'z_nbbbs',
		 ),
	);
}