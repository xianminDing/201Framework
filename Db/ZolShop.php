<?php
/**
* 新商城读库
* @author wanghb <wang.haobin@zol.com.cn>
* @copyright (c)
*/
class Db_ZolShop extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_zolshop_goods_read',
			'database' => 'shop_goods',
		 ),
		 'slave' => array(
			'host' => 'dbserver_zolshop_goods_read',
			'database' => 'shop_goods',
		 ),
	);
}
