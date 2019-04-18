<?php
/** 
* @author dingxm
* @copyright (c) 2016-12-20
*/
class Db_MediaPlatDB extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_mediaplat',
			'database' => 'mediaplat',
		 ),
		 'slave' => array(
			'host' => 'dbserver_mediaplat_read',
			'database' => 'mediaplat',
		 ),
	    'charset'   => 'gbk',
	);	
}
