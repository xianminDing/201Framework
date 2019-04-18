<?php
/**
* 产品库链接
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c)
*/
class Db_ProductWrite extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_zoldb',
			'database' => 'zoldb',
		 ),
		'slave' => array(
			'host' => 'dbserver_zoldb',
			'database' => 'zoldb',
		 ),        
	);
}
