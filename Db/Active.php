<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 2009-08-24
*/
class Db_Active extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_active',
			'database' => 'active',
		 ),
		 'slave' => array(
			'host' => 'dbserver_active',
			'database' => 'active',
		 ),
	);    	
}
