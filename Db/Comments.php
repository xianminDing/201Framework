<?php

class Db_Comments extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_comments',
			'database' => 'comments',
		 ),
		 'slave' => array(
			'host' => 'dbserver_comments_read',
			'database' => 'comments',
		 ),
	);    	
}
