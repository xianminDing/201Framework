<?php

class Db_BigData extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_bigdata',
			'database' => 'bigdata',
		 ),
		 'slave' => array(
			'host' => 'dbserver_bigdata',
			'database' => 'bigdata',
		 ),
        'username' =>'pro_admin',
        'password' =>'3c2d4c41',
	);
}


