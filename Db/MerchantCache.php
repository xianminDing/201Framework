<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 2009-08-30
*/
class Db_MerchantCache extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_procache_read',
			'database' => 'dealer_cache',
		 ),
		 'slave' => array(
			'host' => 'dbserver_procache_read',
			'database' => 'dealer_cache',
		 ),
	);    	
}
