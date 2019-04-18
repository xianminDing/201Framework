<?php
/**
* 点评输出及晒单回导, 小灰灰说这个数据库木有读写分离
* @author wiki <he.weijun@zol.com.cn>
* @copyright (c)
*/
class Db_Userlog extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_userlog',
			'database' => 'userlog',
		 ),
		'slave' => array(
			'host' => 'dbserver_userlog',
			'database' => 'userlog',
		 ),        
	);
}
