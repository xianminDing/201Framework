<?php
/**
* 团购图片库
* @author wang.haobin@zol.com.cn
* @copyright (c) 2013年07月09日
*/
class Db_TuanPic extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_shop_pic_read',
			'database' => 'zol_shop_picture',
		 ),
		 'slave' => array(
			'host' => 'dbserver_shop_pic_read',
			'database' => 'zol_shop_picture',
		 ),
	);    	
}

