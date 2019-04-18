<?php
/**
* 商城log库
* @author chenjt <chen.jingtao@zol.com.cn>
* @copyright (c)
*/
class ZOL_Db_ZolShopLog extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_zolshop_log',
			'database' => 'shop_log',
		 ),
		 'slave' => array(
			'host' => 'dbserver_zolshop_log_read',
			'database' => 'shop_log',
		 ),
	);
}
