<?php
/**
* 用户写链接
* @author  wang.tao5@zol.com.cn
* @copyright (c)
*/
class Db_UserWrite extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_BaseUser',
			'database' => 'BaseUser',
		 ),
		 'slave' => array(
			'host' => 'dbserver_BaseUser',
			'database' => 'BaseUser',
		 ),
	);
}