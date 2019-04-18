<?php
/**
* 二手链接
* @author wang.haobin@zol.com.cn
* @copyright (c) 2013年07月09日
*/
class Db_Usedmarket extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_usedmarket',
			'database' => 'usedmarket',
		 ),
		'slave' => array(
			'host' => 'dbserver_usedmarket_read',
			'database' => 'usedmarket',
		 ),
	);
}