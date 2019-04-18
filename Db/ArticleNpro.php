<?php
class Db_ArticleNpro extends ZOL_Abstract_Pdo {
	protected $servers   = array(
		'master' => array(
			'host'     => 'dbserver_article_npro',
			'database' => 'article_npro',
		 ),
		 'slave' => array(
			'host'     => 'dbserver_article_npro_read',
			'database' => 'article_npro',
		 ),
	);    	
}
