<?php
/**
* xhprof DB
* @author zhongwt <zhong.weitao@zol.com.cn>
* @copyright (c) 2009-08-24
*/
class Db_Xhprof extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'username' => 'xhuser',
		'password' => 'xhpass',
		'master' => array(
			'host'     => 'dbserver_xhprof',
			'database' => 'xhprof',
		 ),
		 'slave' => array(
			'host'     => 'dbserver_xhprof',
			'database' => 'xhprof',
		 ),
	);    	
}
