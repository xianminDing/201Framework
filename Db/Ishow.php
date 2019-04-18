<?php

class Db_Ishow extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_ishow',
			'database' => 'ishow',
		 ),
		 'slave' => array(
			'host' => 'dbserver_ishow_read',
			'database' => 'ishow',
		 ),
	);
}
