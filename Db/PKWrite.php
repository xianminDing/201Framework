<?php
/**
* PK写库连接类
* @author qianzhiwei <qian.zhiwei@zol.com.cn>
* @copyright (c)
*/
class Db_PKWrite extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_pk',
			'database' => 'z_pk',
		 ),
		 'slave' => array(
			'host' => 'dbserver_pk',
			'database' => 'z_pk',
		 ),
	);
}