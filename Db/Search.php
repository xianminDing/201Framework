<?php
/**
* 搜索连接
* @author wanghb <wang.haobin@zol.com.cn>
* @copyright (c)
*/
class Db_Search extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_search_log_read',
			'database' => 'search_log',
		 ),
		 'slave' => array(
			'host' => 'dbserver_search_log_read',
			'database' => 'search_log',
		 ),
        'charset' => 'GBK',
	);
}
