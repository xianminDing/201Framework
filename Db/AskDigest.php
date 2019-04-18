<?php
/**
* @author wanghb <wang.haobin@zol.com.cn>
* @copyright (c) 2011-06-07
* 问答堂精编
*/
class Db_AskDigest extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_askdigest',
			'database' => 'z_askdigest',
		 ),
		 'slave' => array(
			'host' => 'dbserver_askdigest_read',
			'database' => 'z_askdigest',
		 ),
	);
}
