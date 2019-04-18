<?php
/**
* 新商商家库
* @author wanghb <wang.haobin@zol.com.cn>
* @copyright (c)
*/
class Db_ShopMerchant extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_zolshop_goods_read',
			'database' => 'shop_merchant',
		 ),
		 'slave' => array(
			'host' => 'dbserver_zolshop_goods_read',
			'database' => 'shop_merchant',
		 ),
	);
}
