<?php
/**
* 论坛数据库
* @author aliang <liu.hongliang@zol.com.cn>
* @copyright (c)
*/
class Db_Test extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'username' => 'root',
		'password' => '',
		'master' => array(
			'host' => '10.15.184.169',
			'database' => 'test',
		 ),
		 'slave' => array(
			'host' => '10.15.184.169',
			'database' => 'test',
		 ),
	);
}
