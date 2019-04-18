<?php

class Db_OtherbbsV2 extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_otherbbs_v2',
			'database' => 'z_otherbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_otherbbs_v2_read',
			'database' => 'z_otherbbs',
		 ),
	);
}