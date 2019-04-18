<?php
/**
* DIY攒机
* @author 仲伟涛 <zhong.weitao@zol.com.cn>
* @copyright (c)
*/
class Db_DiyRead extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_zj_read',
			'database' => 'z_zj',
		 ),
		'slave' => array(
			'host' => 'dbserver_zj_read',
			'database' => 'z_zj',
		 ),        
	);
}
