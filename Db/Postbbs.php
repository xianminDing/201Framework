<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 
*/
class Db_Postbbs extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_postbbs_read',
			'database' => 'z_postbbs',
		 ),
		 'slave' => array(
			'host' => 'dbserver_postbbs_read',
			'database' => 'z_postbbs',
		 ),
	);    	
}