<?php

class Db_DocumentWrite extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_article',
			'database' => 'article_new',
		 ),
		 'slave' => array(
			'host' => 'dbserver_article',
			'database' => 'article_new',
		 ),
	);    	
}
