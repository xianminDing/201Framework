<?php
/**
* 产品库链接
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c)
*/
class Db_Product extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_zoldb_read1',
			'database' => 'zoldb',
		 ),
		'slave' => array(
			'host' => 'dbserver_zoldb_read1',
			'database' => 'zoldb',
		 ),        
	);
}
