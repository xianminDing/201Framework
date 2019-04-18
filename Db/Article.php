<?php
/**
 * dbserver_article_read1
 * @author huanght
 * @copyright (c)
 */
class Db_Article extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_article',
			'database' => 'article_new',
		 ),
		'slave' => array(
			'host' => 'dbserver_article_read1',
			'database' => 'article_new',
		 ),
	);
}
