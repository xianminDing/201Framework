<?php
/**
* 
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-8-7
*/
class Db_Discussion extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_forums_read1',
			'database' => 'forums',
			//'username' => '',
			//'password' => '',
		 ),
		 'slave' => array(
			'host' => 'dbserver_forums_read1',
			'database' => 'forums',
		 ),
	);    	
}
