<?php

class Db_FileSyn extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_file_syn',
			'database' => 'file_syn',
			//'username' => '',
			//'password' => '',
		 ),
		 'slave' => array(
			'host' => 'dbserver_file_syn',
			'database' => 'file_syn',
		 ),
	);    	
}
