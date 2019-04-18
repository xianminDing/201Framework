<?php
/**
* 投票库链接
* @author wang.tao5@zol.com.cn
* @copyright (c) 2009-7-16
*/
class Db_Indagate extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_survey',
			'database' => 'survey',
		 ),
		'slave' => array(
			'host' => 'dbserver_survey',
			'database' => 'survey',

		 ),
	);
}