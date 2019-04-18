<?php
/**
* 产品库链接
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c)
*/
class Db_ProductStat extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_stat_mcounter',
			'database' => 'stat_product',
		 ),
		'slave' => array(
			'host' => 'dbserver_stat_mcounter',
			'database' => 'stat_product',
		 ),        
	);
}
