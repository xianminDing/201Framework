<?php
/**  
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 2009-08-30
*/
class Db_ProductCache extends ZOL_Abstract_Pdo 
{
    protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_procache',
			'database' => 'pro_cache',
		 ),
		 'slave' => array(
			'host' => 'dbserver_procache_read',
			'database' => 'pro_cache',
		 ),
	);    	
}
