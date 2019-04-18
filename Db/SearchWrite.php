<?php
/**
* 搜索连接
* @author chenjt <chen.jingtao@zol.com.cn>
* @copyright (c)
*/
class Db_SearchWrite extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_search_log',
			'database' => 'search_log',
		 ),
		 'slave' => array(
			'host' => 'dbserver_search_log',
			'database' => 'search_log',
		 ),
	);
}
