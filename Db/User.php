<?php
/**
* 用户连接类
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c)
*/

class Db_User extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_BaseUser',
			'database' => 'BaseUser',
			//'username' => 'service',
			//'password' => '091aff65',
		 ),
		 'slave' => array(
			'host' => 'dbserver_BaseUser_read',
			'database' => 'BaseUser',
		 ),
	);
}
