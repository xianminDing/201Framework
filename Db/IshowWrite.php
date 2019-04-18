<?php

class Db_IshowWrite extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_ishow',
			'database' => 'ishow',
		 ),
		 'slave' => array(
			'host' => 'dbserver_ishow',
			'database' => 'ishow',
		 ),
	);
}
