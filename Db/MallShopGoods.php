<?php
/** 
* @author  aliang 
* @copyright (c) 2009-08-28 
*/
class Db_MallShopGoods extends ZOL_Abstract_Pdo
{ 
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_mall_goods_read',
			'database' => 'zol_shop_goods',
		 ),
		 'slave' => array(
			'host' => 'dbserver_mall_goods_read',
			'database' => 'zol_shop_goods',
		 ),
	);    	
}

