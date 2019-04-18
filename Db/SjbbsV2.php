<?php
/**
* lw 手机论坛V2
* @copyright (c)
*/
class Db_SjbbsV2 extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_sjbbs_v2_read',
			'database' => 'z_sjbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_sjbbs_v2_read',
			'database' => 'z_sjbbs',
		 ),
	);    	
}
