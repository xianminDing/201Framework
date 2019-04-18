<?php
/**
* 团购库链接
* @author wang.tao5@zol.com.cn
* @copyright (c) 2010年10月27日16:55:41
*/
class Db_TuanGou extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_shop_tuan_read',
			'database' => 'zol_shop_tuan',
		 ),
		'slave' => array(
			'host' => 'dbserver_shop_tuan_read',
			'database' => 'zol_shop_tuan',
		 ),
	);
}