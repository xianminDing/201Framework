<?php
class Db_Blog extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_blog_read',
			'database' => 'blog',
		 ),
		 'slave' => array(
			'host' => 'dbserver_blog_read',
			'database' => 'blog',
		 ),
	);    	
}
