<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 2009-08-24
*/
class Db_AskBook extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_askbook_read',
			'database' => 'askbook',
		 ),
		 'slave' => array(
			'host' => 'dbserver_askbook_read',
			'database' => 'askbook',
		 ),
	);    	
}
