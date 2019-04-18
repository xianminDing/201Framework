<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 2009-08-28
*/
class Db_MallGoods extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_mall_goods_read',
			'database' => 'mall_goods',
		 ),
		 'slave' => array(
			'host' => 'dbserver_mall_goods_read',
			'database' => 'mall_goods',
		 ),
	);    	
}
