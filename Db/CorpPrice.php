<?php
/**
* 分站价格数据库
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c)
*/
class Db_CorpPrice extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_corp_price_read',
			'database' => 'corp_price',
		 ),
		'slave' => array(
			'host' => 'dbserver_corp_price_read',
			'database' => 'corp_price',
		 ),        
	);
}
