<?php
/**
* 产品试用
* @author dongkang
* @copyright (c) 2015-06-02
*/
class Db_Try extends ZOL_Abstract_Pdo
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

