<?php
/**
* 最超值库
* @author 杨艳飞@zol.com.cn
* @copyright (c) 2015年01月20日
*/
class Db_Best extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_best',
			'database' => 'z_best',
		 ),
		 'slave' => array(
			'host' => 'dbserver_best_read',
			'database' => 'z_best',
		 ),
	);    	
}

