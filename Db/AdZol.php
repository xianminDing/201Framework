<?php
/**
 * Name: AdZol.php
 * Author: ³Â¾°ÌÎ<chen.jingtao@zol.com.cn>
 * Date: 2019-04-16
 */
class Db_AdZol extends ZOL_Abstract_Pdo {
	
	protected $servers = array(
		'charset' => 'utf8',
		'master' => array(
			'host' => 'dbserver_adsub',
			'database' => 'zol_bms',
		),
		'slave' => array(
			'host' => 'dbserver_adsub',
			'database' => 'zol_bms',
		
		),
	);
	
}