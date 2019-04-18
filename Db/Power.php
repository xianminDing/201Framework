<?php
/** 
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-10-26
*/
class Db_Power extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_power_read',
			'database' => 'power',
		 ),
		 'slave' => array(
			'host' => 'dbserver_power_read',
			'database' => 'power',
		 ),
	);    	
}
