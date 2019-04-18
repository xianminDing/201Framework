<?php
/**
* PK读库连接类
* @author qianzhiwei <qian.zhiwei@zol.com.cn>
* @copyright (c)
*/
class Db_PK extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_pk_read',
			'database' => 'z_pk',
		 ),
		 'slave' => array(
			'host' => 'dbserver_pk_read',
			'database' => 'z_pk',
		 ),
	);
}
