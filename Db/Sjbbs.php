<?php
/**
* 手机论坛
* @author aliang <liu.hongliang@zol.com.cn>
* @copyright (c)
*/
class Db_Sjbbs extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_sjbbs_read',
			'database' => 'z_sjbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_sjbbs_read',
			'database' => 'z_sjbbs',
		 ),
	);    	
}
