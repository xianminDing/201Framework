<?php
/** shpgood是连接类
* @author  aliang 
* @copyright (c) 2009-08-28 
*/
class Db_ShopGoods extends ZOL_Abstract_Pdo
{ 
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_zolshop_goods',
			'database' => 'shop_goods',
		 ),
		 'slave' => array(
			'host' => 'dbserver_zolshop_goods_read',
			'database' => 'shop_goods',
		 ),
	);    	
}

