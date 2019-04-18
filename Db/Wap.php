<?php
/**
* Wap站
* @author wangml
* @copyright (c)
*/
class Db_Wap extends ZOL_Abstract_Pdo
{
    protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_wap',
			'database' => 'wap',
		 ),
		'slave' => array(
			'host' => 'dbserver_wap_read',
			'database' => 'wap',
		 ),
	);
}
