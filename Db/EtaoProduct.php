<?php

class Db_EtaoProduct extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_tech_article',
			'database' => 'tech_article',
		 ),
		 'slave' => array(
			'host' => 'dbserver_tech_article_read',
			'database' => 'tech_article',
		 ),
	);
}
