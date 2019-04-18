<?php

class Db_Document extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_article_read1',
			'database' => 'article_new',
		 ),
		 'slave' => array(
			'host' => 'dbserver_article_read1',
			'database' => 'article_new',
		 ),        
	);    	
}
